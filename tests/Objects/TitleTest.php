<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Objects\Database;

test('page object can handle rich text title format', function () {
    $data = [
        'id' => 'page-123',
        'title' => [
            [
                'type' => 'text',
                'text' => [
                    'content' => 'Test database',
                    'link' => null
                ],
                'annotations' => [
                    'bold' => false,
                    'italic' => false,
                    'strikethrough' => false,
                    'underline' => false,
                    'code' => false,
                    'color' => 'default'
                ],
                'plain_text' => 'Test database',
                'href' => null
            ]
        ]
    ];

    $page = Page::from($data);

    expect($page->getTitle())->toBe('Test database');
    expect($page->toArray()['title'])->toBe('Test database');
});

test('page object can handle child page title format', function () {
    $data = [
        'id' => 'page-456',
        'child_page' => [
            'title' => 'Advanced To-Do'
        ]
    ];

    $page = Page::from($data);

    expect($page->getTitle())->toBe('Advanced To-Do');
    expect($page->toArray()['title'])->toBe('Advanced To-Do');
});

test('database object can handle child database title format', function () {
    $data = [
        'id' => 'db-789',
        'child_database' => [
            'title' => 'Test database'
        ]
    ];

    $database = Database::from($data);

    expect($database->getTitle())->toBe('Test database');
    expect($database->toArray()['title'])->toBe('Test database');
});

test('page object can handle simple string title', function () {
    $data = [
        'id' => 'page-simple',
        'title' => 'Simple Title'
    ];

    $page = Page::from($data);

    expect($page->getTitle())->toBe('Simple Title');
    expect($page->toArray()['title'])->toBe('Simple Title');
});

test('title processing handles multiple rich text blocks', function () {
    $data = [
        'id' => 'page-multi',
        'title' => [
            [
                'type' => 'text',
                'text' => [
                    'content' => 'Multi ',
                    'link' => null
                ],
                'annotations' => [
                    'bold' => false,
                    'italic' => false,
                    'strikethrough' => false,
                    'underline' => false,
                    'code' => false,
                    'color' => 'default'
                ],
                'plain_text' => 'Multi ',
                'href' => null
            ],
            [
                'type' => 'text',
                'text' => [
                    'content' => 'Part Title',
                    'link' => null
                ],
                'annotations' => [
                    'bold' => true,
                    'italic' => false,
                    'strikethrough' => false,
                    'underline' => false,
                    'code' => false,
                    'color' => 'default'
                ],
                'plain_text' => 'Part Title',
                'href' => null
            ]
        ]
    ];

    $page = Page::from($data);

    expect($page->getTitle())->toBe('Multi Part Title');
});

test('title processing handles empty or missing title gracefully', function () {
    $data = [
        'id' => 'page-empty'
    ];

    $page = Page::from($data);

    expect($page->getTitle())->toBe('');
});

test('title processing prefers existing title when data is empty', function () {
    $page = new Page();
    $page->setTitle('Existing Title');
    
    $page->fill([
        'id' => 'page-preserve'
    ]);

    expect($page->getTitle())->toBe('Existing Title');
});
