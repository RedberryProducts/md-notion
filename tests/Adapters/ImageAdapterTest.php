<?php

use RedberryProducts\MdNotion\Adapters\ImageAdapter;

test('image adapter converts file type image block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a1-97de-fde910ee4001',
        'type' => 'image',
        'has_children' => false,
        'image' => [
            'caption' => [],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/image.png',
                'expiry_time' => '2025-09-03T14:04:44.914Z'
            ]
        ]
    ];

    $adapter = new ImageAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('![](https://example.com/image.png)');
});

test('image adapter converts external type image block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a1-97de-fde910ee4002',
        'type' => 'image',
        'has_children' => false,
        'image' => [
            'caption' => [],
            'type' => 'external',
            'external' => [
                'url' => 'https://example.com/image-external.png'
            ]
        ]
    ];

    $adapter = new ImageAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('![](https://example.com/image-external.png)');
});

test('image adapter handles block with caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a1-97de-fde910ee4003',
        'type' => 'image',
        'has_children' => false,
        'image' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'An example image',
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
                    'plain_text' => 'An example image',
                    'href' => null
                ]
            ],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/image-with-caption.png',
                'expiry_time' => '2025-09-03T14:04:44.914Z'
            ]
        ]
    ];

    $adapter = new ImageAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('![An example image](https://example.com/image-with-caption.png)');
});

test('image adapter handles block with formatted caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a1-97de-fde910ee4004',
        'type' => 'image',
        'has_children' => false,
        'image' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'A ',
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
                    'plain_text' => 'A ',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'formatted',
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
                    'plain_text' => 'formatted',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => ' caption',
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
                    'plain_text' => ' caption',
                    'href' => null
                ]
            ],
            'type' => 'external',
            'external' => [
                'url' => 'https://example.com/image-formatted-caption.png'
            ]
        ]
    ];

    $adapter = new ImageAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('![A **formatted** caption](https://example.com/image-formatted-caption.png)');
});
