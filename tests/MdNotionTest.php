<?php

use RedberryProducts\MdNotion\MdNotion;
use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\PageReader;

beforeEach(function () {
    // Mock the PageReader
    $this->mockPageReader = Mockery::mock(PageReader::class);

    // Mock the DatabaseReader
    $this->mockDatabaseReader = Mockery::mock(DatabaseReader::class);

    // Bind mocks to container
    $this->app->instance(PageReader::class, $this->mockPageReader);
    $this->app->instance(DatabaseReader::class, $this->mockDatabaseReader);

    $this->pageId = '263d9316605a806f9e95e1377a46ff3e';
});

afterEach(function () {
    Mockery::close();
});

it('can be created using make method', function () {
    $mdNotion = MdNotion::make($this->pageId);

    expect($mdNotion)->toBeInstanceOf(MdNotion::class);
});

it('can fetch child pages', function () {
    $childPage1 = Page::from(['id' => 'child1', 'title' => 'Child Page 1']);
    $childPage2 = Page::from(['id' => 'child2', 'title' => 'Child Page 2']);

    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
    ]);
    $mainPage->setChildPages(collect([$childPage1, $childPage2]));

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $mdNotion = MdNotion::make($this->pageId);
    $pages = $mdNotion->pages();

    expect($pages)->toHaveCount(2);
    expect($pages->first()->getId())->toBe('child1');
    expect($pages->last()->getId())->toBe('child2');
});

it('can fetch child databases', function () {
    $childDb1 = Database::from(['id' => 'db1', 'title' => 'Database 1']);
    $childDb2 = Database::from(['id' => 'db2', 'title' => 'Database 2']);

    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
    ]);
    $mainPage->setChildDatabases(collect([$childDb1, $childDb2]));

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $mdNotion = MdNotion::make($this->pageId);
    $databases = $mdNotion->databases();

    expect($databases)->toHaveCount(2);
    expect($databases->first()->getId())->toBe('db1');
    expect($databases->last()->getId())->toBe('db2');
});

it('can get content as page object', function () {
    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => '# Hello World',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $mdNotion = MdNotion::make($this->pageId);
    $page = $mdNotion->content()->get();

    expect($page)->toBeInstanceOf(Page::class);
    expect($page->getId())->toBe($this->pageId);
    expect($page->getTitle())->toBe('Main Page');
    expect($page->getContent())->toBe('# Hello World');
});

it('can get content as markdown string', function () {
    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => 'This is the main content.',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $mdNotion = MdNotion::make($this->pageId);
    $markdown = $mdNotion->content()->read();

    expect($markdown)->toContain('# Main Page');
    expect($markdown)->toContain('This is the main content.');
});

it('can get content with child pages', function () {
    $childPage = Page::from([
        'id' => 'child1',
        'title' => 'Child Page',
        'content' => 'Child content',
    ]);

    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => 'Main content',
    ]);
    $mainPage->setChildPages(collect([$childPage]));

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with('child1')
        ->once()
        ->andReturn($childPage);

    $mdNotion = MdNotion::make($this->pageId);
    $page = $mdNotion->content()->withPages()->get();

    expect($page->hasChildPages())->toBeTrue();
    expect($page->getChildPages()->first()->getContent())->toBe('Child content');
});

it('can get content with child databases', function () {
    $childDb = Database::from([
        'id' => 'db1',
        'title' => 'Database 1',
        'tableContent' => '| Column 1 | Column 2 |\n|----------|----------|\n| Value 1  | Value 2  |',
    ]);

    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => 'Main content',
    ]);
    $mainPage->setChildDatabases(collect([$childDb]));

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $this->mockDatabaseReader
        ->shouldReceive('read')
        ->with('db1')
        ->once()
        ->andReturn($childDb);

    $mdNotion = MdNotion::make($this->pageId);
    $page = $mdNotion->content()->withDatabases()->get();

    expect($page->hasChildDatabases())->toBeTrue();
    expect($page->getChildDatabases()->first()->hasTableContent())->toBeTrue();
});

it('can get full content recursively', function () {
    $grandChildPage = Page::from([
        'id' => 'grandchild1',
        'title' => 'Grandchild Page',
        'content' => 'Grandchild content',
    ]);

    $childPage = Page::from([
        'id' => 'child1',
        'title' => 'Child Page',
        'content' => 'Child content',
    ]);
    $childPage->setChildPages(collect([$grandChildPage]));

    $childDb = Database::from([
        'id' => 'db1',
        'title' => 'Database 1',
        'tableContent' => '| Col1 | Col2 |\n|------|------|\n| A    | B    |',
    ]);

    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => 'Main content',
    ]);
    $mainPage->setChildPages(collect([$childPage]));
    $mainPage->setChildDatabases(collect([$childDb]));

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $this->mockDatabaseReader
        ->shouldReceive('read')
        ->with('db1')
        ->once()
        ->andReturn($childDb);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with('child1')
        ->once()
        ->andReturn($childPage);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with('grandchild1')
        ->once()
        ->andReturn($grandChildPage);

    $mdNotion = MdNotion::make($this->pageId);
    $fullMarkdown = $mdNotion->full();

    expect($fullMarkdown)->toContain('# Main Page');
    expect($fullMarkdown)->toContain('Main content');
    expect($fullMarkdown)->toContain('## Database 1');
    expect($fullMarkdown)->toContain('| Col1 | Col2 |');
    expect($fullMarkdown)->toContain('## Child Page');
    expect($fullMarkdown)->toContain('Child content');
    expect($fullMarkdown)->toContain('### Grandchild Page');
    expect($fullMarkdown)->toContain('Grandchild content');
});

it('supports method chaining for content builder', function () {
    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Main Page',
        'content' => 'Main content',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $mdNotion = MdNotion::make($this->pageId);

    // Test method chaining
    $result = $mdNotion->content()->withPages()->withDatabases()->read();

    expect($result)->toBeString();
    expect($result)->toContain('# Main Page');
});
