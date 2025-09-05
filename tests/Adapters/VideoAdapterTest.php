<?php

use RedberryProducts\MdNotion\Adapters\VideoAdapter;

test('video adapter converts external video block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a3-be4e-e7319734943c',
        'type' => 'video',
        'has_children' => false,
        'video' => [
            'caption' => [],
            'type' => 'external',
            'external' => [
                'url' => 'https://www.youtube.com/watch?v=0PiovWiQe8w',
            ],
        ],
    ];

    $adapter = new VideoAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('🎥 [Watch Video](https://www.youtube.com/watch?v=0PiovWiQe8w)');
});

test('video adapter converts file type video block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a3-be4e-e7319734943c',
        'type' => 'video',
        'has_children' => false,
        'video' => [
            'caption' => [],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/video.mp4',
                'expiry_time' => '2025-09-03T14:04:44.914Z',
            ],
        ],
    ];

    $adapter = new VideoAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('🎥 [Watch Video](https://example.com/video.mp4)');
});

test('video adapter handles block with caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a3-be4e-e7319734943c',
        'type' => 'video',
        'has_children' => false,
        'video' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Tutorial video',
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
                    'plain_text' => 'Tutorial video',
                    'href' => null,
                ],
            ],
            'type' => 'external',
            'external' => [
                'url' => 'https://www.youtube.com/watch?v=0PiovWiQe8w',
            ],
        ],
    ];

    $adapter = new VideoAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('🎥 [Tutorial video](https://www.youtube.com/watch?v=0PiovWiQe8w)');
});

test('video adapter handles block with formatted caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80a3-be4e-e7319734943c',
        'type' => 'video',
        'has_children' => false,
        'video' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Watch this ',
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
                    'plain_text' => 'Watch this ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'tutorial',
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
                    'plain_text' => 'tutorial',
                    'href' => null,
                ],
            ],
            'type' => 'external',
            'external' => [
                'url' => 'https://www.youtube.com/watch?v=0PiovWiQe8w',
            ],
        ],
    ];

    $adapter = new VideoAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('🎥 [**Watch this** _tutorial_](https://www.youtube.com/watch?v=0PiovWiQe8w)');
});
