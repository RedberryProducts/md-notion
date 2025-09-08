<?php

use RedberryProducts\MdNotion\Adapters\ColumnAdapter;
use RedberryProducts\MdNotion\DTOs\ColumnDTO;
use RedberryProducts\MdNotion\SDK\Notion;

it('converts column block to markdown', function () {
    // Create mock SDK response for children
    $mockResponse = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ColumnChildrenJson.json'), true);

    $mockResponseObj = Mockery::mock(\Saloon\Http\Response::class);
    $mockResponseObj->shouldReceive('json')
        ->andReturn($mockResponse);

    $mockSdk = Mockery::mock(Notion::class);
    $mockSdk->shouldReceive('act->getBlockChildren')
        ->once()
        ->andReturn($mockResponseObj);

    // Create the adapter
    $adapter = new ColumnAdapter;
    $adapter->setSdk($mockSdk);

    // Load sample column block
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ColumnJson.json'), true);

    // Convert to markdown
    $markdown = $adapter->toMarkdown($block);

    // Expected markdown structure
    $expectedParts = [
        'Col 2',
    ];

    foreach ($expectedParts as $part) {
        expect($markdown)->toContain($part);
    }
});

it('creates column DTO from block data', function () {
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ColumnJson.json'), true);

    $dto = ColumnDTO::from($block);

    expect($dto)->toBeInstanceOf(ColumnDTO::class)
        ->and($dto->widthRatio)->toBe(0.5);
});
