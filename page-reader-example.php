<?php

require_once __DIR__.'/vendor/autoload.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\View;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\DatabaseTable;
use RedberryProducts\MdNotion\Services\PageReader;

// Set up Laravel container
$container = new Container;
Container::setInstance($container);

// Set up Facade root
Facade::setFacadeApplication($container);

// Register filesystem
$container->singleton('files', fn () => new Filesystem);

// Register blade compiler
$container->singleton('blade.compiler', function ($app) {
    return new BladeCompiler(
        $app['files'],
        __DIR__.'/storage/views'
    );
});

// Set up view finder
$viewFinder = new FileViewFinder(
    $container['files'],
    [__DIR__.'/resources/views']
);

// Add namespace for our views
$viewFinder->addNamespace('md-notion', __DIR__.'/resources/views');

// Set up view factory
$resolver = new EngineResolver;
$resolver->register('blade', function () use ($container) {
    return new CompilerEngine($container['blade.compiler']);
});

$factory = new Factory(
    $resolver,
    $viewFinder,
    new Dispatcher($container)
);

// Bind view factory to container
$container->instance('view', $factory);
View::setFacadeApplication($container);

// Initialize the real Notion SDK with token
$token = include __DIR__.'/notion-token.php';
$notion = new Notion($token, '2025-09-03');

// Create services
$mdNotionConfig = include __DIR__.'/config/md-notion.php';
$blockRegistry = new BlockRegistry(new BlockAdapterFactory($notion, $mdNotionConfig['adapters'] ?? []));
$databaseTable = new DatabaseTable;
$pageReader = new PageReader($notion, $blockRegistry);
$databaseReader = new DatabaseReader($notion, $databaseTable);

// Page ID to fetch (replace with your actual page ID)
$pageId = '263d9316605a806f9e95e1377a46ff3e'; // Example page ID

echo "Fetching page content with PageReader...\n";

try {
    // Read page with all content and children in one call
    $page = $pageReader->read($pageId);

    echo "Page fetched successfully!\n";
    echo 'Page title: '.$page->getTitle()."\n";
    echo 'Has child pages: '.($page->hasChildPages() ? 'Yes ('.$page->getChildPages()->count().')' : 'No')."\n";
    echo 'Has child databases: '.($page->hasChildDatabases() ? 'Yes ('.$page->getChildDatabases()->count().')' : 'No')."\n";
    echo 'Content length: '.strlen($page->getContent() ?? '')." characters\n\n";

    // Read content for child databases if any
    if ($page->hasChildDatabases()) {
        echo "Reading child databases content...\n";
        $page->readChildDatabasesContent($databaseReader);
        echo "Child databases content loaded.\n\n";
    }

    // Read content for child pages if any
    if ($page->hasChildPages()) {
        echo "Reading child pages content...\n";
        $page->readChildPagesContent($pageReader);
        echo "Child pages content loaded.\n\n";
    }

    // Build complete markdown content
    $markdown = '';

    // Add main page title and content
    $markdown .= $page->renderTitle(1)."\n\n";
    if ($page->hasContent()) {
        $markdown .= $page->getContent()."\n\n";
    }

    // Add child databases content
    if ($page->hasChildDatabases()) {
        $markdown .= "## Databases\n\n";
        foreach ($page->getChildDatabases() as $database) {
            $markdown .= $database->renderTitle(3)."\n\n";
            if ($database->hasTableContent()) {
                $markdown .= $database->getTableContent()."\n\n";
            }
        }
    }

    // Add child pages content
    if ($page->hasChildPages()) {
        $markdown .= "## Child Pages\n\n";
        foreach ($page->getChildPages() as $childPage) {
            $markdown .= $childPage->renderTitle(3)."\n\n";
            if ($childPage->hasContent()) {
                $markdown .= $childPage->getContent()."\n\n";
            }
        }
    }

    // Save to file
    $filename = __DIR__.'/notion-page.md';
    file_put_contents($filename, $markdown);

    echo "Complete page content exported to: notion-page.md\n";
    echo 'Total markdown length: '.strlen($markdown)." characters\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo "Make sure you have:\n";
    echo "1. A valid notion-token.php file with your Notion integration token\n";
    echo "2. A valid page ID (currently using: $pageId)\n";
    echo "3. Proper permissions for the integration to access the page\n";
}
