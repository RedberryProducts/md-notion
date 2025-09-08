<?php

use RedberryProducts\MdNotion\Adapters\HeadingAdapter;

test('heading_1 adapter converts basic block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-803b-e473728d4e59',
        'type' => 'heading_1',
        'has_children' => false,
        'heading_1' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Heading 1',
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
                    'plain_text' => 'Heading 1',
                    'href' => null,
                ],
            ],
            'is_toggleable' => false,
            'color' => 'default',
        ],
    ];

    $adapter = new HeadingAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('# Heading 1');
});

test('heading_2 adapter converts basic block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80e3-90f3-cc85c61895aa',
        'type' => 'heading_2',
        'has_children' => false,
        'heading_2' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Heading 2',
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
                    'plain_text' => 'Heading 2',
                    'href' => null,
                ],
            ],
            'is_toggleable' => false,
            'color' => 'default',
        ],
    ];

    $adapter = new HeadingAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('## Heading 2');
});

test('heading_3 adapter converts basic block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80fb-a94c-e68fbd2b01fc',
        'type' => 'heading_3',
        'has_children' => false,
        'heading_3' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'And Heading 3',
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
                    'plain_text' => 'And Heading 3',
                    'href' => null,
                ],
            ],
            'is_toggleable' => false,
            'color' => 'default',
        ],
    ];

    $adapter = new HeadingAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('### And Heading 3');
});

test('heading adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80fb-a94c-e68fbd2b01fc',
        'type' => 'heading_1',
        'has_children' => false,
        'heading_1' => [
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
                        'content' => 'Heading',
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
                    'plain_text' => 'Heading',
                    'href' => null,
                ],
            ],
            'is_toggleable' => false,
            'color' => 'default',
        ],
    ];

    $adapter = new HeadingAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('# **Formatted** _Heading_');
});
