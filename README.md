# Read your notion pages as Markdown

[![Latest Version on Packagist](https://img.shields.io/packagist/v/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/redberryproducts/md-notion/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/redberryproducts/md-notion/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/redberryproducts/md-notion.svg?style=flat-square)](https://packagist.org/packages/redberryproducts/md-notion)

Read your notion pages as Markdown in Laravel applications

Example:

```php
use RedberryProducts\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e'
$MdNotion = MdNotion::make($pageId);

// Get content of current page as markdown string
$markdown = $MdNotion->content()->get();
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
];
```

Optionally:

```php
php artisan vendor:publish --tag=":md-notion-views"
```

## Usage

```php
use RedberryProducts\Facades\MdNotion;

$pageId = '263d9316605a806f9e95e1377a46ff3e'
$MdNotion = MdNotion::make($pageId);

// Get pages
$pages = $MdNotion->pages();

// Get page content as markdown string
$markdown = $MdNotion->content()->get();

// Get full content as array of pages with title, id and content in MD (current + child pages)
$markdown = $MdNotion->content()->withPages()->get();
$markdown = $MdNotion->content()->withDatabases()->get();
$markdown = $MdNotion->content()->withPages()->withDatabases()->get();

// Get content of current and child pages as whole MD string
$markdown = $MdNotion->full();

// Read all nested pages recursively (WARNING: Many API requests!)
$page->readAllPagesContent($pageReader); // Too many requests, may slow down your application or hit Notion API limits

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
