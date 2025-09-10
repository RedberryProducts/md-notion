# Read your notion pages as Markdown

[![Latest Version on Packagist](https://img.shields.io/packagist/v/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)

Read your notion pages as Markdown in Laravel applications

Example:

```php
use RedberryProducts\MdNotion\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e';

// Get page content as markdown
$markdown = MdNotion::make($pageId)->content()->read();

// Get complete recursive content 
$fullContent = MdNotion::make($pageId)->full();
```

Don't forget to star the repo ⭐

## Installation

You can install the package via composer:

```bash
composer require redberryproducts/md-notion
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="md-notion-migrations"
php artisan migrate
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
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \RedberryProducts\MdNotion\Adapters\HeadingAdapter::class,
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

## Usage

### Basic Page Content

Get the content of a single page as markdown:

```php
use RedberryProducts\MdNotion\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e';
$content = MdNotion::make($pageId)->content()->read();

// Returns: "# Page Title\n\nPage content as markdown..."
```

### Fetch Child Pages

Get collection of child pages:

```php
$pages = MdNotion::make($pageId)->pages();

// Returns: Collection of Page objects
// Each page has: id, title, content, created_time, last_edited_time
```

### Fetch Child Databases  

Get collection of child databases:

```php
$databases = MdNotion::make($pageId)->databases();

// Returns: Collection of Database objects  
// Each database has: id, title, description, properties, and table content
```

### Content with Children

Get page content including child pages and databases:

```php
// Include child pages in content
$content = MdNotion::make($pageId)
    ->content()
    ->withPages()
    ->read();

// Include child databases as tables
$content = MdNotion::make($pageId)
    ->content() 
    ->withDatabases()
    ->read();

// Include both child pages and databases
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
// ⚠️  WARNING: This may make many API requests for pages with deep nesting!
```

### Dynamic Page Setting

Set page ID dynamically:

```php
$mdNotion = MdNotion::make(); // Empty initially
$content = $mdNotion->setPage($pageId)->full();

// Useful when page ID is determined at runtime
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

## Customization

### Custom Blade Templates

You can customize how markdown is rendered by creating your own Blade templates:

#### Publish the default templates:

```bash
php artisan vendor:publish --tag="md-notion-views"
```

#### Customize the config to use your templates:

```php
// config/md-notion.php
return [
    'templates' => [
        'page_markdown' => 'custom.page-template',     // For content()->read()
        'full_markdown' => 'custom.full-template',     // For full()
    ],
];
```

#### Create your custom templates:

**resources/views/custom/page-template.blade.php:**
```php
{!! $current_page['title'] !!}

@if($current_page['hasContent'])
{!! $current_page['content'] !!}
@endif

@if($hasChildDatabases)
## 📊 My Custom Database Section
@foreach($child_databases as $database)
**{!! strip_tags($database['title']) !!}**
{!! $database['table_content'] !!}
@endforeach
@endif

@if($hasChildPages)  
## 📄 Related Pages
@foreach($child_pages as $page)
### {!! strip_tags($page['title']) !!}
{!! $page['content'] !!}
@endforeach
@endif
```

**resources/views/custom/full-template.blade.php:**
```php
{{-- Create your recursive template with custom styling --}}
@php
function renderCustomMarkdown($data) {
    $markdown = "# 🎯 " . strip_tags($data['current_page']['title']) . "\n\n";
    
    if ($data['current_page']['hasContent']) {
        $markdown .= $data['current_page']['content'] . "\n\n";
    }
    
    // Add your custom rendering logic here...
    
    return $markdown;
}

echo renderCustomMarkdown(get_defined_vars());
@endphp
```

### Custom Block Adapters

Create custom adapters to handle specific Notion block types:

#### 1. Create a custom adapter:

```php
// app/Adapters/CustomCalloutAdapter.php
<?php

namespace App\Adapters;

use RedberryProducts\MdNotion\Adapters\BlockAdapterInterface;

class CustomCalloutAdapter implements BlockAdapterInterface
{
    public function handle(array $block): string
    {
        $text = $block['callout']['rich_text'][0]['plain_text'] ?? '';
        $icon = $block['callout']['icon']['emoji'] ?? '💡';
        
        return "> {$icon} **Note:** {$text}\n\n";
    }

    public function supports(string $blockType): bool
    {
        return $blockType === 'callout';
    }
}
```

#### 2. Register your adapter in the configuration:

```php
// config/md-notion.php
return [
    'adapters' => [
        'callout' => \App\Adapters\CustomCalloutAdapter::class,
        // Keep existing adapters...
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \RedberryProducts\MdNotion\Adapters\HeadingAdapter::class,
        // ... other adapters
    ],
];
```

#### Example: Custom Table Adapter

```php
// app/Adapters/CustomTableAdapter.php
class CustomTableAdapter implements BlockAdapterInterface
{
    public function handle(array $block): string
    {
        $rows = $block['table']['children'] ?? [];
        $markdown = "\n### 📋 Data Table\n\n";
        
        foreach ($rows as $index => $row) {
            $cells = array_map(fn($cell) => 
                $cell['rich_text'][0]['plain_text'] ?? '', 
                $row['table_row']['cells']
            );
            
            $markdown .= '| ' . implode(' | ', $cells) . " |\n";
            
            if ($index === 0) {
                $markdown .= '|' . str_repeat(' --- |', count($cells)) . "\n";
            }
        }
        
        return $markdown . "\n";
    }

    public function supports(string $blockType): bool
    {
        return $blockType === 'table';
    }
}
```

### Available Template Variables

When customizing templates, you have access to these variables:

#### For page templates (`content()->read()`):
- `$current_page` - Array with `title`, `content`, `hasContent`
- `$child_databases` - Array of database data with `title`, `table_content`, `hasTableContent`  
- `$child_pages` - Array of page data with `title`, `content`, `hasContent`
- `$withDatabases` - Boolean flag
- `$withPages` - Boolean flag
- `$hasChildDatabases` - Boolean flag
- `$hasChildPages` - Boolean flag

#### For full templates (`full()`):
- Complete recursive data structure with nested `current_page`, `child_databases`, `child_pages`
- Each level includes `hasChildDatabases`, `hasChildPages`, `level` for depth tracking
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
