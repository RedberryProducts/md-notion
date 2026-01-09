<?php

use Redberry\MdNotion\Adapters\BlockAdapterFactory;
use Redberry\MdNotion\Objects\Page;
use Redberry\MdNotion\SDK\Notion;
use Redberry\MdNotion\SDK\Requests\Actions\BlockChildren;
use Redberry\MdNotion\SDK\Requests\Actions\QueryDataSource;
use Redberry\MdNotion\SDK\Resource\Actions;
use Redberry\MdNotion\Services\BlockRegistry;
use Redberry\MdNotion\Services\DatabaseReader;
use Redberry\MdNotion\Services\DatabaseTable;
use Redberry\MdNotion\Services\PageReader;
use Saloon\Http\Response;

beforeEach(function () {
    // Set config default page size for tests
    config(['md-notion.default_page_size' => 100]);
});

test('page reader uses config default page size when no argument is provided', function () {
    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock page response
    $pageResponse = Mockery::mock(Response::class);
    $pageResponse->shouldReceive('json')->andReturn([
        'object' => 'page',
        'id' => 'test-page-id',
        'properties' => [
            'title' => [
                'type' => 'title',
                'title' => [['plain_text' => 'Test Page']],
            ],
        ],
    ]);

    $actions->shouldReceive('getPage')
        ->with('test-page-id')
        ->andReturn($pageResponse);

    // Mock block children response
    $blocksResponse = Mockery::mock(Response::class);
    $blocksResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect getBlockChildren to be called with config default (100)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 100)
        ->once()
        ->andReturn($blocksResponse);

    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $pageReader = new PageReader($notion, $registry);

    $page = $pageReader->read('test-page-id');

    expect($page)->toBeInstanceOf(Page::class);
});

test('page reader uses provided page size when argument is passed', function () {
    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock page response
    $pageResponse = Mockery::mock(Response::class);
    $pageResponse->shouldReceive('json')->andReturn([
        'object' => 'page',
        'id' => 'test-page-id',
        'properties' => [
            'title' => [
                'type' => 'title',
                'title' => [['plain_text' => 'Test Page']],
            ],
        ],
    ]);

    $actions->shouldReceive('getPage')
        ->with('test-page-id')
        ->andReturn($pageResponse);

    // Mock block children response
    $blocksResponse = Mockery::mock(Response::class);
    $blocksResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect getBlockChildren to be called with custom page size (50)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 50)
        ->once()
        ->andReturn($blocksResponse);

    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $pageReader = new PageReader($notion, $registry);

    $page = $pageReader->read('test-page-id', 50);

    expect($page)->toBeInstanceOf(Page::class);
});

test('page reader respects different config default page sizes', function () {
    // Change config to a different value
    config(['md-notion.default_page_size' => 25]);

    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock page response
    $pageResponse = Mockery::mock(Response::class);
    $pageResponse->shouldReceive('json')->andReturn([
        'object' => 'page',
        'id' => 'test-page-id',
        'properties' => [],
    ]);

    $actions->shouldReceive('getPage')
        ->with('test-page-id')
        ->andReturn($pageResponse);

    // Mock block children response
    $blocksResponse = Mockery::mock(Response::class);
    $blocksResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect getBlockChildren to be called with updated config default (25)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 25)
        ->once()
        ->andReturn($blocksResponse);

    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $pageReader = new PageReader($notion, $registry);

    $page = $pageReader->read('test-page-id');

    expect($page)->toBeInstanceOf(Page::class);
});

