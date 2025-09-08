<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Objects\Database;

test('page object can handle icon data', function () {
    $pageData = [
        'id' => 'test-page-id',
        'title' => 'Test Page',
        'parent' => ['type' => 'page_id', 'page_id' => 'parent-id'],
        'created_time' => '2025-01-01T00:00:00.000Z',
        'last_edited_time' => '2025-01-01T00:00:00.000Z',
        'created_by' => ['object' => 'user', 'id' => 'user-id'],
        'last_edited_by' => ['object' => 'user', 'id' => 'user-id'],
        'has_children' => false,
        'archived' => false,
        'in_trash' => false,
        'icon' => [
            'type' => 'emoji',
            'emoji' => '📄'
        ]
    ];

    $page = Page::from($pageData);

    expect($page->hasIcon())->toBeTrue();
    expect($page->getIcon())->toBe(['type' => 'emoji', 'emoji' => '📄']);
    expect($page->processIcon())->toBe('📄');

    $arrayData = $page->toArray();
    expect($arrayData['icon'])->toBe(['type' => 'emoji', 'emoji' => '📄']);
});

test('database object can handle icon data', function () {
    $databaseData = [
        'id' => 'test-database-id',
        'title' => 'Test Database',
        'parent' => ['type' => 'page_id', 'page_id' => 'parent-id'],
        'created_time' => '2025-01-01T00:00:00.000Z',
        'last_edited_time' => '2025-01-01T00:00:00.000Z',
        'created_by' => ['object' => 'user', 'id' => 'user-id'],
        'last_edited_by' => ['object' => 'user', 'id' => 'user-id'],
        'archived' => false,
        'in_trash' => false,
        'icon' => [
            'type' => 'external',
            'external' => [
                'url' => 'https://example.com/icons/database_icon.svg'
            ]
        ]
    ];

    $database = Database::from($databaseData);

    expect($database->hasIcon())->toBeTrue();
    expect($database->getIcon()['type'])->toBe('external');
    expect($database->processIcon())->toBe('[Database](https://example.com/icons/database_icon.svg)');

    $arrayData = $database->toArray();
    expect($arrayData['icon']['type'])->toBe('external');
});

test('icon processing handles different icon types', function () {
    $page = new Page();

    // Test emoji icon
    $emojiIcon = ['type' => 'emoji', 'emoji' => '🚀'];
    expect($page->processIcon($emojiIcon))->toBe('🚀');

    // Test file icon
    $fileIcon = ['type' => 'file', 'file' => ['url' => 'https://example.com/icon.png']];
    expect($page->processIcon($fileIcon))->toBe('[🔗](https://example.com/icon.png)');

    // Test external icon
    $externalIcon = ['type' => 'external', 'external' => ['url' => 'https://example.com/icons/custom_icon.svg']];
    expect($page->processIcon($externalIcon))->toBe('[Custom](https://example.com/icons/custom_icon.svg)');

    // Test default icon
    $unknownIcon = ['type' => 'unknown'];
    expect($page->processIcon($unknownIcon))->toBe('💡');

    // Test empty icon
    expect($page->processIcon())->toBe('');
    expect($page->processIcon(null))->toBe('');
});
