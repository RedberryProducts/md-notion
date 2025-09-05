<?php

use RedberryProducts\MdNotion\Adapters\TableRowAdapter;

test('table row adapter converts basic row to markdown', function () {
    $block = [
        'type' => 'table_row',
        'table_row' => [
            'cells' => [
                [
                    [
                        'type' => 'text',
                        'text' => [
                            'content' => 'Title',
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
                        'plain_text' => 'Title',
                        'href' => null,
                    ],
                ],
                [
                    [
                        'type' => 'text',
                        'text' => [
                            'content' => 'type',
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
                        'plain_text' => 'type',
                        'href' => null,
                    ],
                ],
                [
                    [
                        'type' => 'text',
                        'text' => [
                            'content' => 'date',
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
                        'plain_text' => 'date',
                        'href' => null,
                    ],
                ],
            ],
        ],
    ];

    $adapter = new TableRowAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('|Title|type|date|');
});

test('table row adapter handles formatted text', function () {
    $block = [
        'type' => 'table_row',
        'table_row' => [
            'cells' => [
                [
                    [
                        'type' => 'text',
                        'text' => [
                            'content' => 'Bold',
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
                        'plain_text' => 'Bold',
                        'href' => null,
                    ],
                ],
                [
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
            ],
        ],
    ];

    $adapter = new TableRowAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('|**Bold**|_italic_|');
});
