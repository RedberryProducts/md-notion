<?php

require_once __DIR__ . '/vendor/autoload.php';

use RedberryProducts\MdNotion\Adapters\TableAdapter;
use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Engines\CompilerEngine;
use Illuminate\View\Engines\EngineResolver;
use Illuminate\View\Factory;
use Illuminate\View\FileViewFinder;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\View;

// Set up Laravel container
$container = new Container;
Container::setInstance($container);

// Set up Facade root
Facade::setFacadeApplication($container);

// Register filesystem
$container->singleton('files', fn() => new Filesystem);

// Register blade compiler
$container->singleton('blade.compiler', function ($app) {
    return new BladeCompiler(
        $app['files'],
        __DIR__ . '/storage/views'
    );
});

// Set up view finder
$viewFinder = new FileViewFinder(
    $container['files'],
    [__DIR__ . '/resources/views']
);

// Add namespace for our views
$viewFinder->addNamespace('md-notion', __DIR__ . '/resources/views');

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
use RedberryProducts\MdNotion\SDK\Notion;

// Load table JSON
$tableJson = file_get_contents(__DIR__ . '/BlockJsonExamples/TableJson.json');
$tableBlock = json_decode($tableJson, true);

// Create mock children response based on example data
$childrenResponse = [
    'results' => [
        [
            'type' => 'table_row',
            'table_row' => [
                'cells' => [
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'Title'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'Title'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'type'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'type'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'date'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'date'
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'table_row',
            'table_row' => [
                'cells' => [
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'Title 1'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'Title 1'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'K'],
                            'annotations' => [
                                'bold' => true,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'K'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => '03.09.2025'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => '03.09.2025'
                        ]
                    ]
                ]
            ]
        ],
        [
            'type' => 'table_row',
            'table_row' => [
                'cells' => [
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'Title 2'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'Title 2'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => 'Y'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => true,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => 'Y'
                        ]
                    ],
                    [
                        [
                            'type' => 'text',
                            'text' => ['content' => '03.09.2025'],
                            'annotations' => [
                                'bold' => false,
                                'italic' => false,
                                'strikethrough' => false,
                                'underline' => false,
                                'code' => false,
                                'color' => 'default'
                            ],
                            'plain_text' => '03.09.2025'
                        ]
                    ]
                ]
            ]
        ]
    ]
];

// Initialize the real Notion SDK with token
$token = include __DIR__.'/notion-token.php';
$notion = new Notion($token, '2025-09-03');

// Create and configure the adapter
$adapter = new TableAdapter();
$adapter->setSdk($notion);

// Convert to markdown
$markdown = $adapter->toMarkdown($tableBlock);

// Save to file
file_put_contents(__DIR__ . '/table.md', $markdown);

echo "Table converted and saved to table.md\n";
