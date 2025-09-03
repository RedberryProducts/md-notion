<?php

require_once __DIR__.'/vendor/autoload.php';

use RedberryProducts\MdNotion\SDK\Notion;

$token = include __DIR__.'/notion-token.php';
$notion = new Notion($token, '2022-06-28');

$response = $notion->act()->getBlockChildren('263d9316605a806f9e95e1377a46ff3e', '100');

// Test run
print_r($response->json());
