<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Services\ContentManager;
use RedberryProducts\MdNotion\Services\DatabaseTable;

test('page object can use content manager methods', function () {
    $page = new Page(['id' => 'test-page-id']);
    $contentManager = Mockery::mock(ContentManager::class);
    
    // Mock content manager methods
    $contentManager->shouldReceive('fetchChildPages')
        ->with('test-page-id')
        ->andReturn(collect([
            new Page(['id' => 'child-1']),
            new Page(['id' => 'child-2'])
        ]));
    
    $contentManager->shouldReceive('fetchChildDatabases')
        ->with('test-page-id')
        ->andReturn(collect([
            new Database(['id' => 'db-1'])
        ]));
    
    $contentManager->shouldReceive('fetchPageContent')
        ->with('test-page-id')
        ->andReturn(new Page(['id' => 'test-page-id', 'content' => 'Test content']));
    
    // Test the methods
    $page->fetchChildPages($contentManager);
    expect($page->getChildPages())->toHaveCount(2);
    
    $page->fetchChildDatabases($contentManager);
    expect($page->getChildDatabases())->toHaveCount(1);
    
    $page->fetchContent($contentManager);
    expect($page->getContent())->toBe('Test content');
});

test('database object can use database table methods', function () {
    $database = new Database(['id' => 'test-db-id']);
    $databaseTable = Mockery::mock(DatabaseTable::class);
    
    // Mock database table methods
    $databaseTable->shouldReceive('fetchDatabaseAsMarkdownTable')
        ->with('test-db-id')
        ->andReturn('| Name | Status |\n| --- | --- |\n| Item 1 | Active |');
    
    $databaseTable->shouldReceive('fetchDatabaseItems')
        ->with('test-db-id')
        ->andReturn(collect([
            new Page(['id' => 'item-1']),
            new Page(['id' => 'item-2'])
        ]));
    
    // Test the methods
    $database->fetchAsTable($databaseTable);
    expect($database->getTableContent())->toContain('| Name | Status |');
    
    $database->fetchItems($databaseTable);
    expect($database->getItems())->toHaveCount(2);
});

test('page object serializes correctly with child content', function () {
    $page = new Page([
        'id' => 'test-page',
        'title' => [['plain_text' => 'Test Page']],
        'content' => 'Page content',
        'has_children' => true
    ]);
    
    $page->setChildPages(collect([
        new Page(['id' => 'child-1'])
    ]));
    
    $page->setChildDatabases(collect([
        new Database(['id' => 'db-1'])
    ]));
    
    $array = $page->toArray();
    
    expect($array['id'])->toBe('test-page');
    expect($array['content'])->toBe('Page content');
    expect($array['has_children'])->toBe(true);
    expect($array['childPages'])->toHaveCount(1);
    expect($array['childDatabases'])->toHaveCount(1);
});

test('database object serializes correctly with items and table content', function () {
    $database = new Database([
        'id' => 'test-db',
        'title' => [['plain_text' => 'Test Database']]
    ]);
    
    $database->setTableContent('| Name | Status |\n| --- | --- |');
    $database->setItems(collect([
        new Page(['id' => 'item-1'])
    ]));
    
    $array = $database->toArray();
    
    expect($array['id'])->toBe('test-db');
    expect($array['tableContent'])->toContain('| Name | Status |');
    expect($array['items'])->toHaveCount(1);
});

afterEach(function () {
    Mockery::close();
});