test('database reader uses config default page size when no argument is provided', function () {
    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock database response
    $databaseResponse = Mockery::mock(Response::class);
    $databaseResponse->shouldReceive('json')->andReturn([
        'object' => 'database',
        'id' => 'test-database-id',
        'title' => [['plain_text' => 'Test Database']],
        'data_sources' => [
            ['id' => 'data-source-1', 'name' => 'Main'],
        ],
    ]);

    $actions->shouldReceive('getDatabase')
        ->with('test-database-id')
        ->andReturn($databaseResponse);

    // Mock query data source response
    $queryResponse = Mockery::mock(Response::class);
    $queryResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect queryDataSource to be called with config default (100)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 100)
        ->once()
        ->andReturn($queryResponse);

    $databaseTable = new DatabaseTable($notion);
    $databaseReader = new DatabaseReader($notion, $databaseTable);

    $database = $databaseReader->read('test-database-id');

    expect($database->getId())->toBe('test-database-id');
});

test('database reader uses provided page size when argument is passed', function () {
    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock database response
    $databaseResponse = Mockery::mock(Response::class);
    $databaseResponse->shouldReceive('json')->andReturn([
        'object' => 'database',
        'id' => 'test-database-id',
        'title' => [['plain_text' => 'Test Database']],
        'data_sources' => [
            ['id' => 'data-source-1', 'name' => 'Main'],
        ],
    ]);

    $actions->shouldReceive('getDatabase')
        ->with('test-database-id')
        ->andReturn($databaseResponse);

    // Mock query data source response
    $queryResponse = Mockery::mock(Response::class);
    $queryResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect queryDataSource to be called with custom page size (30)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 30)
        ->once()
        ->andReturn($queryResponse);

    $databaseTable = new DatabaseTable($notion);
    $databaseReader = new DatabaseReader($notion, $databaseTable);

    $database = $databaseReader->read('test-database-id', 30);

    expect($database->getId())->toBe('test-database-id');
});

test('database reader respects different config default page sizes', function () {
    // Change config to a different value
    config(['md-notion.default_page_size' => 75]);

    $notion = Mockery::mock(Notion::class);
    $actions = Mockery::mock(Actions::class);

    $notion->shouldReceive('act')->andReturn($actions);

    // Mock database response
    $databaseResponse = Mockery::mock(Response::class);
    $databaseResponse->shouldReceive('json')->andReturn([
        'object' => 'database',
        'id' => 'test-database-id',
        'title' => [['plain_text' => 'Test Database']],
        'data_sources' => [
            ['id' => 'data-source-1', 'name' => 'Main'],
        ],
    ]);

    $actions->shouldReceive('getDatabase')
        ->with('test-database-id')
        ->andReturn($databaseResponse);

    // Mock query data source response
    $queryResponse = Mockery::mock(Response::class);
    $queryResponse->shouldReceive('json')->andReturn([
        'results' => [],
        'has_more' => false,
    ]);

    // Expect queryDataSource to be called with updated config default (75)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 75)
        ->once()
        ->andReturn($queryResponse);

    $databaseTable = new DatabaseTable($notion);
    $databaseReader = new DatabaseReader($notion, $databaseTable);

    $database = $databaseReader->read('test-database-id');

    expect($database->getId())->toBe('test-database-id');
});

test('block children request sends page_size as integer in query params', function () {
    $request = new BlockChildren('block-id', 50);

    expect($request->query()->all())->toBe(['page_size' => 50]);
    expect($request->query()->get('page_size'))->toBeInt();
});

test('query data source request sends page_size as integer in body', function () {
    $request = new QueryDataSource('data-source-id', null, 50);

    $reflection = new \ReflectionClass($request);
    $method = $reflection->getMethod('defaultBody');
    $method->setAccessible(true);

    $body = $method->invoke($request);

    expect($body)->toBe(['page_size' => 50]);
    expect($body['page_size'])->toBeInt();
});

test('query data source request excludes page_size when null', function () {
    $request = new QueryDataSource('data-source-id', null, null);

    $reflection = new \ReflectionClass($request);
    $method = $reflection->getMethod('defaultBody');
    $method->setAccessible(true);

    $body = $method->invoke($request);

    expect($body)->toBe([]);
});

afterEach(function () {
    Mockery::close();
});
