# ContentManager Usage Guide

The ContentManager is a powerful service that allows you to fetch Notion pages and convert them to markdown with support for child pages and databases.

## Overview

The ContentManager system consists of three main components:

1. **ContentManager** - Fetches blocks and converts them to markdown
2. **BlockRegistry** - Resolves block types to their appropriate adapters
3. **DatabaseTable** - Handles database content and converts to markdown tables

## Basic Usage

### Setup

```php
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\ContentManager;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Services\DatabaseTable;
use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;

// Initialize SDK
$notion = new Notion($apiKey, '2022-06-28');

// Load adapter configuration
$adapters = config('md-notion.adapters');

// Create services
$factory = new BlockAdapterFactory($notion, $adapters);
$registry = new BlockRegistry($factory);
$contentManager = new ContentManager($notion, $registry);
$databaseTable = new DatabaseTable($notion, $contentManager);
```

### Fetching Page Content

```php
// Fetch a page with its markdown content
$page = $contentManager->fetchPageContent($pageId);

echo $page->getContent(); // Markdown content
echo $page->getTitle();   // Page title
echo $page->hasChildren() ? 'Has children' : 'No children';
```

### Working with Child Pages

```php
// Fetch child pages
$childPages = $contentManager->fetchChildPages($pageId);

foreach ($childPages as $childPage) {
    echo "Child: {$childPage->getTitle()}\n";
}

// Fetch page with all child content
$fullPage = $contentManager->fetchPageWithChildren($pageId, true, true);
```

### Working with Databases

```php
// Convert database to markdown table
$tableMarkdown = $databaseTable->fetchDatabaseAsMarkdownTable($databaseId);
echo $tableMarkdown;

// Fetch database items as Page objects
$items = $databaseTable->fetchDatabaseItems($databaseId);
foreach ($items as $item) {
    echo "Item: {$item->getTitle()}\n";
    echo "Content: {$item->getContent()}\n";
}
```

## Object Integration

### Enhanced Page Object

The Page object now includes methods to work directly with ContentManager:

```php
$page = new Page(['id' => $pageId]);

// Fetch content and children
$page->fetchContent($contentManager);
$page->fetchChildPages($contentManager);
$page->fetchChildDatabases($contentManager);

// Check what was fetched
if ($page->hasContent()) {
    echo $page->getContent();
}

if ($page->hasChildPages()) {
    foreach ($page->getChildPages() as $childPage) {
        echo "Child: {$childPage->getTitle()}\n";
    }
}
```

### Enhanced Database Object

The Database object can now fetch its content in multiple formats:

```php
$database = new Database(['id' => $databaseId]);

// Fetch as markdown table
$database->fetchAsTable($databaseTable);
echo $database->getTableContent();

// Fetch items with full content
$database->fetchItems($databaseTable);
foreach ($database->getItems() as $item) {
    echo "Item: {$item->getTitle()}\n";
}
```

## Advanced Features

### Custom Block Handling

The ContentManager gracefully handles unsupported block types:

```php
// Unsupported blocks are converted to comments
// Output: <!-- Unsupported block type: custom_block -->
```

### Registry Pattern

The BlockRegistry follows the registry pattern for clean adapter resolution:

```php
$adapter = $registry->resolve('paragraph');
$markdown = $adapter->toMarkdown($blockData);
```

### Database Table Customization

The DatabaseTable service intelligently selects columns for display:

- Prioritizes important property types (title, rich_text, url, date, etc.)
- Limits to first 5 columns for readability
- Handles various Notion property types with proper formatting

## Error Handling

All methods include proper error handling:

```php
try {
    $page = $contentManager->fetchPageContent($pageId);
} catch (Exception $e) {
    echo "Error fetching page: {$e->getMessage()}";
}
```

## Performance Considerations

- Child pages are fetched recursively, which may take time for large page trees
- Database queries can be large; consider pagination for production use
- The system caches adapter instances for efficiency

## Examples

See `content-manager-example.php` for a complete working example of all features.