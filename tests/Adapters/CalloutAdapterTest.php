<?php

use RedberryProducts\MdNotion\Adapters\CalloutAdapter;

test('callout adapter converts emoji block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80ed-92e0-cc82406df08d',
        'type' => 'callout',
        'has_children' => false,
        'callout' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Call out with icon',
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
                    'plain_text' => 'Call out with icon',
                    'href' => null,
                ],
            ],
            'icon' => [
                'type' => 'emoji',
                'emoji' => '2️⃣',
            ],
            'color' => 'gray_background',
        ],
    ];

    $adapter = new CalloutAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> 2️⃣ Call out with icon');
});

test('callout adapter handles external icon', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80ed-92e0-cc82406df08d',
        'type' => 'callout',
        'has_children' => false,
        'callout' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Call out with external icon',
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
                    'plain_text' => 'Call out with external icon',
                    'href' => null,
                ],
            ],
            'icon' => [
                'type' => 'external',
                'external' => [
                    'url' => 'https://www.notion.so/icons/apple_gray.svg',
                ],
            ],
            'color' => 'gray_background',
        ],
    ];

    $adapter = new CalloutAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> [Apple](https://www.notion.so/icons/apple_gray.svg) Call out with external icon');
});

test('callout adapter handles file icon', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80ed-92e0-cc82406df08d',
        'type' => 'callout',
        'has_children' => false,
        'callout' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Call out with file icon',
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
                    'plain_text' => 'Call out with file icon',
                    'href' => null,
                ],
            ],
            'icon' => [
                'type' => 'file',
                'file' => [
                    'url' => 'https://example.com/image.png',
                    'expiry_time' => '2025-09-05T12:54:30.470Z',
                ],
            ],
            'color' => 'gray_background',
        ],
    ];

    $adapter = new CalloutAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> [🔗](https://example.com/image.png) Call out with file icon');
});

test('callout adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80ed-92e0-cc82406df08d',
        'type' => 'callout',
        'has_children' => false,
        'callout' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Bold ',
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
                    'plain_text' => 'Bold ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'and italic',
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
                    'plain_text' => 'and italic',
                    'href' => null,
                ],
            ],
            'icon' => [
                'type' => 'emoji',
                'emoji' => '💡',
            ],
            'color' => 'gray_background',
        ],
    ];

    $adapter = new CalloutAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> 💡 **Bold** _and italic_');
});
