<?php

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Objects\Database;

test('page object can be initialized from block context', function () {
    // Using ChildPageJson.json data
    $blockData = [
        "object" => "block",
        "id" => "263d9316-605a-8005-adfd-f8412735b609",
        "parent" => [
            "type" => "page_id",
            "page_id" => "263d9316-605a-806f-9e95-e1377a46ff3e"
        ],
        "created_time" => "2025-09-03T12:38:00.000Z",
        "last_edited_time" => "2025-09-03T12:43:00.000Z",
        "created_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "last_edited_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "has_children" => true,
        "archived" => false,
        "in_trash" => false,
        "type" => "child_page",
        "child_page" => [
            "title" => "Advanced To-Do"
        ]
    ];

    $page = Page::from($blockData);

    expect($page->getId())->toBe("263d9316-605a-8005-adfd-f8412735b609");
    expect($page->getTitle())->toBe("Advanced To-Do");
    expect($page->getParentType())->toBe("page_id");
    expect($page->getParentId())->toBe("263d9316-605a-806f-9e95-e1377a46ff3e");
    expect($page->isArchived())->toBeFalse();
    expect($page->isInTrash())->toBeFalse();
    expect($page->getCreatedTime())->toBe("2025-09-03T12:38:00.000Z");
    expect($page->getLastEditedTime())->toBe("2025-09-03T12:43:00.000Z");
});

test('database object can be initialized from block context', function () {
    // Using ChildDatabaseJson.json data
    $blockData = [
        "object" => "block",
        "id" => "263d9316-605a-80e8-8f08-cc0acb533046",
        "parent" => [
            "type" => "page_id",
            "page_id" => "263d9316-605a-806f-9e95-e1377a46ff3e"
        ],
        "created_time" => "2025-09-03T12:51:00.000Z",
        "last_edited_time" => "2025-09-03T12:51:00.000Z",
        "created_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "last_edited_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "has_children" => false,
        "archived" => false,
        "in_trash" => false,
        "type" => "child_database",
        "child_database" => [
            "title" => "Test database"
        ]
    ];

    $database = Database::from($blockData);

    expect($database->getId())->toBe("263d9316-605a-80e8-8f08-cc0acb533046");
    expect($database->getTitle())->toBe("Test database");
    expect($database->getParentType())->toBe("page_id");
    expect($database->getParentId())->toBe("263d9316-605a-806f-9e95-e1377a46ff3e");
    expect($database->isArchived())->toBeFalse();
    expect($database->isInTrash())->toBeFalse();
    expect($database->getCreatedTime())->toBe("2025-09-03T12:51:00.000Z");
    expect($database->getLastEditedTime())->toBe("2025-09-03T12:51:00.000Z");
});

test('page object can be initialized from object context', function () {
    // Using PageObject.json data
    $objectData = [
        "object" => "page",
        "id" => "263d9316-605a-806f-9e95-e1377a46ff3e",
        "created_time" => "2025-09-03T12:33:00.000Z",
        "last_edited_time" => "2025-09-08T14:58:00.000Z",
        "created_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "last_edited_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "cover" => [
            "type" => "external",
            "external" => [
                "url" => "https://www.notion.so/images/page-cover/gradients_5.png"
            ]
        ],
        "icon" => [
            "type" => "emoji",
            "emoji" => "🧪"
        ],
        "parent" => [
            "type" => "workspace",
            "workspace" => true
        ],
        "archived" => false,
        "in_trash" => false,
        "properties" => [
            "title" => [
                "id" => "title",
                "type" => "title",
                "title" => [
                    [
                        "type" => "text",
                        "text" => [
                            "content" => "Test page",
                            "link" => null
                        ],
                        "annotations" => [
                            "bold" => false,
                            "italic" => false,
                            "strikethrough" => false,
                            "underline" => false,
                            "code" => false,
                            "color" => "default"
                        ],
                        "plain_text" => "Test page",
                        "href" => null
                    ]
                ]
            ]
        ],
        "url" => "https://www.notion.so/Test-page-263d9316605a806f9e95e1377a46ff3e",
        "public_url" => null
    ];

    $page = Page::from($objectData);

    expect($page->getId())->toBe("263d9316-605a-806f-9e95-e1377a46ff3e");
    expect($page->getTitle())->toBe("Test page");
    expect($page->getUrl())->toBe("https://www.notion.so/Test-page-263d9316605a806f9e95e1377a46ff3e");
    expect($page->getPublicUrl())->toBeNull();
    expect($page->getParentType())->toBe("workspace");
    expect($page->hasIcon())->toBeTrue();
    expect($page->processIcon())->toBe("🧪");
    expect($page->hasProperties())->toBeTrue();
    expect($page->getProperty('title'))->toBeArray();
    expect($page->isArchived())->toBeFalse();
    expect($page->isInTrash())->toBeFalse();
});

