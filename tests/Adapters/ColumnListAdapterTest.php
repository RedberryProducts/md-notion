<?php

use Redberry\MdNotion\Adapters\ColumnListAdapter;
use Redberry\MdNotion\DTOs\ColumnListDTO;
use Redberry\MdNotion\SDK\Notion;

it('converts column list block to markdown', function () {
    // Create mock SDK response for columns
    $mockColumnResponse = [
        'results' => [
            // First column with its content
            [
                'type' => 'column',
                'column' => ['width_ratio' => 0.5],
                'id' => 'column-1',
            ],
            // Second column with its content
            [
                'type' => 'column',
                'column' => ['width_ratio' => 0.5],
                'id' => 'column-2',
            ],
        ],
    ];

    // Mock response for column children
    $mockColumnChildrenResponse = [
        'results' => [
            [
                'object' => 'block',
                'id' => 'paragraph-1',
                'type' => 'paragraph',
                'has_children' => false,
                'paragraph' => [
                    'rich_text' => [
                        [
                            'type' => 'text',
                            'text' => [
                                'content' => 'Column content',
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
                            'plain_text' => 'Column content',
                            'href' => null,
                        ],
                    ],
                    'color' => 'default',
                ],
            ],
        ],
    ];

    // Create mock SDK (getBlockChildren now returns array directly)
    $mockSdk = Mockery::mock(Notion::class);
    $mockSdk->shouldReceive('act->getBlockChildren')
        ->once()
        ->with('263d9316-605a-8057-b12e-f880bc565fcb', null)
        ->andReturn($mockColumnResponse);

    $mockSdk->shouldReceive('act->getBlockChildren')
        ->with('column-1', null)
        ->once()
        ->andReturn($mockColumnChildrenResponse);

    $mockSdk->shouldReceive('act->getBlockChildren')
        ->with('column-2', null)
        ->once()
        ->andReturn($mockColumnChildrenResponse);

    // Create the adapter
    $adapter = new ColumnListAdapter;
    $adapter->setSdk($mockSdk);

    // Load sample column list block
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ColumnListJson.json'), true);

    // Convert to markdown
    $markdown = $adapter->toMarkdown($block);

    // Expected markdown structure
    $expected = <<<'MD'
**Column 1**

Column content

---

**Column 2**

Column content
MD;

    expect($markdown)->toBe($expected);
});

it('creates column list DTO from block data', function () {
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ColumnListJson.json'), true);

    $dto = ColumnListDTO::from($block);

    expect($dto)->toBeInstanceOf(ColumnListDTO::class);
});

// Helper removed as we're using direct Mockery mocks now
