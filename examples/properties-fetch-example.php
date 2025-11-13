<?php

require_once __DIR__.'/../vendor/autoload.php';

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
use Redberry\MdNotion\Adapters\BlockAdapterFactory;
use Redberry\MdNotion\SDK\Notion;
use Redberry\MdNotion\Services\BlockRegistry;
use Redberry\MdNotion\Services\DatabaseReader;
use Redberry\MdNotion\Services\DatabaseTable;
use Redberry\MdNotion\Services\PageReader;
use Redberry\MdNotion\Services\PropertiesTable;

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
        __DIR__.'/../storage/views'
    );
});

// Set up view finder
$viewFinder = new FileViewFinder(
    $container['files'],
    [__DIR__.'/../resources/views']
);

// Add namespace for our views
$viewFinder->addNamespace('md-notion', __DIR__.'/../resources/views');

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
$token = include __DIR__.'/../notion-token.php';
$notion = new Notion($token, '2025-09-03');

// Create services
$mdNotionConfig = include __DIR__.'/../config/md-notion.php';
$blockRegistry = new BlockRegistry(new BlockAdapterFactory($notion, $mdNotionConfig['adapters'] ?? []));
$databaseTable = new DatabaseTable;
$pageReader = new PageReader($notion, $blockRegistry);
$databaseReader = new DatabaseReader($notion, $databaseTable);
$propertiesTable = new PropertiesTable;

// Bind services to container so Page objects can use them
$container->instance(PropertiesTable::class, $propertiesTable);

// Use a page ID that has properties (replace with your actual page ID)
// This should be a page from your Notion workspace that has various properties
$pageId = '263d9316605a80c0a8fbfd152e79b9d8'; // Replace with a page that has properties

echo "Fetching page content with properties...\n";

try {
    // Read page
    $page = $pageReader->read($pageId);

    echo "Page fetched successfully!\n";
    echo 'Page title: '.$page->getTitle()."\n";
    echo 'Has properties: '.($page->hasProperties() ? 'Yes ('.count($page->getProperties()).')' : 'No')."\n";
    echo 'Content length: '.strlen($page->getContent() ?? '')." characters\n\n";

    // Build markdown content
    $markdown = '';

    // Add page title
    $markdown .= $page->renderTitle(1)."\n\n";

    // Add properties table if available
    if ($page->hasProperties()) {
        $markdown .= $page->renderPropertiesTable()."\n";
    }

    // Add page content
    if ($page->hasContent()) {
        $markdown .= $page->getContent()."\n\n";
    }

    // Save to file
    file_put_contents(__DIR__.'/properties-example.md', $markdown);

    echo "Page with properties table converted and saved to examples/properties-example.md\n";
    echo "\nGenerated markdown:\n";
    echo "---\n";
    echo $markdown;
    echo "---\n";

} catch (\Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo "Stack trace:\n";
    echo $e->getTraceAsString()."\n";
}
