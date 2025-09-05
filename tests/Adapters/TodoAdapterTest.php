<?php

use RedberryProducts\MdNotion\Adapters\TodoAdapter;

test('todo adapter converts unchecked block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80dd-9672-dc746fb5c2d5',
        'type' => 'to_do',
        'has_children' => false,
        'to_do' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'To do 1',
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
                    'plain_text' => 'To do 1',
                    'href' => null
                ]
            ],
            'checked' => false,
            'color' => 'default'
        ]
    ];

    $adapter = new TodoAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('- [ ] To do 1');
});

test('todo adapter converts checked block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80dd-9672-dc746fb5c2d5',
        'type' => 'to_do',
        'has_children' => false,
        'to_do' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Completed task',
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
                    'plain_text' => 'Completed task',
                    'href' => null
                ]
            ],
            'checked' => true,
            'color' => 'default'
        ]
    ];

    $adapter = new TodoAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('- [x] Completed task');
});

test('todo adapter handles formatted text', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80dd-9672-dc746fb5c2d5',
        'type' => 'to_do',
        'has_children' => false,
        'to_do' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Formatted ',
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
                    'plain_text' => 'Formatted ',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'todo item',
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
                    'plain_text' => 'todo item',
                    'href' => null
                ]
            ],
            'checked' => true,
            'color' => 'default'
        ]
    ];

    $adapter = new TodoAdapter();
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('- [x] **Formatted** _todo item_');
});
