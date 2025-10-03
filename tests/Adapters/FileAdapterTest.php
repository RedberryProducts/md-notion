<?php

use Redberry\MdNotion\Adapters\FileAdapter;

test('file adapter converts basic file block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-81e5-c8a11d9c1122',
        'type' => 'file',
        'has_children' => false,
        'file' => [
            'caption' => [],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/file.pdf',
                'expiry_time' => '2025-09-03T14:04:44.912Z',
            ],
            'name' => 'example.pdf',
        ],
    ];

    $adapter = new FileAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('📎 [example.pdf](https://example.com/file.pdf)');
});

test('file adapter handles file block with caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-81e5-c8a11d9c1122',
        'type' => 'file',
        'has_children' => false,
        'file' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Project documentation',
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
                    'plain_text' => 'Project documentation',
                    'href' => null,
                ],
            ],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/docs.pdf',
                'expiry_time' => '2025-09-03T14:04:44.912Z',
            ],
            'name' => 'documentation.pdf',
        ],
    ];

    $adapter = new FileAdapter;
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;
    expect($markdown)->toBe("📎 [documentation.pdf](https://example.com/docs.pdf){$nl}{$nl}> Project documentation");
});

test('file adapter handles file block with formatted caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-81e5-c8a11d9c1122',
        'type' => 'file',
        'has_children' => false,
        'file' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Download ',
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
                    'plain_text' => 'Download ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'project files',
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
                    'plain_text' => 'project files',
                    'href' => null,
                ],
            ],
            'type' => 'file',
            'file' => [
                'url' => 'https://example.com/project.zip',
                'expiry_time' => '2025-09-03T14:04:44.912Z',
            ],
            'name' => 'project.zip',
        ],
    ];

    $adapter = new FileAdapter;
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;

    expect($markdown)->toBe("📎 [project.zip](https://example.com/project.zip){$nl}{$nl}> Download **project files**");
});

test('file adapter handles external file block', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-81e5-c8a11d9c1122',
        'type' => 'file',
        'has_children' => false,
        'file' => [
            'caption' => [],
            'type' => 'external',
            'external' => [
                'url' => 'https://github.com/MaestroError/LarAgent/settings/copilot/coding_agent',
            ],
            'name' => 'coding_agent',
        ],
    ];

    $adapter = new FileAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('🔗 [coding_agent](https://github.com/MaestroError/LarAgent/settings/copilot/coding_agent)');
});

test('file adapter handles external file block with caption', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-806f-81e5-c8a11d9c1122',
        'type' => 'file',
        'has_children' => false,
        'file' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Visit ',
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
                    'plain_text' => 'Visit ',
                    'href' => null,
                ],
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'coding agent',
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
                    'plain_text' => 'coding agent',
                    'href' => null,
                ],
            ],
            'type' => 'external',
            'external' => [
                'url' => 'https://github.com/MaestroError/LarAgent/settings/copilot/coding_agent',
            ],
            'name' => 'coding_agent',
        ],
    ];

    $adapter = new FileAdapter;
    $markdown = $adapter->toMarkdown($block);

    $nl = PHP_EOL;
    expect($markdown)->toBe("🔗 [coding_agent](https://github.com/MaestroError/LarAgent/settings/copilot/coding_agent){$nl}{$nl}> Visit **coding agent**");
});
