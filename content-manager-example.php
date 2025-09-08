<?php

/**
 * Example demonstrating ContentManager usage
 * This file shows how to use the new ContentManager functionality
 */

require_once __DIR__ . '/vendor/autoload.php';

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\ContentManager;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Services\DatabaseTable;
use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;

// Example configuration - in real usage, these would come from your environment
$notionApiKey = 'your-notion-api-key-here';
$pageId = 'your-page-id-here';
$databaseId = 'your-database-id-here';

// Initialize SDK and services
$notion = new Notion($notionApiKey, '2022-06-28');

// Load adapter configuration
$adapterMap = require __DIR__ . '/config/md-notion.php';
$adapters = $adapterMap['adapters'];

// Create services
$factory = new BlockAdapterFactory($notion, $adapters);
$registry = new BlockRegistry($factory);
$contentManager = new ContentManager($notion, $registry);
$databaseTable = new DatabaseTable($notion, $contentManager);

echo "=== ContentManager Example ===\n\n";

// Example 1: Fetch page content with markdown
echo "1. Fetching page content:\n";
try {
    $page = $contentManager->fetchPageContent($pageId);
    echo "Page ID: {$page->getId()}\n";
    echo "Page Title: {$page->getTitle()}\n";
    echo "Has Children: " . ($page->hasChildren() ? 'Yes' : 'No') . "\n";
    echo "Content Length: " . strlen($page->getContent() ?? '') . " characters\n";
    echo "Content Preview: " . substr($page->getContent() ?? '', 0, 100) . "...\n\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

// Example 2: Fetch child pages
echo "2. Fetching child pages:\n";
try {
    $childPages = $contentManager->fetchChildPages($pageId);
    echo "Found {$childPages->count()} child pages:\n";
    foreach ($childPages as $childPage) {
        echo "  - {$childPage->getId()}: {$childPage->getTitle()}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

// Example 3: Fetch child databases
echo "3. Fetching child databases:\n";
try {
    $childDatabases = $contentManager->fetchChildDatabases($pageId);
    echo "Found {$childDatabases->count()} child databases:\n";
    foreach ($childDatabases as $database) {
        echo "  - {$database->getId()}: {$database->getTitle()}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

// Example 4: Page object with ContentManager integration
echo "4. Using Page object with ContentManager:\n";
try {
    $page = new \RedberryProducts\MdNotion\Objects\Page(['id' => $pageId]);
    
    // Fetch content using the new methods
    $page->fetchContent($contentManager);
    $page->fetchChildPages($contentManager);
    $page->fetchChildDatabases($contentManager);
    
    echo "Page has content: " . ($page->hasContent() ? 'Yes' : 'No') . "\n";
    echo "Page has child pages: " . ($page->hasChildPages() ? 'Yes' : 'No') . "\n";
    echo "Page has child databases: " . ($page->hasChildDatabases() ? 'Yes' : 'No') . "\n\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

// Example 5: Database table functionality
echo "5. Database table functionality:\n";
try {
    $database = new \RedberryProducts\MdNotion\Objects\Database(['id' => $databaseId]);
    
    // Fetch as markdown table
    $database->fetchAsTable($databaseTable);
    echo "Table content preview:\n";
    echo substr($database->getTableContent() ?? '', 0, 200) . "...\n\n";
    
    // Fetch database items
    $database->fetchItems($databaseTable);
    echo "Database has {$database->getItems()->count()} items\n";
    foreach ($database->getItems()->take(3) as $item) {
        echo "  - {$item->getId()}: {$item->getTitle()}\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

// Example 6: Comprehensive page fetch with all children
echo "6. Comprehensive page fetch:\n";
try {
    $fullPage = $contentManager->fetchPageWithChildren($pageId, true, true);
    
    echo "Full page structure:\n";
    echo "- Main page: {$fullPage->getId()}\n";
    echo "- Content length: " . strlen($fullPage->getContent() ?? '') . " chars\n";
    echo "- Child pages: {$fullPage->getChildPages()->count()}\n";
    echo "- Child databases: {$fullPage->getChildDatabases()->count()}\n";
    
    // Show child pages with their content
    foreach ($fullPage->getChildPages() as $childPage) {
        echo "  - Child: {$childPage->getId()} (" . strlen($childPage->getContent() ?? '') . " chars)\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
}

echo "=== Example Complete ===\n";
echo "Note: Update the \$notionApiKey, \$pageId, and \$databaseId variables with real values to test.\n";