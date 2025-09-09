<?php

it('can render page-md blade template', function () {
    $page = \RedberryProducts\MdNotion\Objects\Page::from([
        'id' => 'test-id',
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $rendered = view('md-notion::page-md', [
        'page' => $page,
        'withDatabases' => false,
        'withPages' => false,
    ])->render();

    expect($rendered)->toContain('# Test Page');
    expect($rendered)->toContain('Test content');
});

it('can render full-md blade template', function () {
    $page = \RedberryProducts\MdNotion\Objects\Page::from([
        'id' => 'test-id',
        'title' => 'Test Page',
        'content' => 'Test content',
    ]);

    $rendered = view('md-notion::full-md', [
        'page' => $page,
    ])->render();

    expect($rendered)->toContain('# Test Page');
    expect($rendered)->toContain('Test content');
});

it('can access template configuration from config', function () {
    $pageTemplate = config('md-notion.templates.page_markdown');
    $fullTemplate = config('md-notion.templates.full_markdown');

    expect($pageTemplate)->toBe('md-notion::page-md');
    expect($fullTemplate)->toBe('md-notion::full-md');
});
