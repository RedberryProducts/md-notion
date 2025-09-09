<?php

use RedberryProducts\MdNotion\Services\DatabaseTable;
use RedberryProducts\MdNotion\SDK\Notion;

test('database table can be instantiated', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);
    
    expect($databaseTable)->toBeInstanceOf(DatabaseTable::class);
});

test('database table extracts rich text correctly', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);
    
    // Test the private extractRichText method via reflection
    $reflection = new \ReflectionClass($databaseTable);
    $method = $reflection->getMethod('extractRichText');
    $method->setAccessible(true);
    
    $richText = [
        ['plain_text' => 'Hello '],
        ['plain_text' => 'World']
    ];
    
    $result = $method->invoke($databaseTable, $richText);
    
    expect($result)->toBe('Hello World');
});

test('database table extracts property values correctly', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $databaseTable = new DatabaseTable($notion);
    
    // Test the private extractPropertyValue method via reflection
    $reflection = new \ReflectionClass($databaseTable);
    $method = $reflection->getMethod('extractPropertyValue');
    $method->setAccessible(true);
    
    // Test title property
    $titleProperty = [
        'title' => [
            ['plain_text' => 'Test Title']
        ]
    ];
    $result = $method->invoke($databaseTable, $titleProperty, 'title');
    expect($result)->toBe('Test Title');
    
    // Test URL property
    $urlProperty = ['url' => 'https://example.com'];
    $result = $method->invoke($databaseTable, $urlProperty, 'url');
    expect($result)->toBe('[Link](https://example.com)');
    
    // Test select property
    $selectProperty = ['select' => ['name' => 'Active']];
    $result = $method->invoke($databaseTable, $selectProperty, 'select');
    expect($result)->toBe('Active');
});

afterEach(function () {
    Mockery::close();
});