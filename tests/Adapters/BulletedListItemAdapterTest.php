<?php

use RedberryProducts\MdNotion\Adapters\BulletedListItemAdapter;

test('bulleted list item adapter converts block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8071-b771-ea869058502b',
        'type' => 'bulleted_list_item',
        'has_children' => false,
        'bulleted_list_item' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'list 1',
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
                    'plain_text' => 'list 1',
                    'href' => null,
                ],
            ],
            'color' => 'default',
        ],
    ];

    $adapter = new BulletedListItemAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('- list 1');
});

test('bulleted list item adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8071-b771-ea869058502b',
        'type' => 'bulleted_list_item',
        'has_children' => false,
        'bulleted_list_item' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'bold ',
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
                    'plain_text' => 'bold ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'and ',
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
                    'plain_text' => 'and ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'italic',
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
                    'plain_text' => 'italic',
                    'href' => null,
                ],
            ],
            'color' => 'default',
        ],
    ];

    $adapter = new BulletedListItemAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('- **bold** and _italic_');
});
