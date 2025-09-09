<?php

use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Services\PageReader;

test('page reader can be instantiated', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);

    $pageReader = new PageReader($notion, $registry);

    expect($pageReader)->toBeInstanceOf(PageReader::class);
});

test('page reader processes blocks correctly', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $adapterMap = [
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
    ];
    $factory = new BlockAdapterFactory($notion, $adapterMap);
    $registry = new BlockRegistry($factory);

    $pageReader = new PageReader($notion, $registry);

    // Test the private processBlock method via reflection
    $reflection = new \ReflectionClass($pageReader);
    $method = $reflection->getMethod('processBlock');
    $method->setAccessible(true);

    $block = [
        'id' => 'block-123',
        'type' => 'paragraph',
        'paragraph' => [
            'rich_text' => [
                [
                    'type' => 'text',
                    'text' => [
                        'content' => 'Hello world',
                        'link' => null,
                    ],
                    'annotations' => [
                        'bold' => false,
                        'italic' => false,
                        'strikethrough' => false,
                        'underline' => false,
                        'code' => false,
                        'color' => 'default',
                    ],
                    'plain_text' => 'Hello world',
                    'href' => null,
                ],
            ],
            'color' => 'default',
        ],
    ];
    $result = $method->invoke($pageReader, $block);

    expect($result)->toContain('Hello world');
});

test('page reader handles unsupported block types gracefully', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);

    $pageReader = new PageReader($notion, $registry);

    // Test the private processBlock method via reflection
    $reflection = new \ReflectionClass($pageReader);
    $method = $reflection->getMethod('processBlock');
    $method->setAccessible(true);

    $block = ['type' => 'unsupported_block', 'id' => 'test-id'];
    $result = $method->invoke($pageReader, $block);

    expect($result)->toContain('<!-- Unsupported block type: unsupported_block -->');
});

afterEach(function () {
    Mockery::close();
});
