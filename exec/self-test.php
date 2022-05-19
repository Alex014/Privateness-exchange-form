<?php
require '../lib/DB.php';
require '../lib/Ness.php';
require '../lib/Emercoin.php';

use lib\DB;
use lib\Ness;
use lib\Emercoin;


ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!file_exists(__DIR__ . '/../config/config.php')) {
    die ('config.php file does not exist');
}

$config = require '../config/config.php';

if ($argc > 1) {
    if ('-a' === $argv[1]) {
        echo "\n" . $config['ness']['v2']['payment_address'] . "\n";
    } elseif ('-v' === $argv[1]) {
        print_r($config);
    } elseif ('-v1' === $argv[1]) {
        print_r($config['ness']['v1']);
    } elseif ('-v2' === $argv[1]) {
        print_r($config['ness']['v2']);
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

$ness1 = $config['ness']['v1'];
$ness2 = $config['ness']['v2'];

$v1 = new Ness($ness1['host'], (int) $ness1['port'], $ness1['wallet_id'], $ness1['password']);
$v2 = new Ness($ness2['host'], (int) $ness2['port'], $ness2['wallet_id'], $ness2['password']);

if (is_array($db->findAll())) {
    echo "\nDB - OK\n";
} else {
    echo "\nDB - FAILED\n";
}

try {
    Emercoin::getinfo();
} catch (\Exception $err) {
    echo "\nEmercoin NVS - FAILED\n";
}

if (!isset($err)) {
    echo "\nEmercoin NVS - OK\n";
}

if ($v2->health()) {
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
self-test.php -a    show payment address
self-test.php -v1   show Privateness network V1 config
self-test.php -v2   show Privateness network V2 config
self-test.php -emc  show EMERCOIN config
self-test.php -db   show database config
self-test.php -v    show all config
";