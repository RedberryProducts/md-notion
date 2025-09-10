<?php

use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\PageReader;

test('page object can use page reader methods', function () {
    $page = new Page(['id' => 'test-page-id']);
    $pageReader = Mockery::mock(PageReader::class);
    
    // Bind the mock to the container so app() can resolve it
    app()->instance(PageReader::class, $pageReader);

    // Mock page reader methods
    $pageReader->shouldReceive('read')
        ->with('child-1')
        ->andReturn(new Page(['id' => 'child-1', 'content' => 'Child content 1']));

    $pageReader->shouldReceive('read')
        ->with('child-2')
        ->andReturn(new Page(['id' => 'child-2', 'content' => 'Child content 2']));

    // Set up child pages first
    $page->setChildPages(collect([
        new Page(['id' => 'child-1']),
        new Page(['id' => 'child-2']),
    ]));

    // Test the readChildPagesContent method
    $page->readChildPagesContent();
    expect($page->getChildPages())->toHaveCount(2);
    expect($page->getChildPages()->first()->getContent())->toBe('Child content 1');
});

test('page object can recursively read all nested child pages content', function () {
    $page = new Page(['id' => 'root-page']);
    $pageReader = Mockery::mock(PageReader::class);
    
    // Bind the mock to the container so app() can resolve it
    app()->instance(PageReader::class, $pageReader);

    // Create nested page structure
    $childPage1 = new Page(['id' => 'child-1']);
    $childPage2 = new Page(['id' => 'child-2']);
    $grandChildPage = new Page(['id' => 'grandchild-1']);

    // Mock PageReader to return pages with content and nested children
    $pageReader->shouldReceive('read')
        ->with('child-1')
        ->andReturn(new Page([
            'id' => 'child-1',
            'content' => 'Child 1 content',
            'childPages' => collect([$grandChildPage]),
        ]));

    $pageReader->shouldReceive('read')
        ->with('child-2')
        ->andReturn(new Page(['id' => 'child-2', 'content' => 'Child 2 content']));

    $pageReader->shouldReceive('read')
        ->with('grandchild-1')
        ->andReturn(new Page(['id' => 'grandchild-1', 'content' => 'Grandchild content']));

    // Set up the initial child pages
    $page->setChildPages(collect([$childPage1, $childPage2]));

    // Test the readAllPagesContent method (recursive)
    $page->readAllPagesContent();

    // Verify the structure
    expect($page->getChildPages())->toHaveCount(2);
    expect($page->getChildPages()->first()->getContent())->toBe('Child 1 content');
    expect($page->getChildPages()->last()->getContent())->toBe('Child 2 content');

    // Verify nested child was also read
    $firstChild = $page->getChildPages()->first();
    expect($firstChild->hasChildPages())->toBeTrue();
    expect($firstChild->getChildPages())->toHaveCount(1);
    expect($firstChild->getChildPages()->first()->getContent())->toBe('Grandchild content');
});

test('page object can read child databases content', function () {
    $page = new Page(['id' => 'test-page-id']);
    $databaseReader = Mockery::mock(DatabaseReader::class);
    
    // Bind the mock to the container so app() can resolve it
    app()->instance(DatabaseReader::class, $databaseReader);

    // Mock database reader methods
    $databaseReader->shouldReceive('read')
        ->with('db-1')
        ->andReturn(new Database(['id' => 'db-1', 'tableContent' => '| Name | Status |\n| --- | --- |']));

    $databaseReader->shouldReceive('read')
        ->with('db-2')
        ->andReturn(new Database(['id' => 'db-2', 'tableContent' => '| Title | Category |\n| --- | --- |']));

    // Set up child databases first
    $page->setChildDatabases(collect([
        new Database(['id' => 'db-1']),
        new Database(['id' => 'db-2']),
    ]));

    // Test the readChildDatabasesContent method
    $page->readChildDatabasesContent();
    expect($page->getChildDatabases())->toHaveCount(2);
    expect($page->getChildDatabases()->first()->getTableContent())->toContain('| Name | Status |');
    expect($page->getChildDatabases()->last()->getTableContent())->toContain('| Title | Category |');
});

test('database object can use database reader and page reader methods', function () {
    $database = new Database(['id' => 'test-db-id']);
    $pageReader = Mockery::mock(PageReader::class);
    
    // Bind the mock to the container so app() can resolve it
    app()->instance(PageReader::class, $pageReader);

    // Mock page reader methods for database items
    $pageReader->shouldReceive('read')
        ->with('item-1')
        ->andReturn(new Page(['id' => 'item-1', 'content' => 'Item 1 content']));

    $pageReader->shouldReceive('read')
        ->with('item-2')
        ->andReturn(new Page(['id' => 'item-2', 'content' => 'Item 2 content']));

    // Set up child pages (database items) first
    $database->setChildPages(collect([
        new Page(['id' => 'item-1']),
        new Page(['id' => 'item-2']),
    ]));

    // Test the readItemsContent method
    $database->readItemsContent();
    expect($database->getChildPages())->toHaveCount(2);
    expect($database->getChildPages()->first()->getContent())->toBe('Item 1 content');
});

test('page object serializes correctly with child content', function () {
    $page = new Page([
        'id' => 'test-page',
        'title' => [['plain_text' => 'Test Page']],
        'content' => 'Page content',
        'has_children' => true,
    ]);

    $page->setChildPages(collect([
        new Page(['id' => 'child-1']),
    ]));

    $page->setChildDatabases(collect([
        new Database(['id' => 'db-1']),
    ]));

    $array = $page->toArray();

    expect($array['id'])->toBe('test-page');
    expect($array['content'])->toBe('Page content');
    expect($array['has_children'])->toBe(true);
    expect($array['childPages'])->toHaveCount(1);
    expect($array['childDatabases'])->toHaveCount(1);
});

test('database object serializes correctly with table content and child pages', function () {
    $database = new Database([
        'id' => 'test-db',
        'title' => [['plain_text' => 'Test Database']],
    ]);

    $database->setTableContent('| Name | Status |\n| --- | --- |');
    $database->setChildPages(collect([
        new Page(['id' => 'item-1']),
    ]));

    $array = $database->toArray();

    expect($array['id'])->toBe('test-db');
    expect($array['tableContent'])->toContain('| Name | Status |');
    expect($array['childPages'])->toHaveCount(1);
});

afterEach(function () {
    Mockery::close();
});
