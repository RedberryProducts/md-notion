<?php

use Redberry\MdNotion\ContentBuilder;
use Redberry\MdNotion\Objects\Page;
use Redberry\MdNotion\Services\DatabaseReader;
use Redberry\MdNotion\Services\PageReader;

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

it('validates pageId in get() method', function () {
    $contentBuilder = new ContentBuilder('', $this->mockPageReader, $this->mockDatabaseReader);

    expect(fn () => $contentBuilder->get())
        ->toThrow(InvalidArgumentException::class, 'Page ID must be set');
});

it('validates pageId in read() method', function () {
    $contentBuilder = new ContentBuilder('', $this->mockPageReader, $this->mockDatabaseReader);

    expect(fn () => $contentBuilder->read())
        ->toThrow(InvalidArgumentException::class, 'Page ID must be set');
});

it('can use withPages and withDatabases before calling read', function () {
    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $contentBuilder = new ContentBuilder($this->pageId, $this->mockPageReader, $this->mockDatabaseReader);
    $markdown = $contentBuilder->withPages()->withDatabases()->read();

    expect($markdown)->toContain('# Test Page');
    expect($markdown)->toContain('Test content');
});

it('passes correct variables to blade template in read method', function () {
    $mainPage = Page::from([
        'id' => $this->pageId,
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($this->pageId)
        ->once()
        ->andReturn($mainPage);

    $contentBuilder = new ContentBuilder($this->pageId, $this->mockPageReader, $this->mockDatabaseReader);
    $contentBuilder->withPages()->withDatabases();

    $markdown = $contentBuilder->read();

    // Verify that the template has access to the variables we pass
    expect($markdown)->toContain('# Test Page');
});
