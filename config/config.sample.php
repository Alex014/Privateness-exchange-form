<?php

return [
    'ness' => [
        'v1' => [
            'host' => 'localhost',
            'port' => '6660',
            'wallet_id' => '',
            'password' => '',
            'prefix' => 'http://'
        ],
        'v2' => [
            'host' => 'localhost',
            'port' => '6660',
            'wallets' => [
                '2021_12_02_fdgh.wlt' => '123456789$',
                '2022_06_15_fada.wlt' => 'qwerty'
            ],
            'main_wallet_id' => '2021_12_02_fdgh.wlt',
            'prefix' => 'http://',
            'payment_address' => ''
        ]
    ],
    'emercoin' => [
        'host' => '127.0.0.1',
        'port' => '8332',
        'user' => '',
        'password' => ''
    ],
    'db' => [
        'host' => 'localhost',
        'user' => '',
        'password' => '',
        'database' => ''
    ],
    'exchange' => [
        'ratio' => 200000
    ]
];
