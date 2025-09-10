<?php

/**
 * MdNotion Facade Example Script
 * This script demonstrates the usage of the MdNotion facade with all its methods.
 * It fetches a complete Notion page with all nested content and saves it as markdown.
 *
 * Requirements:
 * - notion-token.php file with your Notion integration token
 * - storage/views directory with write permissions
 * - Valid page ID with proper integration permissions
 */

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
use RedberryProducts\MdNotion\Facades\MdNotion;
use RedberryProducts\MdNotion\MdNotionServiceProvider;
use RedberryProducts\MdNotion\SDK\Notion;

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
$container->bind('Illuminate\Contracts\View\Factory', function () use ($factory) {
    return $factory;
});
View::setFacadeApplication($container);

// Load configuration
$container->instance('config', [
    'md-notion' => include __DIR__.'/config/md-notion.php',
]);

function config($key = null, $default = null)
{
    if (is_null($key)) {
        return app('config');
    }

    return app('config')[$key] ?? $default;
}

function app($abstract = null, array $parameters = [])
{
    if (is_null($abstract)) {
        return Container::getInstance();
    }

    return Container::getInstance()->make($abstract, $parameters);
}

// Register the MdNotionServiceProvider services
$serviceProvider = new MdNotionServiceProvider($container);
$serviceProvider->packageRegistered();

// Initialize the real Notion SDK with token
$token = include __DIR__.'/notion-token.php';

// Override the Notion service with actual token
$container->singleton(Notion::class, function () use ($token) {
    return new Notion($token, '2025-09-03');
});

// Create storage directory if it doesn't exist
if (! file_exists(__DIR__.'/storage/views')) {
    mkdir(__DIR__.'/storage/views', 0755, true);
}

// Page ID to fetch (replace with your actual page ID)
$pageId = '263d9316605a806f9e95e1377a46ff3e'; // Example page ID

echo "Fetching full page content with MdNotion facade...\n";

try {
    // Create a single instance of MdNotion
    $mdNotion = MdNotion::make($pageId);

    echo "Fetching full page content...\n";
    
    // Get full content and immediately save to file to free memory
    $filename = __DIR__.'/notion-full.md';
    file_put_contents($filename, $mdNotion->full());
    
    echo "Complete page content exported to: notion-full.md\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo "Make sure you have:\n";
    echo "1. A valid notion-token.php file with your Notion integration token\n";
    echo "2. A valid page ID (currently using: $pageId)\n";
    echo "3. Proper permissions for the integration to access the page\n";
    echo "4. The storage/views directory exists with write permissions\n";
}
