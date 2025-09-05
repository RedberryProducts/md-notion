<?php

use Mockery;
use RedberryProducts\MdNotion\Adapters\TableAdapter;
use RedberryProducts\MdNotion\SDK\Notion;

test('table adapter converts basic table to markdown', function () {
    $block = [
        'id' => 'table-123',
        'type' => 'table',
        'table' => [
            'table_width' => 3,
            'has_column_header' => true,
            'has_row_header' => false,
        ],
    ];

    $rowsResponse = [
        'results' => [
            [
                'type' => 'table_row',
                'table_row' => [
                    'cells' => [
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'Title'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'Title',
                            ],
                        ],
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'type'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'type',
                            ],
                        ],
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'date'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'date',
                            ],
                        ],
                    ],
                ],
            ],
            [
                'type' => 'table_row',
                'table_row' => [
                    'cells' => [
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'Title 1'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'Title 1',
                            ],
                        ],
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'K'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'K',
                            ],
                        ],
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => '03.09.2025'],
                                'annotations' => ['bold' => false],
                                'plain_text' => '03.09.2025',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $sdk = Mockery::mock(Notion::class);
    $sdk->shouldReceive('getBlockChildren')
        ->with('table-123')
        ->once()
        ->andReturn($rowsResponse);

    $adapter = new TableAdapter;
    $adapter->setSdk($sdk);

    $markdown = $adapter->toMarkdown($block);

    $expected = "|Title|type|date|\n|---|---|---|\n|Title 1|K|03.09.2025|";
    expect($markdown)->toBe($expected);
});

test('table adapter handles table without headers', function () {
    $block = [
        'id' => 'table-123',
        'type' => 'table',
        'table' => [
            'table_width' => 2,
            'has_column_header' => false,
            'has_row_header' => false,
        ],
    ];

    $rowsResponse = [
        'results' => [
            [
                'type' => 'table_row',
                'table_row' => [
                    'cells' => [
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'Cell 1'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'Cell 1',
                            ],
                        ],
                        [
                            [
                                'type' => 'text',
                                'text' => ['content' => 'Cell 2'],
                                'annotations' => ['bold' => false],
                                'plain_text' => 'Cell 2',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ];

    $sdk = Mockery::mock(Notion::class);
    $sdk->shouldReceive('getBlockChildren')
        ->with('table-123')
        ->once()
        ->andReturn($rowsResponse);

    $adapter = new TableAdapter;
    $adapter->setSdk($sdk);

    $markdown = $adapter->toMarkdown($block);

    expect($markdown)->toBe('|Cell 1|Cell 2|');
});