test('database object can be initialized from object context', function () {
    // Using DatabaseObject.json data with simplified properties
    $objectData = [
        "object" => "database",
        "id" => "263d9316-605a-80e8-8f08-cc0acb533046",
        "cover" => null,
        "icon" => null,
        "created_time" => "2025-09-03T12:51:00.000Z",
        "created_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "last_edited_by" => [
            "object" => "user",
            "id" => "dd320fcd-ab37-435c-9ea8-d8c76cbbb486"
        ],
        "last_edited_time" => "2025-09-08T15:07:00.000Z",
        "title" => [
            [
                "type" => "text",
                "text" => [
                    "content" => "Test database",
                    "link" => null
                ],
                "annotations" => [
                    "bold" => false,
                    "italic" => false,
                    "strikethrough" => false,
                    "underline" => false,
                    "code" => false,
                    "color" => "default"
                ],
                "plain_text" => "Test database",
                "href" => null
            ]
        ],
        "properties" => [
            "Name" => [
                "id" => "title",
                "name" => "Name",
                "type" => "title",
                "title" => []
            ],
            "Status" => [
                "id" => "%60DPS",
                "name" => "Status",
                "type" => "status",
                "status" => [
                    "options" => [
                        [
                            "id" => "d21e0a9d-bc3b-4a52-836a-a5925a07bf8f",
                            "name" => "Not started",
                            "color" => "default"
                        ]
                    ]
                ]
            ]
        ],
        "parent" => [
            "type" => "page_id",
            "page_id" => "263d9316-605a-806f-9e95-e1377a46ff3e"
        ],
        "url" => "https://www.notion.so/263d9316605a80e88f08cc0acb533046",
        "public_url" => null,
        "archived" => false,
        "in_trash" => false
    ];

    $database = Database::from($objectData);

    expect($database->getId())->toBe("263d9316-605a-80e8-8f08-cc0acb533046");
    expect($database->getTitle())->toBe("Test database");
    expect($database->getUrl())->toBe("https://www.notion.so/263d9316605a80e88f08cc0acb533046");
    expect($database->getPublicUrl())->toBeNull();
    expect($database->getParentType())->toBe("page_id");
    expect($database->getParentId())->toBe("263d9316-605a-806f-9e95-e1377a46ff3e");
    expect($database->hasIcon())->toBeFalse();
    expect($database->hasProperties())->toBeTrue();
    expect($database->getProperty('Name'))->toBeArray();
    expect($database->getProperty('Status'))->toBeArray();
    expect($database->isArchived())->toBeFalse();
    expect($database->isInTrash())->toBeFalse();
});

test('page can be initialized from block context then refilled from object context', function () {
    // First initialize from block context (ChildPageJson.json)
    $blockData = [
        "object" => "block",
        "id" => "263d9316-605a-8005-adfd-f8412735b609",
        "type" => "child_page",
        "child_page" => [
            "title" => "Advanced To-Do"
        ],
        "parent" => [
            "type" => "page_id",
            "page_id" => "263d9316-605a-806f-9e95-e1377a46ff3e"
        ],
        "created_time" => "2025-09-03T12:38:00.000Z",
        "archived" => false,
        "in_trash" => false
    ];

    $page = Page::from($blockData);

    // Verify initial state from block context
    expect($page->getId())->toBe("263d9316-605a-8005-adfd-f8412735b609");
    expect($page->getTitle())->toBe("Advanced To-Do");
    expect($page->hasIcon())->toBeFalse();
    expect($page->hasProperties())->toBeFalse();
    expect($page->hasUrl())->toBeFalse();

    // Now refill from object context (PageObject.json structure)
    $objectData = [
        "id" => "263d9316-605a-8005-adfd-f8412735b609", // Same ID
        "properties" => [
            "title" => [
                "id" => "title",
                "type" => "title",
                "title" => [
                    [
                        "type" => "text",
                        "text" => [
                            "content" => "Advanced To-Do Updated",
                            "link" => null
                        ],
                        "plain_text" => "Advanced To-Do Updated",
                        "href" => null
                    ]
                ]
            ]
        ],
        "url" => "https://www.notion.so/Advanced-To-Do-263d9316605a8005adfdf8412735b609",
        "public_url" => "https://notion.so/Advanced-To-Do-public",
        "icon" => [
            "type" => "emoji",
            "emoji" => "✅"
        ],
        "last_edited_time" => "2025-09-08T16:00:00.000Z"
    ];

    $page->fill($objectData);

    // Verify updated state after refill
    expect($page->getId())->toBe("263d9316-605a-8005-adfd-f8412735b609"); // Same ID
    expect($page->getTitle())->toBe("Advanced To-Do Updated"); // Updated title from properties
    expect($page->hasIcon())->toBeTrue();
    expect($page->processIcon())->toBe("✅");
    expect($page->hasProperties())->toBeTrue();
    expect($page->hasUrl())->toBeTrue();
    expect($page->getUrl())->toBe("https://www.notion.so/Advanced-To-Do-263d9316605a8005adfdf8412735b609");
    expect($page->hasPublicUrl())->toBeTrue();
    expect($page->getPublicUrl())->toBe("https://notion.so/Advanced-To-Do-public");
    expect($page->getLastEditedTime())->toBe("2025-09-08T16:00:00.000Z");
    expect($page->getCreatedTime())->toBe("2025-09-03T12:38:00.000Z"); // Preserved from initial
});

