<?php

use Redberry\MdNotion\Adapters\DividerAdapter;

test('divider adapter converts block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-805b-a90d-f9fd5767d159',
        'type' => 'divider',
        'has_children' => false,
        'divider' => [],
    ];

    $adapter = new DividerAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('---');
});

test('divider adapter handles empty divider object', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-805b-a90d-f9fd5767d159',
        'type' => 'divider',
        'has_children' => false,
        'divider' => [],
    ];

    $adapter = new DividerAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('---');
});
