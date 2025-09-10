<?php

use RedberryProducts\MdNotion\Facades\MdNotion;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\PageReader;

beforeEach(function () {
    $this->mockPageReader = Mockery::mock(PageReader::class);
    $this->app->instance(PageReader::class, $this->mockPageReader);
});

afterEach(function () {
    Mockery::close();
});

it('can use MdNotion facade', function () {
    $pageId = 'test-page-id';

    $mainPage = Page::from([
        'id' => $pageId,
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $this->mockPageReader
        ->shouldReceive('read')
        ->with($pageId)
        ->once()
        ->andReturn($mainPage);

    // Test facade usage
    $mdNotion = MdNotion::make($pageId);
    $content = $mdNotion->content()->read();

    expect($content)->toContain('# Test Page');
    expect($content)->toContain('Test content');
});

it('facade returns correct instance type', function () {
    $pageId = 'test-page-id';

    $mdNotion = MdNotion::make($pageId);

    expect($mdNotion)->toBeInstanceOf(\RedberryProducts\MdNotion\MdNotion::class);
});
