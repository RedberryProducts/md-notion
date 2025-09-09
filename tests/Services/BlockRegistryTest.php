<?php

use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\BlockRegistry;

test('block registry resolves adapter correctly', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $adapterMap = [
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
    ];

    $factory = new BlockAdapterFactory($notion, $adapterMap);
    $registry = new BlockRegistry($factory);

    $adapter = $registry->resolve('paragraph');

    expect($adapter)->toBeInstanceOf(\RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class);
});

test('block registry returns registered block types', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $adapterMap = [
        'paragraph' => \RedberryProducts\MdNotion\Adapters\ParagraphAdapter::class,
        'heading_1' => \RedberryProducts\MdNotion\Adapters\HeadingAdapter::class,
    ];

    $factory = new BlockAdapterFactory($notion, $adapterMap);
    $registry = new BlockRegistry($factory);

    $types = $registry->getRegisteredBlockTypes();

    expect($types)->toBe(['paragraph', 'heading_1']);
});

test('block registry throws exception for unknown block type', function () {
    $notion = new Notion('test-key', '2022-06-28');
    $adapterMap = [];

    $factory = new BlockAdapterFactory($notion, $adapterMap);
    $registry = new BlockRegistry($factory);

    expect(fn () => $registry->resolve('unknown'))
        ->toThrow(\InvalidArgumentException::class);
});
