<?php

use Redberry\MdNotion\Adapters\NumberedListItemAdapter;

test('numbered list item adapter converts basic block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8019-900e-e21d57d643e5',
        'type' => 'numbered_list_item',
        'has_children' => false,
        'numbered_list_item' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Numbered list',
                        'link' => null,
                    ],
                    'annotations' => [
                        'bold' => false,
                        'italic' => false,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default',
                    ],
                    'plain_text' => 'Numbered list',
                    'href' => null,
                ],
            ],
            'color' => 'default',
        ],
    ];

    $adapter = new NumberedListItemAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('1. Numbered list');
});

test('numbered list item adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8019-900e-e21d57d643e5',
        'type' => 'numbered_list_item',
        'has_children' => false,
        'numbered_list_item' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Formatted ',
                        'link' => null,
                    ],
                    'annotations' => [
                        'bold' => true,
                        'italic' => false,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default',
                    ],
                    'plain_text' => 'Formatted ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'list item',
                        'link' => null,
                    ],
                    'annotations' => [
                        'bold' => false,
                        'italic' => true,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default',
                    ],
                    'plain_text' => 'list item',
                    'href' => null,
                ],
            ],
            'color' => 'default',
        ],
    ];

    $adapter = new NumberedListItemAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('1. **Formatted** _list item_');
});
