<?php

use RedberryProducts\MdNotion\Adapters\ParagraphAdapter;

test('paragraph adapter converts block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80da-9e63-d58d46d765bb',
        'type' => 'paragraph',
        'has_children' => false,
        'paragraph' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Hello ',
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
                    'plain_text' => 'Hello ',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'world',
                        'link' => ['url' => 'https://example.com']
                    ],
                    'annotations' => [
                        'bold' => false,
                        'italic' => false,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default'
                    ],
                    'plain_text' => 'world',
                    'href' => 'https://example.com'
                ]
            ],
            'color' => 'default'
        ]
    ];

    $adapter = new ParagraphAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('**Hello** [world](https://example.com)');
});
