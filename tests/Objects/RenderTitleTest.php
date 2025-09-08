<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Objects\Database;

test('renderTitle generates markdown heading with level 1', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title'
    ]);

    $result = $page->renderTitle(1);

    expect($result)->toBe('# My Page Title');
});

test('renderTitle generates markdown heading with level 2', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title'
    ]);

    $result = $page->renderTitle(2);

    expect($result)->toBe('## My Page Title');
});

test('renderTitle generates markdown heading with level 3', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title'
    ]);

    $result = $page->renderTitle(3);

    expect($result)->toBe('### My Page Title');
});

test('renderTitle includes emoji icon when present', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title',
        'icon' => [
            'type' => 'emoji',
            'emoji' => '📄'
        ]
    ]);

    $result = $page->renderTitle(1);

    expect($result)->toBe('# 📄 My Page Title');
});

test('renderTitle includes external icon when present', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title',
        'icon' => [
            'type' => 'external',
            'external' => [
                'url' => 'https://example.com/icons/document_icon.svg'
            ]
        ]
    ]);

    $result = $page->renderTitle(2);

    expect($result)->toBe('## [Document](https://example.com/icons/document_icon.svg) My Page Title');
});

test('renderTitle includes file icon when present', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title',
        'icon' => [
            'type' => 'file',
            'file' => [
                'url' => 'https://s3.amazonaws.com/example/icon.png',
                'expiry_time' => '2024-01-01T00:00:00Z'
            ]
        ]
    ]);

    $result = $page->renderTitle(3);

    expect($result)->toBe('### [🔗](https://s3.amazonaws.com/example/icon.png) My Page Title');
});

test('renderTitle works with database objects', function () {
    $database = new Database([
        'id' => 'db-456',
        'title' => 'My Database',
        'icon' => [
            'type' => 'emoji',
            'emoji' => '🗂️'
        ]
    ]);

    $result = $database->renderTitle(1);

    expect($result)->toBe('# 🗂️ My Database');
});

test('renderTitle handles complex title formats', function () {
    $page = new Page([
        'id' => 'page-complex',
        'title' => [
            [
                'type' => 'text',
                'text' => [
                    'content' => 'Complex Title',
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
                'plain_text' => 'Complex Title',
                'href' => null
            ]
        ],
        'icon' => [
            'type' => 'emoji',
            'emoji' => '⭐'
        ]
    ]);

    $result = $page->renderTitle(2);

    expect($result)->toBe('## ⭐ Complex Title');
});

test('renderTitle throws exception for invalid level', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'My Page Title'
    ]);

    expect(fn() => $page->renderTitle(0))->toThrow(\InvalidArgumentException::class);
    expect(fn() => $page->renderTitle(4))->toThrow(\InvalidArgumentException::class);
    expect(fn() => $page->renderTitle(-1))->toThrow(\InvalidArgumentException::class);
});

test('renderTitle defaults to level 1 when no parameter provided', function () {
    $page = new Page([
        'id' => 'page-123',
        'title' => 'Default Level Title'
    ]);

    $result = $page->renderTitle();

    expect($result)->toBe('# Default Level Title');
});

test('renderTitle handles empty title gracefully', function () {
    $page = new Page([
        'id' => 'page-empty',
        'icon' => [
            'type' => 'emoji',
            'emoji' => '📝'
        ]
    ]);

    $result = $page->renderTitle(1);

    expect($result)->toBe('# 📝 ');
});
