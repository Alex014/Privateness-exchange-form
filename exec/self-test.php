<?php
require '../lib/DB.php';
require '../lib/Ness.php';
require '../lib/Emercoin.php';

use lib\DB;
use lib\Ness;
use lib\Emercoin;


ini_set('display_errors', true);
error_reporting(E_ERROR);

$config = require '../config/config.php';

$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

Emercoin::$address = $config['emercoin']['host'];
Emercoin::$port = $config['emercoin']['port'];
Emercoin::$username = $config['emercoin']['user'];
Emercoin::$password = $config['emercoin']['password'];

$ness1 = $config['ness']['v2'];
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