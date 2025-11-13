<?php

use Redberry\MdNotion\Services\PropertiesTable;

beforeEach(function () {
    $this->propertiesTable = new PropertiesTable;
});

test('returns empty string for empty properties', function () {
    $result = $this->propertiesTable->convertPropertiesToMarkdownTable([]);

    expect($result)->toBe('');
});

test('renders title property correctly', function () {
    $properties = [
        'Name' => [
            'id' => 'title',
            'type' => 'title',
            'title' => [
                ['plain_text' => 'Test Page'],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Property | Value |')
        ->and($result)->toContain('| Name | Test Page |');
});

test('renders url property correctly', function () {
    $properties = [
        'URL' => [
            'id' => 'BLtm',
            'type' => 'url',
            'url' => 'https://github.com/example',
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| URL | [https://github.com/example](https://github.com/example) |');
});

test('renders email property correctly', function () {
    $properties = [
        'Email' => [
            'id' => 'YLWr',
            'type' => 'email',
            'email' => 'test@example.com',
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Email | [test@example.com](mailto:test@example.com) |');
});

test('renders checkbox property correctly', function () {
    $properties = [
        'CheckboxField' => [
            'id' => 'LPTO',
            'type' => 'checkbox',
            'checkbox' => true,
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| CheckboxField | ✓ |');
});

test('renders unchecked checkbox correctly', function () {
    $properties = [
        'CheckboxField' => [
            'id' => 'LPTO',
            'type' => 'checkbox',
            'checkbox' => false,
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| CheckboxField | ✗ |');
});

test('renders number property correctly', function () {
    $properties = [
        'NumberField' => [
            'id' => 'YP%3Ay',
            'type' => 'number',
            'number' => 343451234,
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| NumberField | 343451234 |');
});

test('renders date property with start date only', function () {
    $properties = [
        'Date' => [
            'id' => '%5Dm%3Ez',
            'type' => 'date',
            'date' => [
                'start' => '2025-09-10',
                'end' => null,
                'time_zone' => null,
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Date | 2025-09-10 |');
});

test('renders date property with date range', function () {
    $properties = [
        'Date' => [
            'id' => '%5Dm%3Ez',
            'type' => 'date',
            'date' => [
                'start' => '2025-09-10',
                'end' => '2025-09-15',
                'time_zone' => null,
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Date | 2025-09-10 → 2025-09-15 |');
});

test('renders select property correctly', function () {
    $properties = [
        'Type' => [
            'id' => 'MbTK',
            'type' => 'select',
            'select' => [
                'id' => 'LOQu',
                'name' => 'type-2',
                'color' => 'pink',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Type | type-2 |');
});

test('renders multi_select property correctly', function () {
    $properties = [
        'Priority Level' => [
            'id' => 'fvdf',
            'type' => 'multi_select',
            'multi_select' => [
                [
                    'id' => 'wSec',
                    'name' => '4',
                    'color' => 'gray',
                ],
                [
                    'id' => 'xyz',
                    'name' => '5',
                    'color' => 'blue',
                ],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Priority Level | 4, 5 |');
});

test('renders status property correctly', function () {
    $properties = [
        'Status' => [
            'id' => '%60DPS',
            'type' => 'status',
            'status' => [
                'id' => 'd21e0a9d-bc3b-4a52-836a-a5925a07bf8f',
                'name' => 'Not started',
                'color' => 'default',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Status | Not started |');
});

test('skips properties with empty values', function () {
    $properties = [
        'Email' => [
            'id' => 'YLWr',
            'type' => 'email',
            'email' => null,
        ],
        'Name' => [
            'id' => 'title',
            'type' => 'title',
            'title' => [
                ['plain_text' => 'Test Page'],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->not->toContain('| Email |')
        ->and($result)->toContain('| Name | Test Page |');
});

test('renders people property correctly', function () {
    $properties = [
        'Person' => [
            'id' => '%7BaiB',
            'type' => 'people',
            'people' => [
                ['name' => 'John Doe'],
                ['name' => 'Jane Smith'],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Person | John Doe, Jane Smith |');
});

test('renders files property with external files', function () {
    $properties = [
        'Files & media' => [
            'id' => 'iLcJ',
            'type' => 'files',
            'files' => [
                [
                    'name' => 'document.pdf',
                    'type' => 'external',
                    'external' => [
                        'url' => 'https://example.com/document.pdf',
                    ],
                ],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Files & media | [document.pdf](https://example.com/document.pdf) |');
});

test('renders files property with uploaded files', function () {
    $properties = [
        'Files & media' => [
            'id' => 'iLcJ',
            'type' => 'files',
            'files' => [
                [
                    'name' => 'image.png',
                    'type' => 'file',
                    'file' => [
                        'url' => 'https://s3.amazonaws.com/notion/image.png',
                    ],
                ],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Files & media | [image.png](https://s3.amazonaws.com/notion/image.png) |');
});

test('renders phone_number property correctly', function () {
    $properties = [
        'Phone' => [
            'id' => 'phone',
            'type' => 'phone_number',
            'phone_number' => '+1234567890',
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Phone | +1234567890 |');
});

test('renders created_time property correctly', function () {
    $properties = [
        'Created' => [
            'id' => 'created',
            'type' => 'created_time',
            'created_time' => '2025-09-10T10:30:00.000Z',
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Created | 2025-09-10T10:30:00.000Z |');
});

test('renders created_by property correctly', function () {
    $properties = [
        'Created By' => [
            'id' => 'created_by',
            'type' => 'created_by',
            'created_by' => [
                'id' => 'user-id',
                'name' => 'John Doe',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Created By | John Doe |');
});

test('renders last_edited_time property correctly', function () {
    $properties = [
        'Last Edited' => [
            'id' => 'last_edited',
            'type' => 'last_edited_time',
            'last_edited_time' => '2025-09-11T15:45:00.000Z',
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Last Edited | 2025-09-11T15:45:00.000Z |');
});

test('renders last_edited_by property correctly', function () {
    $properties = [
        'Last Edited By' => [
            'id' => 'last_edited_by',
            'type' => 'last_edited_by',
            'last_edited_by' => [
                'id' => 'user-id',
                'name' => 'Jane Smith',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Last Edited By | Jane Smith |');
});

test('renders relation property correctly', function () {
    $properties = [
        'Related Pages' => [
            'id' => 'relation',
            'type' => 'relation',
            'relation' => [
                ['id' => 'page-1'],
                ['id' => 'page-2'],
                ['id' => 'page-3'],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Related Pages | 3 related item(s) |');
});

test('renders rollup property with number type', function () {
    $properties = [
        'Total' => [
            'id' => 'rollup',
            'type' => 'rollup',
            'rollup' => [
                'type' => 'number',
                'number' => 42,
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Total | 42 |');
});

test('renders rollup property with array type', function () {
    $properties = [
        'Items' => [
            'id' => 'rollup',
            'type' => 'rollup',
            'rollup' => [
                'type' => 'array',
                'array' => [1, 2, 3, 4, 5],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Items | 5 item(s) |');
});

test('renders formula property with string type', function () {
    $properties = [
        'Calculated' => [
            'id' => 'formula',
            'type' => 'formula',
            'formula' => [
                'type' => 'string',
                'string' => 'Result Text',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Calculated | Result Text |');
});

test('renders formula property with boolean type', function () {
    $properties = [
        'Is Valid' => [
            'id' => 'formula',
            'type' => 'formula',
            'formula' => [
                'type' => 'boolean',
                'boolean' => true,
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Is Valid | Yes |');
});

test('renders multiple properties in correct table format', function () {
    $properties = [
        'Name' => [
            'id' => 'title',
            'type' => 'title',
            'title' => [
                ['plain_text' => 'Test Page'],
            ],
        ],
        'URL' => [
            'id' => 'url',
            'type' => 'url',
            'url' => 'https://example.com',
        ],
        'Status' => [
            'id' => 'status',
            'type' => 'status',
            'status' => [
                'name' => 'In Progress',
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Property | Value |')
        ->and($result)->toContain('| --- | --- |')
        ->and($result)->toContain('| Name | Test Page |')
        ->and($result)->toContain('| URL | [https://example.com](https://example.com) |')
        ->and($result)->toContain('| Status | In Progress |');
});

test('renders rich_text property correctly', function () {
    $properties = [
        'Description' => [
            'id' => 'rich',
            'type' => 'rich_text',
            'rich_text' => [
                ['plain_text' => 'This is '],
                ['plain_text' => 'a description'],
            ],
        ],
    ];

    $result = $this->propertiesTable->convertPropertiesToMarkdownTable($properties);

    expect($result)->toContain('| Description | This is a description |');
});
