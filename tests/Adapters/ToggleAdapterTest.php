<?php

use RedberryProducts\MdNotion\Adapters\ToggleAdapter;
use RedberryProducts\MdNotion\DTOs\ToggleDTO;
use RedberryProducts\MdNotion\SDK\Notion;

it('converts toggle block to markdown', function () {
    // Create mock SDK response for children
    $mockResponse = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ToggleResponseExample.json'), true);

    // Create mock response object
    $mockResponseObj = Mockery::mock(\Saloon\Http\Response::class);
    $mockResponseObj->shouldReceive('json')
        ->andReturn($mockResponse);

    $mockSdk = Mockery::mock(Notion::class);
    $mockSdk->shouldReceive('act->getBlockChildren')
        ->once()
        ->andReturn($mockResponseObj);

    // Create the adapter
    $adapter = new ToggleAdapter;
    $adapter->setSdk($mockSdk);

    // Load sample toggle block
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ToggleJson.json'), true);

    // Convert to markdown
    $markdown = $adapter->toMarkdown($block);

    // Expected markdown structure
    $expectedParts = [
        '<details>',
        '<summary>Toggle title here',
        'Toggle value',
        '![](https://prod-files-secure.s3.us-west-2.amazonaws.com',
        '</details>',
    ];

    foreach ($expectedParts as $part) {
        expect($markdown)->toContain($part);
    }

    // Expectations are now done in the loop above
});

it('creates toggle DTO from block data', function () {
    $block = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ToggleJson.json'), true);

    $dto = ToggleDTO::from($block);

    expect($dto)->toBeInstanceOf(ToggleDTO::class)
        ->and($dto->richText)->toBeArray()
        ->and($dto->richText[0]['plain_text'])->toBe('Toggle title here ')
        ->and($dto->color)->toBe('default');
});
