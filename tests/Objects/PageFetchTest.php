<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\PageReader;

beforeEach(function () {
    $this->mockPageReader = Mockery::mock(PageReader::class);
    
    // Bind the mock to the container so app() can resolve it
    app()->instance(PageReader::class, $this->mockPageReader);
});

afterEach(function () {
    Mockery::close();
});

it('can fetch content using PageReader', function () {
    $pageId = 'test-page-id';

    // Create original page with minimal data
    $originalPage = Page::from([
        'id' => $pageId,
        'title' => 'Original Title',
    ]);

    // Create fetched page with full content
    $fetchedPage = Page::from([
        'id' => $pageId,
        'title' => 'Fetched Title',
        'content' => 'This is the fetched content',
        'has_children' => true,
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($pageId)
        ->once()
        ->andReturn($fetchedPage);

    // Fetch content
    $result = $originalPage->fetch();

    // Should return the same instance
    expect($result)->toBe($originalPage);

    // Should have updated content
    expect($originalPage->getTitle())->toBe('Fetched Title');
    expect($originalPage->getContent())->toBe('This is the fetched content');
    expect($originalPage->hasChildren())->toBeTrue();
});

it('preserves object identity after fetch', function () {
    $pageId = 'test-page-id';

    $originalPage = Page::from(['id' => $pageId]);
    $fetchedPage = Page::from([
        'id' => $pageId,
        'content' => 'New content',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($pageId)
        ->once()
        ->andReturn($fetchedPage);

    $beforeFetch = $originalPage;
    $afterFetch = $originalPage->fetch();

    expect($beforeFetch)->toBe($afterFetch);
    expect($originalPage->getContent())->toBe('New content');
});
