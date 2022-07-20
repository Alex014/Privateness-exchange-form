<?php
require '../lib/DB.php';

ini_set('display_errors', true);

use lib\DB;

$config = require '../config/config.php';
$db = new DB($config['db']['host'], $config['db']['database'], $config['db']['user'], $config['db']['password']);

if (empty($_GET['address']) || empty($_GET['pay_address'])) {
    echo json_encode([
        'status' => false
    ]);
    die();
}

$address = $_GET['address'];
$pay_address = $_GET['pay_address'];
$token = $db->find($address, $pay_address);

if (false === $token) {
    echo json_encode([
        'status' => false
    ]);
    die();
}

$token['recieve'] = $token['hours'] / $config['exchange']['ratio'];

echo json_encode([
    'status' => $token['status'],
    'error' => $token['error'],
    'address' => $token['address'],
    'pay_address' => $token['pay_address'],
    'gen_address' => $token['gen_address'],
    'hours' => $token['hours'],
    'ratio' => $config['exchange']['ratio'],
    'recieve' => $token['recieve']
]);