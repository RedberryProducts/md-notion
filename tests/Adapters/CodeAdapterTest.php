<?php

use RedberryProducts\MdNotion\Adapters\CodeAdapter;

test('code adapter converts code block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80e9-a228-e7e1088d6a44',
        'type' => 'code',
        'has_children' => false,
        'code' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Got the Code block here with PHP',
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
                    'plain_text' => 'Got the Code block here with PHP',
                    'href' => null
                ]
            ],
            'language' => 'php',
            'caption' => []
        ]
    ];

    $adapter = new CodeAdapter();
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;

    expect($markdown)->toBe("```php{$nl} Got the Code block here with PHP{$nl}```");
});

test('code adapter handles code block with caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80e9-a228-e7e1088d6a44',
        'type' => 'code',
        'has_children' => false,
        'code' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => '<?php\n\necho "Hello World!";',
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
                    'plain_text' => '<?php\n\necho "Hello World!";',
                    'href' => null
                ]
            ],
            'language' => 'php',
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Simple PHP example',
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
                    'plain_text' => 'Simple PHP example',
                    'href' => null
                ]
            ]
        ]
    ];

    $adapter = new CodeAdapter();
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;

    expect($markdown)->toBe("```php{$nl} <?php\n\necho \"Hello World!\";{$nl}```{$nl}{$nl}> Simple PHP example");
});

test('code adapter handles code block with formatted caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-80e9-a228-e7e1088d6a44',
        'type' => 'code',
        'has_children' => false,
        'code' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'const greeting = "Hello";',
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
                    'plain_text' => 'const greeting = "Hello";',
                    'href' => null
                ]
            ],
            'language' => 'javascript',
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Using ',
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
                    'plain_text' => 'Using ',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'const',
                        'link' => null
                    ],
                    'annotations' => [
                        'bold' => true,
                        'italic' => false,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => true,
                        'color' => 'default'
                    ],
                    'plain_text' => 'const',
                    'href' => null
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => ' for constants',
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
                    'plain_text' => ' for constants',
                    'href' => null
                ]
            ]
        ]
    ];

    $adapter = new CodeAdapter();
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;

    expect($markdown)->toBe("```javascript{$nl} const greeting = \"Hello\";{$nl}```{$nl}{$nl}> Using **`const`** for constants");
});
