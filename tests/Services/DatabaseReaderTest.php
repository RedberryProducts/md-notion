<?php

use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\DatabaseTable;

test('database reader can be instantiated', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);

    $databaseReader = new DatabaseReader($notion, $databaseTable);

    expect($databaseReader)->toBeInstanceOf(DatabaseReader::class);
});

test('database reader converts query data to markdown', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);

    $databaseReader = new DatabaseReader($notion, $databaseTable);

    // Test the database table conversion method via reflection
    $reflection = new \ReflectionClass($databaseTable);
    $method = $reflection->getMethod('convertQueryToMarkdownTable');
    $method->setAccessible(true);

    $queryData = [
        'results' => [
            [
                'object' => 'page',
                'id' => 'item-1',
                'properties' => [
                    'Name' => ['type' => 'title', 'title' => [['plain_text' => 'Item 1']]],
                ],
            ],
        ],
    ];

    $result = $method->invoke($databaseTable, $queryData);

    expect($result)->toBeString();
    expect($result)->toContain('Name');
});

test('database reader handles empty database', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);

    $databaseReader = new DatabaseReader($notion, $databaseTable);

    // Test the database table conversion method with empty data
    $reflection = new \ReflectionClass($databaseTable);
    $method = $reflection->getMethod('convertQueryToMarkdownTable');
    $method->setAccessible(true);

    $queryData = ['results' => []];
    $result = $method->invoke($databaseTable, $queryData);

    expect($result)->toContain('Empty database');
});

afterEach(function () {
    Mockery::close();
});
