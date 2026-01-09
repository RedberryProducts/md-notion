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

    // Expect getBlockChildren to be called with config default (100)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 100)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

    // Expect getBlockChildren to be called with custom page size (50)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 50)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

    // Expect getBlockChildren to be called with updated config default (25)
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 25)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

    // Expect queryDataSource to be called with config default (100)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 100)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

    // Expect queryDataSource to be called with custom page size (30)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 30)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

    // Expect queryDataSource to be called with updated config default (75)
    $actions->shouldReceive('queryDataSource')
        ->with('data-source-1', null, 75)
        ->once()
        ->andReturn([
            'results' => [],
            'has_more' => false,
        ]);

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

// ============================================================================
// Pagination Tests for fetchPaginatedResults
// ============================================================================

test('pagination makes multiple API calls when pageSize exceeds 100', function () {
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

    // For pageSize 150, we expect two calls:
    // 1st call: page_size=100, returns 100 items with has_more=true
    // 2nd call: page_size=100 with cursor, returns 50 items with has_more=false
    $actions->shouldReceive('getBlockChildren')
        ->with('test-page-id', 150)
        ->once()
        ->andReturn([
            'results' => array_fill(0, 150, ['type' => 'paragraph', 'paragraph' => ['rich_text' => []]]),
            'has_more' => false,
            'next_cursor' => null,
        ]);

    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $pageReader = new PageReader($notion, $registry);

    $page = $pageReader->read('test-page-id', 150);

    expect($page)->toBeInstanceOf(Page::class);
});

test('getBlockChildren paginates and merges results when pageSize is 150', function () {
    $notion = new Notion('test-token', '2025-09-03');

    // Create mock responses for pagination
    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items with cursor
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns 60 items (more than needed)
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(101, 160)),
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 150);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(150);
    expect($result['results'][0]['id'])->toBe('block-1');
    expect($result['results'][99]['id'])->toBe('block-100');
    expect($result['results'][149]['id'])->toBe('block-150');
    // has_more should be true because we trimmed 10 extra results
    expect($result['has_more'])->toBeTrue();
});

test('getBlockChildren returns exactly 100 items without pagination when pageSize is 100', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 100);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(100);
    expect($result['has_more'])->toBeTrue();
    expect($result['next_cursor'])->toBe('cursor-abc');
});

test('getBlockChildren paginates correctly for exactly 101 items (edge case)', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns 1 item
        new \Saloon\Http\Faking\MockResponse([
            'results' => [['id' => 'block-101', 'type' => 'paragraph']],
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 101);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(101);
    expect($result['results'][100]['id'])->toBe('block-101');
    expect($result['has_more'])->toBeFalse();
});

test('getBlockChildren paginates correctly for exactly 200 items', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns exactly 100 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(101, 200)),
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 200);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(200);
    expect($result['results'][0]['id'])->toBe('block-1');
    expect($result['results'][199]['id'])->toBe('block-200');
    expect($result['has_more'])->toBeFalse();
});

test('getBlockChildren stops pagination when limit is reached even with more available', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items with has_more=true
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns 100 items (but we only need 20 more)
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(101, 200)),
            'has_more' => true,
            'next_cursor' => 'cursor-def',
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 120);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(120);
    // has_more should be true because API still has more AND we trimmed results
    expect($result['has_more'])->toBeTrue();
    // cursor should still be available for continuation
    expect($result['next_cursor'])->toBe('cursor-def');
});

test('getBlockChildren has_more is true when results were trimmed even if API has_more is false', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns 50 items, API says no more
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i", 'type' => 'paragraph'], range(101, 150)),
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    // Request 130 items, but we fetch 150 total (100 + 50)
    $result = $notion->act()->getBlockChildren('block-id', 130);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(130);
    // has_more should be true because we trimmed 20 results, even though API said no more
    expect($result['has_more'])->toBeTrue();
});

test('queryDataSource paginates and merges results correctly', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First request: returns 100 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "row-$i", 'properties' => []], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-abc',
        ]),
        // Second request: returns 50 items
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "row-$i", 'properties' => []], range(101, 150)),
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->queryDataSource('datasource-id', null, 150);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(150);
    expect($result['results'][0]['id'])->toBe('row-1');
    expect($result['results'][149]['id'])->toBe('row-150');
    expect($result['has_more'])->toBeFalse();
});

test('pagination handles three pages correctly', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new \Saloon\Http\Faking\MockClient([
        // First page
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i"], range(1, 100)),
            'has_more' => true,
            'next_cursor' => 'cursor-1',
        ]),
        // Second page
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i"], range(101, 200)),
            'has_more' => true,
            'next_cursor' => 'cursor-2',
        ]),
        // Third page
        new \Saloon\Http\Faking\MockResponse([
            'results' => array_map(fn ($i) => ['id' => "block-$i"], range(201, 250)),
            'has_more' => false,
            'next_cursor' => null,
        ]),
    ]);

    $notion->withMockClient($mockClient);

    $result = $notion->act()->getBlockChildren('block-id', 250);

    expect($result)->toBeArray();
    expect($result['results'])->toHaveCount(250);
    expect($result['results'][0]['id'])->toBe('block-1');
    expect($result['results'][100]['id'])->toBe('block-101');
    expect($result['results'][200]['id'])->toBe('block-201');
    expect($result['results'][249]['id'])->toBe('block-250');
    expect($result['has_more'])->toBeFalse();
});

afterEach(function () {
    Mockery::close();
});
