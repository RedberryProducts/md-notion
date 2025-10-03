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
use Redberry\MdNotion\Adapters\ColumnListAdapter;

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
use Redberry\MdNotion\SDK\Notion;

// Use actual block ID from Notion
$columnListBlock = [
    'object' => 'block',
    'id' => '263d9316-605a-8057-b12e-f880bc565fcb',
    'type' => 'column_list',
    'has_children' => true,
    'column_list' => [],
];

// Initialize the real Notion SDK with token
$token = include __DIR__.'/notion-token.php';
$notion = new Notion($token, '2025-09-03');

// Create and configure the adapter
$adapter = new ColumnListAdapter;
$adapter->setSdk($notion);

// Convert to markdown
$markdown = $adapter->toMarkdown($columnListBlock);

// Save to file
file_put_contents(__DIR__.'/column-list.md', $markdown);

echo "Column list block converted and saved to column-list.md\n";
