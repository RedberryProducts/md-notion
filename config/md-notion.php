<?php

// config for Redberry/MdNotion
return [

    /**
     * The Notion API key used for authentication with the Notion API.
     */
    'notion_api_key' => env('NOTION_API_KEY', ''),

    /**
     * Defines the maximum block number that can be fetched in a single request.
     */
    'default_page_size' => env('NOTION_DEFAULT_PAGE_SIZE', 100),

    /**
     * Blade templates for markdown rendering
     */
    'templates' => [
        'page_markdown' => 'md-notion::page-md',
        'full_markdown' => 'md-notion::full-md',
    ],

    /**
     * Block type to adapter class mappings.
     * Keys are Notion block types from the JSON response.
     * Values are fully qualified adapter class names.
     */
    'adapters' => [
        'paragraph' => \Redberry\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \Redberry\MdNotion\Adapters\HeadingAdapter::class,
        'heading_2' => \Redberry\MdNotion\Adapters\HeadingAdapter::class,
        'heading_3' => \Redberry\MdNotion\Adapters\HeadingAdapter::class,
        'bulleted_list_item' => \Redberry\MdNotion\Adapters\BulletedListItemAdapter::class,
        'numbered_list_item' => \Redberry\MdNotion\Adapters\NumberedListItemAdapter::class,
        'to_do' => \Redberry\MdNotion\Adapters\ToDoAdapter::class,
        'toggle' => \Redberry\MdNotion\Adapters\ToggleAdapter::class,
        'code' => \Redberry\MdNotion\Adapters\CodeAdapter::class,
        'quote' => \Redberry\MdNotion\Adapters\QuoteAdapter::class,
        'callout' => \Redberry\MdNotion\Adapters\CalloutAdapter::class,
        'divider' => \Redberry\MdNotion\Adapters\DividerAdapter::class,
        'bookmark' => \Redberry\MdNotion\Adapters\BookmarkAdapter::class,
        'image' => \Redberry\MdNotion\Adapters\ImageAdapter::class,
        'file' => \Redberry\MdNotion\Adapters\FileAdapter::class,
        'video' => \Redberry\MdNotion\Adapters\VideoAdapter::class,
        'column_list' => \Redberry\MdNotion\Adapters\ColumnListAdapter::class,
        'column' => \Redberry\MdNotion\Adapters\ColumnAdapter::class,
        'table' => \Redberry\MdNotion\Adapters\TableAdapter::class,
        'table_row' => \Redberry\MdNotion\Adapters\TableRowAdapter::class,
    ],
];
