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
View::setFacadeApplication($container);

// Load configuration
$container->instance('config', [
    'md-notion' => include __DIR__.'/config/md-notion.php'
]);

// Set up the config accessor
$container->singleton('config', function () use ($container) {
    return new class($container['config']) {
        private $config;
        
        public function __construct($config) {
            $this->config = $config;
        }
        
        public function get($key, $default = null) {
            $keys = explode('.', $key);
            $value = $this->config;
            
            foreach ($keys as $k) {
                if (!isset($value[$k])) {
                    return $default;
                }
                $value = $value[$k];
            }
            
            return $value;
        }
    };
});

// Register the MdNotionServiceProvider services
$serviceProvider = new MdNotionServiceProvider($container);
$serviceProvider->packageRegistered();

// Initialize the real Notion SDK with token
$token = include __DIR__.'/notion-token.php';

// Override the Notion service with actual token
$container->singleton(Notion::class, function () use ($token) {
    return new Notion($token, '2025-09-03');
});

// Page ID to fetch (replace with your actual page ID)
$pageId = '263d9316605a806f9e95e1377a46ff3e'; // Example page ID

echo "Fetching full page content with MdNotion facade...\n";

try {
    // Use the facade to get full page content
    $markdown = MdNotion::make($pageId)->full();

    echo "Page fetched successfully!\n";
    echo 'Total markdown length: '.strlen($markdown)." characters\n\n";

    // Save to file
    $filename = __DIR__.'/notion-full.md';
    file_put_contents($filename, $markdown);

    echo "Complete page content exported to: notion-full.md\n";
    
    // Also demonstrate other API methods
    echo "\nTesting other API methods:\n";
    
    // Get pages collection
    $pages = MdNotion::make($pageId)->pages();
    echo "Child pages count: ".$pages->count()."\n";
    
    // Get databases collection  
    $databases = MdNotion::make($pageId)->databases();
    echo "Child databases count: ".$databases->count()."\n";
    
    // Get Page object with specific content
    $page = MdNotion::make($pageId)->content()->withPages()->withDatabases()->get();
    echo "Page title: ".$page->getTitle()."\n";
    
    // Get Page as Markdown with specific content
    $contentMarkdown = MdNotion::make($pageId)->content()->withPages()->withDatabases()->read();
    echo "Content markdown length: ".strlen($contentMarkdown)." characters\n";
    
    // Demonstrate setPage method
    $mdNotion = MdNotion::make();
    $fullMarkdown = $mdNotion->setPage($pageId)->full();
    echo "Using setPage method - markdown length: ".strlen($fullMarkdown)." characters\n";

} catch (Exception $e) {
    echo 'Error: '.$e->getMessage()."\n";
    echo "Make sure you have:\n";
    echo "1. A valid notion-token.php file with your Notion integration token\n";
    echo "2. A valid page ID (currently using: $pageId)\n";
    echo "3. Proper permissions for the integration to access the page\n";
    echo "4. The storage/views directory exists with write permissions\n";
}