<?php
namespace lib;

require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/Ness.php';
require __DIR__ . '/../lib/Emercoin.php';
require __DIR__ . '/../lib/Parser.php';

use lib\DB;
use lib\Ness;
use lib\Emercoin;
use lib\Parser;

class Script {
    private $v1;
    private $v2;
    private $db;
    private $config;

    public $pregenerated_addresses_strategy = true;

    public function __construct() 
    {
        $this->config = require __DIR__ . '/../config/config.php';

        $ness1 = $this->config['ness']['v1'];
        $ness2 = $this->config['ness']['v2'];

        $this->db = new DB($this->config['db']['host'], $this->config['db']['database'], $this->config['db']['user'], $this->config['db']['password']);
        $this->v1 = new Ness($ness1['host'], (int) $ness1['port'], $ness1['wallets'], $ness1['main_wallet_id'], $ness1['prefix']);
        $this->v2 = new Ness($ness2['host'], (int) $ness2['port'], $ness2['wallets'], $ness2['main_wallet_id'], $ness2['prefix']);
    }

    public function parseTokens()
    {
        $this->synchronize();
        echo "\nsynchronize - OK\n\n";
        $this->process();
        echo "\nprocess - OK\n";
    }

    private function selectTokensNVS()
    {
        Emercoin::$address = $this->config['emercoin']['host'];
        Emercoin::$port = $this->config['emercoin']['port'];
        Emercoin::$username = $this->config['emercoin']['user'];
        Emercoin::$password = $this->config['emercoin']['password'];
        $tokens = Emercoin::name_filter("worm:token:ness_exchange_v1_v2:.+");

        $tokens = array_map(function ($token) {
            $token['crc32'] = crc32($token['value']);
            $name = explode(':', $token['name']);
            $token['address'] = $name[count($name) - 2];
            $token['pay_address'] = $name[count($name) - 1];
            return $token;
        },
        $tokens);

        return $tokens;
    }

    private function selectTokensDB()
    {
        $result = [];
        $tokens = $this->db->findAll();
        foreach ($tokens as $token) {
            $result[$token['address']] = $token;
        }

        return $result;
    }

    private function synchronize()
    {
        $tokens_nvs = $this->selectTokensNVS();

        foreach ($tokens_nvs as $token) {
            $element = $this->db->find($token['address'], $token['pay_address']);

            if (!empty($element)) {
                if ('PAYED' !== $element['status'] && 'ACTIVATED' !== $element['status']) {
                    if ($element['crc32'] != $token['crc32']) {
                        $this->db->update($token['address'], $token['pay_address'], $this->parseToken($token));
                    }
                }
            } else {
                $this->db->add($this->parseToken($token));
            }
        }
    }

    private function process()
    {
        $tokens_db = $this->selectTokensDB();
        foreach ($tokens_db as $token) {
            $this->processToken($token);
        }
    }

    private function parseToken(array $token)
    {
        $result = Parser::parseToken($token['value'], $token['address'], $token['pay_address']);
        $result['address'] = $token['address'];

        return $result;
    }

    private function processToken(array $token)
    {
        if ('CHECKED' === $token['status']) {
            $this->activateToken($token);
        } elseif ('ACTIVATED' === $token['status']) {
            $this->payToken($token);
        }
    }

    private function checkAddress(string $address)
    {
        return $this->v1->checkAddress($address);
    }

    private function checkPaymentAddress(string $address)
    {
        return $this->v2->checkAddress($address);
    }

    private function checkGeneratedAddress(array $token)
    {
        if ($this->pregenerated_addresses_strategy) {
            return $this->v2->checkAllRecieved($token['address'], $token['gen_address']);
        } else {
            return $this->v2->checkLastRecieved($token['address'], $token['gen_address']);
        }
    }

    private function activateToken(array $token)
    {
        if (!$this->checkAddress($token['address'])) {
            $this->db->updateError($token['address'], $token['pay_address'], "Address $token[address] does not exist");
            return false;
        }

        if (!$this->checkPaymentAddress($token['pay_address'])) {
            $this->db->updateError($token['address'], $token['pay_address'], "Payment address $token[pay_address] does not exist");
            return false;
        }

        $address = $token['address'];
        $addr_data = $this->v1->getAddress($address);
        $hours = $addr_data['addresses'][$address]['confirmed']['hours'];

        if (empty($hours)) {
            $this->db->updateError($token['address'], $token['pay_address'], "Internal error");
            return false;
        }

        echo "Token " . $token['address'] . "-" . $token['pay_address'] . " ";

        if ($this->pregenerated_addresses_strategy) {
            $gen_address = $this->v2->findEmptyAddress();
        } else {
            $gen_address = $this->v2->createAddr();
        }

        echo " ... generated address $gen_address\n";

        $this->db->activate($token['address'], $token['pay_address'], $gen_address, $hours);
    }

    private function payToken(array $token)
    {
        if ($this->checkGeneratedAddress($token)) {
            $address = $token['address'];
            $pay_address = $token['pay_address'];
            $addr_data = $this->v1->getAddress($address);
            $hours = $addr_data['addresses'][$address]['confirmed']['hours'];
            $coins = $hours  / $this->config['exchange']['ratio'];
            // var_dump($this->config['ness']['v2']['payment_address'], $token['pay_address'], $coins);
            echo "From " . $this->config['ness']['v2']['payment_address'] . " to " . $token['pay_address'];

            if ($this->v2->pay($this->config['ness']['v2']['payment_address'], $token['pay_address'], $coins, 1)) {
                sleep(5);
                $this->db->pay($address, $pay_address , $hours);
                echo " payed $coins NESS\n";
            }
        }
    }
}