test('database can be initialized from block context then refilled from object context', function () {
    // First initialize from block context (ChildDatabaseJson.json)
    $blockData = [
        "object" => "block",
        "id" => "263d9316-605a-80e8-8f08-cc0acb533046",
        "type" => "child_database",
        "child_database" => [
            "title" => "Test database"
        ],
        "parent" => [
            "type" => "page_id",
            "page_id" => "263d9316-605a-806f-9e95-e1377a46ff3e"
        ],
        "created_time" => "2025-09-03T12:51:00.000Z",
        "archived" => false,
        "in_trash" => false
    ];

    $database = Database::from($blockData);

    // Verify initial state from block context
    expect($database->getId())->toBe("263d9316-605a-80e8-8f08-cc0acb533046");
    expect($database->getTitle())->toBe("Test database");
    expect($database->hasIcon())->toBeFalse();
    expect($database->hasProperties())->toBeFalse();
    expect($database->hasUrl())->toBeFalse();

    // Now refill from object context (DatabaseObject.json structure)
    $objectData = [
        "id" => "263d9316-605a-80e8-8f08-cc0acb533046", // Same ID
        "title" => [
            [
                "type" => "text",
                "text" => [
                    "content" => "Test database Updated",
                    "link" => null
                ],
                "plain_text" => "Test database Updated",
                "href" => null
            ]
        ],
        "properties" => [
            "Name" => [
                "id" => "title",
                "name" => "Name",
                "type" => "title",
                "title" => []
            ],
            "Status" => [
                "id" => "status",
                "name" => "Status",
                "type" => "status",
                "status" => [
                    "options" => [
                        [
                            "name" => "Active",
                            "color" => "green"
                        ]
                    ]
                ]
            ]
        ],
        "url" => "https://www.notion.so/Test-database-263d9316605a80e88f08cc0acb533046",
        "public_url" => "https://notion.so/Test-database-public",
        "icon" => [
            "type" => "emoji",
            "emoji" => "🗃️"
        ],
        "last_edited_time" => "2025-09-08T16:30:00.000Z"
    ];

    $database->fill($objectData);

    // Verify updated state after refill
    expect($database->getId())->toBe("263d9316-605a-80e8-8f08-cc0acb533046"); // Same ID
    expect($database->getTitle())->toBe("Test database Updated"); // Updated title from rich text
    expect($database->hasIcon())->toBeTrue();
    expect($database->processIcon())->toBe("🗃️");
    expect($database->hasProperties())->toBeTrue();
    expect($database->getProperty('Name'))->toBeArray();
    expect($database->getProperty('Status'))->toBeArray();
    expect($database->hasUrl())->toBeTrue();
    expect($database->getUrl())->toBe("https://www.notion.so/Test-database-263d9316605a80e88f08cc0acb533046");
    expect($database->hasPublicUrl())->toBeTrue();
    expect($database->getPublicUrl())->toBe("https://notion.so/Test-database-public");
    expect($database->getLastEditedTime())->toBe("2025-09-08T16:30:00.000Z");
    expect($database->getCreatedTime())->toBe("2025-09-03T12:51:00.000Z"); // Preserved from initial
});

test('objects preserve existing data when refilling with partial data', function () {
    // Initialize page with full data
    $fullData = [
        "id" => "test-preserve",
        "title" => "Original Title",
        "url" => "https://original.url",
        "public_url" => "https://original.public.url",
        "properties" => [
            "field1" => "value1",
            "field2" => "value2"
        ],
        "icon" => [
            "type" => "emoji",
            "emoji" => "🔥"
        ]
    ];

    $page = Page::from($fullData);

    // Refill with partial data
    $partialData = [
        "id" => "test-preserve",
        "title" => "Updated Title",
        "public_url" => "https://updated.public.url"
    ];

    $page->fill($partialData);

    // Verify that existing data is preserved and only specified fields are updated
    expect($page->getId())->toBe("test-preserve");
    expect($page->getTitle())->toBe("Updated Title"); // Updated
    expect($page->getUrl())->toBe("https://original.url"); // Preserved
    expect($page->getPublicUrl())->toBe("https://updated.public.url"); // Updated
    expect($page->getProperties())->toBe(["field1" => "value1", "field2" => "value2"]); // Preserved
    expect($page->hasIcon())->toBeTrue(); // Preserved
    expect($page->processIcon())->toBe("🔥"); // Preserved
});
