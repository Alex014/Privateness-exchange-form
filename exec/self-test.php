<?php
require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/Ness.php';
require __DIR__ . '/../lib/Emercoin.php';

use lib\DB;
use lib\Ness;
use lib\Emercoin;


ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!file_exists(__DIR__ . '/../config/config.php')) {
    die ('config.php file does not exist');
}

$config = require __DIR__ . '/../config/config.php';

$ness1 = $config['ness']['v1'];
$ness2 = $config['ness']['v2'];

$v1 = new Ness($ness1['host'], (int) $ness1['port'], $ness1['wallets'], $ness1['main_wallet_id'], $ness1['prefix']);
$v2 = new Ness($ness2['host'], (int) $ness2['port'], $ness2['wallets'], $ness2['main_wallet_id'], $ness2['prefix']);

if ($argc > 1) {
    if ('-a' === $argv[1]) {
        echo "\n" . $config['ness']['v2']['payment_address'] . "\n";
    } elseif ('-v' === $argv[1]) {
        print_r($config);
    } elseif ('-v1' === $argv[1]) {
        print_r($ness1);
    } elseif ('-v2' === $argv[1]) {
        print_r($ness2);
    } elseif ('-info1' === $argv[1]) {
        print_r($v1->health());
    } elseif ('-info2' === $argv[1]) {
        print_r($v2->health());
    } elseif ('-wallets' === $argv[1]) {
        print_r($v2->listWallets());
    } elseif ('-addresses' === $argv[1]) {
        if (empty($argv[2])) {
            echo "Empty wallet name\n";
            echo "Usage: self-test.php -addresses <wallet-name>\n";
            exit();
        } else {
            $wallet = $argv[2];
        }

        foreach ($v2->listAddresses($wallet) as $address => $info) {
            echo $address . " => " . $info['confirmed']['coins'] . " (" . $info['confirmed']['hours'] . ")" . "\n";
        }
    } elseif ('-emc' === $argv[1]) {
        print_r($config['emercoin']);
    } elseif ('-db' === $argv[1]) {
        print_r($config)['db'];
    } else {
        print_r($config);
    }

    die();
}

$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

Emercoin::$address = $config['emercoin']['host'];
Emercoin::$port = $config['emercoin']['port'];
Emercoin::$username = $config['emercoin']['user'];
Emercoin::$password = $config['emercoin']['password'];

if (is_array($db->findAll())) {
    echo "\nDB - OK\n";
} else {
    echo "\nDB - FAILED\n";
}

try {
    Emercoin::getinfo();
    Emercoin::name_filter("worm:token:ness_exchange_v1_v2:.+");
} catch (\Exception $err) {
    echo "\nEmercoin NVS - FAILED (" . $err->getMessage() . ")\n";
}

if (!isset($err)) {
    echo "\nEmercoin NVS - OK\n";
}

if ($v1->health()) {
    echo "\nNESS V1 - OK\n";
} else {
    echo "\nNESS V1 - FAILED\n";
}

if ($v2->checkAddress($ness2['payment_address'])) {
    echo "\nNESS V2 - OK\n";
} else {
    echo "\nNESS V2 - FAILED\n";
}

echo "
CMD PARAMS:
self-test.php -a        show payment address
self-test.php -v1       show Privateness network V1 CONFIG
self-test.php -v2       show Privateness network V2 CONFIG
self-test.php -info1    show Privateness network V1 INFORMATION
self-test.php -info2    show Privateness network V2 INFORMATION
self-test.php -wallets  list wallets from v2
self-test.php -addresses <wallet_id>    list addresses from v2 and wallet_id
self-test.php -emc      show EMERCOIN config
self-test.php -db       show database config
self-test.php -v        show all config
";