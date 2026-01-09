# Read your notion pages as Markdown

[![Latest Version on Packagist](https://img.shields.io/packagist/v/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)

Read your notion pages as Markdown in Laravel applications

Example:

```php
use Redberry\MdNotion\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e';

// Get page content as markdown
$markdown = MdNotion::make($pageId)->content()->read();

// Get complete recursive content
$fullContent = MdNotion::make($pageId)->full();
```

Don't forget to star the repo ⭐

#### Table of contents

-   [Installation](#installation)
-   [Configuration](#configuration)
-   [Features](#features)
-   [Usage](#usage)
-   [Page Size & Pagination](#page-size--pagination)
-   [Error Handling](#error-handling)
-   [Page and Database Objects API](#page-and-database-objects-api)
-   [Customization](#customization)
-   [Testing](#testing)
-   [Security Vulnerabilities](#security-vulnerabilities)
-   [Credits](#credits)
-   [License](#license)

## Installation

You can install the package via composer:

```bash
composer require redberry/md-notion
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="md-notion-config"
```

This is the contents of the published config file:

```php
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
     * Customize these to use your own adapters.
     */
    'adapters' => [
        'paragraph' => \Redberry\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \Redberry\MdNotion\Adapters\HeadingAdapter::class,
        // ... many more block adapters
    ],
];
```

Optionally, you can publish the views:

```bash
php artisan vendor:publish --tag="md-notion-views"
```

## Configuration

Set your Notion API key in your `.env` file:

```env
NOTION_API_KEY=your_notion_api_key_here
```

To get your Notion API key:

1. Go to [Notion Developers](https://developers.notion.com/)
2. Create a new integration
3. Copy the API key
4. Add the integration to your Notion pages you want to read

## Features

🔄 **Fluent API** - Chain methods for intuitive content fetching  
📄 **Page Reading** - Extract Notion pages as clean markdown  
🗃️ **Database Support** - Convert Notion databases to markdown tables  
🌲 **Recursive Fetching** - Get all nested pages and databases in one call  
🎨 **Customizable Templates** - Use Blade templates for markdown output  
🧩 **Custom Adapters** - Extend block adapters for specialized content  
⚡ **Laravel Integration** - Seamless service provider and facade support  
🛠️ **Configurable** - Easy configuration via Laravel config files  
📊 **Pagination Support** - Automatic pagination for large pages (100+ blocks)  
🚨 **Error Handling** - Typed exceptions for Notion API errors

## Usage

### Basic Page Content

Get the content of a single page as markdown:

```php
use Redberry\MdNotion\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e';
$content = MdNotion::make($pageId)->content()->read();

// Returns: "# Page Title\n\nPage content as markdown..."
```

### Fetch Child Pages

Get collection of child pages:

```php
$pages = MdNotion::make($pageId)->pages();

// Returns: Collection of Page objects
```

_note: Each page has: id, title, content, created_time, last_edited_time, etc. Check [API reference](#page-and-database-objects-api)_

### Fetch Child Databases

Get collection of child databases:

```php
$databases = MdNotion::make($pageId)->databases();

// Returns: Collection of Database objects
```

_Each database has: id, title, description, properties, and table content, etc. Check [API reference](#page-and-database-objects-api)_

### Content with Children

Get page content including child pages and databases:

```php
// Include child pages in Markdown content
$content = MdNotion::make($pageId)
    ->content()
    ->withPages()
    ->read();

// Include child databases as tables in Markdown content
$content = MdNotion::make($pageId)
    ->content()
    ->withDatabases()
    ->read();

// Include both child pages and databases in Markdown content
$content = MdNotion::make($pageId)
    ->content()
    ->withPages()
    ->withDatabases()
    ->read();

// Returns: Formatted markdown with main content + child content sections
```

### Complete Recursive Content

Get everything recursively (current page + all nested pages and databases):

```php
$fullContent = MdNotion::make($pageId)->full();

// Returns: Complete markdown with all nested content
```

`full` is the only method which by default returns database item's content as well. It performs minimum one request per page, so pages with nested content or big databases can hit the memory limits, can be slow or hit limits of notion API.

_⚠️ WARNING: This may make many API requests for pages with deep nesting!_

### Dynamic Page Setting

Set page ID dynamically:

```php
$mdNotion = MdNotion::make(); // Empty initially
$content = $mdNotion->setPage($pageId)->full();

```

### Get Page Objects

Access the raw Page object with all data:

```php
$page = MdNotion::make($pageId)
    ->content()
    ->withPages()
    ->withDatabases()
    ->get();

// Returns: Page object with loaded child content
// Access: $page->getTitle(), $page->getContent(), $page->getChildPages(), etc.
```

## Page Size & Pagination

The Notion API limits responses to 100 blocks per request. This package handles pagination automatically, allowing you to fetch more blocks seamlessly.

### Configuration

Set the default page size in your `.env` file:

```env
NOTION_DEFAULT_PAGE_SIZE=100
```

Or in the config file:

```php
// config/md-notion.php
return [
    'default_page_size' => env('NOTION_DEFAULT_PAGE_SIZE', 100),
    // ...
];
```

### Custom Page Size

You can override the default page size per request:

```php
use Redberry\MdNotion\Facades\MdNotion;

// Fetch up to 50 blocks
$content = MdNotion::make($pageId)->content()->read(50);

// Fetch up to 200 blocks (automatically paginated)
$content = MdNotion::make($pageId)->content()->read(200);

// Use default from config
$content = MdNotion::make($pageId)->content()->read();
```

### How Pagination Works

-   **Page size ≤ 100**: Single API request
-   **Page size > 100**: Automatic pagination with multiple requests

The returned data always has a consistent structure:

```php
[
    'results' => [...],      // Array of blocks
    'has_more' => bool,      // Whether more items exist
    'next_cursor' => ?string // Cursor for manual continuation (null if results were trimmed)
]
```

> **Note**: When results are trimmed to meet your requested limit, `next_cursor` is set to `null` to prevent accidentally skipping items. The `has_more` flag will still indicate if more items exist.

### Validation

Page size must be a positive integer. Invalid values will throw an exception:

```php
// These will throw InvalidArgumentException:
MdNotion::make($pageId)->content()->read(0);   // Zero not allowed
MdNotion::make($pageId)->content()->read(-5);  // Negative not allowed
```

## Error Handling

The package provides a dedicated `NotionApiException` for handling Notion API errors with detailed information.

### Basic Error Handling

```php
use Redberry\MdNotion\Facades\MdNotion;
use Redberry\MdNotion\SDK\Exceptions\NotionApiException;

try {
    $content = MdNotion::make($pageId)->content()->read();
} catch (NotionApiException $e) {
    // Get error details
    echo $e->getMessage();           // "Notion API Error [404] object_not_found: Could not find page..."
    echo $e->getNotionCode();        // "object_not_found"
    echo $e->getNotionMessage();     // "Could not find page with ID: ..."
    
    // Access the original response
    $response = $e->getResponse();
    $statusCode = $response->status(); // 404
}
```

### Error Type Checking

The exception provides convenient methods to check error types:

```php
try {
    $content = MdNotion::make($pageId)->content()->read();
} catch (NotionApiException $e) {
    if ($e->isNotFound()) {
        // Page doesn't exist or not shared with integration
    }
    
    if ($e->isUnauthorized()) {
        // Invalid API key
    }
    
    if ($e->isForbidden()) {
        // Integration doesn't have access to this resource
    }
    
    if ($e->isRateLimited()) {
        // Too many requests, implement backoff
    }
    
    if ($e->isValidationError()) {
        // Invalid request parameters
    }
    
    if ($e->isServerError()) {
        // Notion server error (5xx)
    }
    
    if ($e->isRetryable()) {
        // Safe to retry (rate limits, server errors, conflicts)
    }
}
```

### Notion Error Codes

The `getNotionCode()` method returns one of these values:

| Code | HTTP Status | Description |
|------|-------------|-------------|
| `invalid_json` | 400 | Request body is not valid JSON |
| `invalid_request_url` | 400 | Invalid request URL |
| `invalid_request` | 400 | Invalid request parameters |
| `validation_error` | 400 | Request validation failed |
| `missing_version` | 400 | Missing Notion-Version header |
| `unauthorized` | 401 | Invalid API key |
| `restricted_resource` | 403 | No access to resource |
| `object_not_found` | 404 | Resource not found |
| `conflict_error` | 409 | Transaction conflict |
| `rate_limited` | 429 | Too many requests |
| `internal_server_error` | 500 | Notion server error |
| `bad_gateway` | 502 | Bad gateway |
| `service_unavailable` | 503 | Service temporarily unavailable |
| `gateway_timeout` | 504 | Gateway timeout |

### Retry Strategy Example

```php
use Redberry\MdNotion\SDK\Exceptions\NotionApiException;

function fetchWithRetry(string $pageId, int $maxRetries = 3): string
{
    $attempts = 0;
    
    while ($attempts < $maxRetries) {
        try {
            return MdNotion::make($pageId)->content()->read();
        } catch (NotionApiException $e) {
            if (!$e->isRetryable()) {
                throw $e; // Don't retry non-retryable errors
            }
            
            $attempts++;
            if ($attempts >= $maxRetries) {
                throw $e;
            }
            
            // Exponential backoff
            $delay = $e->isRateLimited() ? 1000 : 500;
            usleep($delay * $attempts * 1000);
        }
    }
}
```

## Page and Database Objects API

The `MdNotion` package provides rich object models for working with Notion pages and databases. Both `Page` and `Database` objects extend `BaseObject` and use several traits to provide comprehensive functionality.

### Page Object API

#### Basic Properties and Methods

```php
use Redberry\MdNotion\Objects\Page;

// Create from data
$page = Page::from([
    'id' => 'page-id-123',
    'title' => 'My Page Title',
    'content' => '# Page content...',
    'has_children' => true
]);

// Core properties
$page->getId();                    // string - Page ID
$page->getTitle();                 // string - Page title
$page->getContent();               // ?string - Page content
$page->hasContent();               // bool - Whether page has content
$page->hasChildren();              // bool - Whether page has child pages
```

#### Content Management

```php
// Content operations
$page->setContent('# New content');
$page->getContent();               // Returns MD string: '# New content'
$page->hasContent();               // Returns: true

// Child database
$databases = $page->getChildDatabases();  // Collection<Database>
```

#### Fetching and Updating

When page is accessed as child page of another, it may not contain all information you need, including child pages, markdown content and etc. To get the needed data, you can use ID with MdNotion again or use `fetch` method on page instance.

```php
// Fetch latest data from Notion API
$page->fetch();         // Updates current instance with fresh data

// The fetch method preserves object identity
$originalPage = Page::from(['id' => 'page-123']);
$updatedPage = $originalPage->fetch();
// $originalPage === $updatedPage (same page, updated data)
```

### Database Object API

#### Basic Properties and Methods

```php
use Redberry\MdNotion\Objects\Database;

// Create from data
$database = Database::from([
    'id' => 'db-id-123',
    'title' => 'My Database',
    'tableContent' => '| Name | Status |\n|------|--------|\n| Task 1 | Done |'
]);

// Core properties
$database->getId();                // string - Database ID
$database->getTitle();             // string - Database title
$database->getTableContent();      // ?string - Database as markdown table
$database->hasTableContent();      // bool - Whether has table content
```

#### Table Content Management

```php
// Table content operations
$database->getTableContent();      // Returns markdown table string
$database->hasTableContent();      // Returns true if table content exists

// Read items content (populate child pages with content)
$database->readItemsContent();
```

#### Fetching and Updating

```php
// Fetch latest data from Notion API
$database->fetch(); // Updates current instance with fresh data
```

### Shared API (from BaseObject & Traits)

Both `Page` and `Database` objects share these APIs through inheritance and traits:

#### Title Management (HasTitle trait)

```php
// Title operations
$object->getTitle();               // string - Get title
$object->setTitle('New Title');    // Set title
$object->renderTitle(1);           // string - Render as markdown heading (# Title)
$object->renderTitle(2);           // string - Render as level 2 heading (## Title)
$object->renderTitle(3);           // string - Render as level 3 heading (### Title)
```

#### Icon Management (HasIcon trait)

```php
// Icon operations
$object->getIcon();                // ?array - Get icon data
$object->setIcon($iconData);       // Set icon data
$object->hasIcon();                // bool - Whether has icon
$object->processIcon();            // string - Get icon as emoji/markdown

// Icon types supported:
// - Emoji: Returns emoji character
// - External: Returns [IconName](url) markdown link
// - File: Returns [🔗](url) markdown link
```

#### Metadata Management (HasMeta trait)

```php
// Timestamps
$object->getCreatedTime();         // string - ISO timestamp
$object->getLastEditedTime();      // string - ISO timestamp

// User information
$object->getCreatedBy();           // array - User data who created
$object->getLastEditedBy();        // array - User data who last edited

// Status flags
$object->isArchived();             // bool - Whether archived
$object->isTrashed();              // bool - Alias for isInTrash()
```

#### Parent Relationship (HasParent trait)

```php
// Parent operations
$object->getParent();              // array - Full parent data
$object->hasParent();              // bool - Whether has parent
$object->getParentType();          // ?string - Parent type (page_id, workspace, etc.)
$object->getParentId();            // ?string - Parent ID based on type
```

#### Child Pages Management (HasChildPages trait)

```php
// Child pages operations
$object->getChildPages();          // Collection<Page> - Child pages collection
$object->hasChildPages();          // bool - Whether has child pages
```

#### URLs and Properties

```php
// URL management
$object->getUrl();                 // ?string - Notion URL
$object->hasUrl();                 // bool - Whether has URL
$object->getPublicUrl();           // ?string - Public sharing URL
$object->hasPublicUrl();           // bool - Whether has public URL

// Properties management
$object->getProperties();          // array - All Notion properties
$object->hasProperties();          // bool - Whether has properties
$object->getProperty('title');     // mixed - Get specific property
```

#### Serialization and Data Conversion

```php
// Convert to array
$data = $object->toArray();        // Complete object data as array

// Fill from array (merge new data)
$object->fill($newData);           // Updates object with new data, preserves existing

// Static creation
$object = Page::from($data);       // Create new instance from data array
$object = Database::from($data);   // Create new instance from data array
```

### Advanced Usage Examples

#### Working with Object Identity

```php
// Objects maintain identity during fetch operations
$page = Page::from(['id' => 'page-123']);
$samePageReference = $page->fetch();

// $page === $samePageReference (same object instance)
// But $page now has updated content from Notion API
```

#### Child Content Management

```php
// Page with child databases
$childDbs = $page->getChildDatabases();

// Database with child pages
$childPages = $database->getChildPages();

// Read all child page content
$database->readItemsContent();
```

#### Partial Data Updates

```php
$page = Page::from([
    'id' => 'page-123',
    'title' => 'Original Title',
    'content' => 'Original content'
]);

// Partial update - only updates specified fields
$page->fill([
    'title' => 'Updated Title'
    // content remains "Original content"
]);

echo $page->getTitle();   // "Updated Title"
echo $page->getContent(); // "Original content" (preserved)
```

## Customization

### Custom Blade Templates

You can customize how markdown is rendered by creating your own Blade templates:

#### Create your custom templates

To change the layout or logic how `read` or `full` methods render the markdown, you should create a new view and replace them in config:

```php
// config/md-notion.php
return [
    'templates' => [
        'page_markdown' => 'custom.page-template',     // For content()->read()
        'full_markdown' => 'custom.full-template',     // For full()
    ],
];
```

When customizing templates, you have access to these variables:

##### For page templates (`content()->read()`):

-   `$current_page` - Array with `title`, `content`, `hasContent`
-   `$child_databases` - Array of database data with `title`, `table_content`, `hasTableContent`
-   `$child_pages` - Array of page data with `title`, `content`, `hasContent`
-   `$withDatabases` - Boolean flag
-   `$withPages` - Boolean flag
-   `$hasChildDatabases` - Boolean flag
-   `$hasChildPages` - Boolean flag

##### For full templates (`full()`):

-   Complete recursive data structure with nested `current_page`, `child_databases`, `child_pages`
-   Each level includes `hasChildDatabases`, `hasChildPages`, `level` for depth tracking

You can check current blade templates here:

-   `read()` Method: **resources\views\page-md.blade.php**
-   `full()` Method: **resources\views\full-md.blade.php**

### Custom Block Adapters

Create custom adapters to handle specific Notion block types:

#### 1. Create a custom adapter:

You will need adapter class extending `src\Adapters\BaseBlockAdapter.php` and custom blade template rendering the data.

```php
// app/Adapters/CustomCodeAdapter.php
<?php

namespace App\Adapters;

use Redberry\MdNotion\Adapters\BaseBlockAdapter;

class CustomCodeAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'code'; // Set the type
    }

    public function getTemplate(): string
    {
        return 'notion.blocks.code'; // Set blade view
    }

    // Main method which prepares data to pass to view
    protected function prepareData(array $block): array
    {
        $code = $block['code'];

        $content = $this->processRichText($code['richText']);
        $content = str_replace('\\n', "\n", $content);

        // You will have access to this variables in your blade template:
        return [
            'content' => $content,
            'language' => $code['language'],
            'caption' => $this->processRichText($dto->caption),
            'block' => $code,
        ];
    }
}
```

Check example: `src\Adapters\ParagraphAdapter.php`

#### 2. Register your adapter in the configuration:

```php
// config/md-notion.php
return [
    'adapters' => [
        'callout' => \App\Adapters\CustomCalloutAdapter::class,
        // Keep existing adapters...
        'paragraph' => \Redberry\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \Redberry\MdNotion\Adapters\HeadingAdapter::class,
        // ... other adapters
    ],
];
```

## Testing

```bash
composer test
```

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

Thanks to following people and projects:

-   [Revaz Gh. (Aka MaestroError)](https://github.com/maestroerror)
-   [Saloon](https://github.com/saloonphp/saloon)
-   [Saloon-sdk-generator](https://github.com/crescat-io/saloon-sdk-generator)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
