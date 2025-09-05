<?php

// config for RedberryProducts/MdNotion
return [

    /**
     * The Notion API key used for authentication with the Notion API.
     */
    "notion_api_key" => env("NOTION_API_KEY", ""),

    /**
     * Defines the maximum block number that can be fetched in a single request.
     */
    "default_page_size" => env("NOTION_DEFAULT_PAGE_SIZE", 100),

    /**
     * Block type to adapter class mappings.
     * Keys are Notion block types from the JSON response.
     * Values are fully qualified adapter class names.
     */
    "adapters" => [
        "paragraph" => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
        "heading_1" => fn() => new \RedberryProducts\MdNotion\Adapters\HeadingAdapter(1),
        "heading_2" => fn() => new \RedberryProducts\MdNotion\Adapters\HeadingAdapter(2),
        "heading_3" => fn() => new \RedberryProducts\MdNotion\Adapters\HeadingAdapter(3),
        "bulleted_list_item" => \RedberryProducts\MdNotion\Adapters\BulletedListItemAdapter::class,
        "numbered_list_item" => \RedberryProducts\MdNotion\Adapters\NumberedListItemAdapter::class,
        "to_do" => \RedberryProducts\MdNotion\Adapters\ToDoAdapter::class,
        "toggle" => \RedberryProducts\MdNotion\Adapters\ToggleAdapter::class,
        "code" => \RedberryProducts\MdNotion\Adapters\CodeAdapter::class,
        "quote" => \RedberryProducts\MdNotion\Adapters\QuoteAdapter::class,
        "callout" => \RedberryProducts\MdNotion\Adapters\CalloutAdapter::class,
        "divider" => \RedberryProducts\MdNotion\Adapters\DividerAdapter::class,
        "bookmark" => \RedberryProducts\MdNotion\Adapters\BookmarkAdapter::class,
        "image" => \RedberryProducts\MdNotion\Adapters\ImageAdapter::class,
        "file" => \RedberryProducts\MdNotion\Adapters\FileAdapter::class,
        "video" => \RedberryProducts\MdNotion\Adapters\VideoAdapter::class,
        "column_list" => \RedberryProducts\MdNotion\Adapters\ColumnListAdapter::class,
        "column" => \RedberryProducts\MdNotion\Adapters\ColumnAdapter::class,
        "table" => \RedberryProducts\MdNotion\Adapters\TableAdapter::class,
        "table_row" => \RedberryProducts\MdNotion\Adapters\TableRowAdapter::class,
        "child_page" => \RedberryProducts\MdNotion\Adapters\ChildPageAdapter::class,
        "child_database" => \RedberryProducts\MdNotion\Adapters\ChildDatabaseAdapter::class,
    ],
];
