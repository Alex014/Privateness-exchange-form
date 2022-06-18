<?php
require __DIR__ . '/../lib/DB.php';
require __DIR__ . '/../lib/Ness.php';
require __DIR__ . '/../lib/Emercoin.php';

use lib\Ness;


ini_set('display_errors', true);
error_reporting(E_ERROR);

if (!file_exists(__DIR__ . '/../config/config.php')) {
    die ('config.php file does not exist');
}

$config = require __DIR__ . '/../config/config.php';

$ness1 = $config['ness']['v1'];
$ness2 = $config['ness']['v2'];

if ($argc >= 5) {
    $v2 = new Ness($ness2['host'], (int) $ness2['port'], $ness2['wallets'], $ness2['main_wallet_id'], $ness2['prefix']);
    
    $from = $argv[1];
    $to = $argv[2];
    $coins = (float) $argv[3];
    $hours = (float) $argv[4];

    echo "pay($from, $to, $coins, $hours)\n";

    $v2->pay($from, $to, $coins, $hours);
} else {
    echo "payment-test.php addr1 addr2 coins hours";
}
