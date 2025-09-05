<?php

use RedberryProducts\MdNotion\Adapters\BookmarkAdapter;

test('bookmark adapter converts block to markdown', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8083-ab06-d5fab031aa5b',
        'type' => 'bookmark',
        'has_children' => false,
        'bookmark' => [
            'caption' => [],
            'url' => 'https://docs.n8n.io/integrations/builtin/credentials/notion/#using-api-integration-token',
        ],
    ];

    $adapter = new BookmarkAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('[docs.n8n.io  - Bookmark ](https://docs.n8n.io/integrations/builtin/credentials/notion/#using-api-integration-token)');
});

test('bookmark adapter handles captions', function () {
    $block = [
        'object' => 'block',
        'id' => '263d9316-605a-8083-ab06-d5fab031aa5b',
        'type' => 'bookmark',
        'has_children' => false,
        'bookmark' => [
            'caption' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Check docs here',
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
                    'plain_text' => 'Check docs here',
                    'href' => null,
                ],
            ],
            'url' => 'https://docs.n8n.io/integrations/builtin/credentials/notion/#using-api-integration-token',
        ],
    ];

    $adapter = new BookmarkAdapter;
    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('[docs.n8n.io  -  Check docs here ](https://docs.n8n.io/integrations/builtin/credentials/notion/#using-api-integration-token)');
});
