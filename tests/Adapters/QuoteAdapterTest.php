<?php

use RedberryProducts\MdNotion\Adapters\QuoteAdapter;

test('quote adapter converts basic block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8074-b6d1-e2767b195e87',
        'type' => 'quote',
        'has_children' => false,
        'quote' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Quote Text',
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
                    'plain_text' => 'Quote Text',
                    'href' => null
                ]
            ],
            'color' => 'default'
        ]
    ];

    $adapter = new QuoteAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> Quote Text');
});

test('quote adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8074-b6d1-e2767b195e87',
        'type' => 'quote',
        'has_children' => false,
        'quote' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Important ',
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
                    'plain_text' => 'Important ',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'quote',
                        'link' => null
                    ],
                    'annotations' => [
                        'bold' => false,
                        'italic' => true,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default'
                    ],
                    'plain_text' => 'quote',
                    'href' => null
                ]
            ],
            'color' => 'default'
        ]
    ];

    $adapter = new QuoteAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> **Important** _quote_');
});

test('quote adapter handles multi-line text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8074-b6d1-e2767b195e87',
        'type' => 'quote',
        'has_children' => false,
        'quote' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => "First line\nSecond line",
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
                    'plain_text' => "First line\nSecond line",
                    'href' => null
                ]
            ],
            'color' => 'default'
        ]
    ];

    $adapter = new QuoteAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('> First line' . PHP_EOL . '> Second line');
});
