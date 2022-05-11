<?php

namespace lib;

class Parser {
    public static function parseToken(string $xml) 
    {
        $full_content = $xml;
        $xml = preg_replace("/<!--.+?-->/i", '', $xml);
        $xml = preg_replace('/”/i', '"', $xml);

        $xmlObject = simplexml_load_string($xml);
        if (null === $xmlObject) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'Wrong <WORM> format',
                'status' => 'ERROR'
            ];
        }
        
        if (null === $xmlObject->token || false === $xmlObject->token || null === $xmlObject->token['type']) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'Wrong <WORM> format',
                'status' => 'ERROR'
            ];
        }

        $type = $xmlObject->token['type'];

        if('ness-exchange-v1-v2' !== strtolower($type)) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'Wrong type (' . $type . ') must be "ness-exchange-v1-v2"',
                'status' => 'ERROR'
            ];
        }

        $address = (string) $xmlObject->token['address'];
        if('' === $address) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'No address field',
                'status' => 'ERROR'
            ];
        }

        $pay_address = (string) $xmlObject->token['pay_address'];
        if('' === $pay_address) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'No pay_address field',
                'status' => 'ERROR'
            ];
        }

        if((string) $xmlObject->token['address'] === (string) $xmlObject->token['pay_address']) {
            return [
                'content' => $full_content,
                'crc32' => crc32($full_content),
                'error' => 'Fields address and pay_address must be different',
                'status' => 'ERROR'
            ];
        }

        return [
            'content' => $full_content,
            'crc32' => crc32($full_content),
            'address' => $address,
            'pay_address' => $pay_address,
            'error' => '',
            'status' => 'CHECKED'
        ];
    }
}
