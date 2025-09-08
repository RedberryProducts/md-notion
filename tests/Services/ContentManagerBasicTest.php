<?php

use RedberryProducts\MdNotion\Services\ContentManager;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Objects\Page;

test('content manager can be instantiated', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $adapterMap = [
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
    ];
    
    $factory = new BlockAdapterFactory($notion, $adapterMap);
    $registry = new BlockRegistry($factory);
    $contentManager = new ContentManager($notion, $registry);
    
    expect($contentManager)->toBeInstanceOf(ContentManager::class);
});

test('content manager processes unsupported block types gracefully', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $contentManager = new ContentManager($notion, $registry);
    
    // Test the private processBlock method via reflection
    $reflection = new \ReflectionClass($contentManager);
    $method = $reflection->getMethod('processBlock');
    $method->setAccessible(true);
    
    $block = ['type' => 'unsupported_type', 'id' => 'test-id'];
    $result = $method->invoke($contentManager, $block);
    
    expect($result)->toContain('<!-- Unsupported block type: unsupported_type -->');
});

test('content manager skips child page and database blocks', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $factory = new BlockAdapterFactory($notion, []);
    $registry = new BlockRegistry($factory);
    $contentManager = new ContentManager($notion, $registry);
    
    // Test the private processBlock method via reflection
    $reflection = new \ReflectionClass($contentManager);
    $method = $reflection->getMethod('processBlock');
    $method->setAccessible(true);
    
    $childPageBlock = ['type' => 'child_page', 'id' => 'test-id'];
    $childDbBlock = ['type' => 'child_database', 'id' => 'test-id'];
    
    $pageResult = $method->invoke($contentManager, $childPageBlock);
    $dbResult = $method->invoke($contentManager, $childDbBlock);
    
    expect($pageResult)->toBe('');
    expect($dbResult)->toBe('');
});