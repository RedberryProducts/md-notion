<?php

use Redberry\MdNotion\Adapters\ToggleAdapter;
use Redberry\MdNotion\DTOs\RichTextDTO;
use Redberry\MdNotion\DTOs\ToggleDTO;
use Redberry\MdNotion\SDK\Notion;

it('converts toggle block to markdown', function () {
    // Create mock SDK response for children (getBlockChildren now returns array directly)
    $mockResponse = json_decode(file_get_contents(dirname(__DIR__, 2).'/BlockJsonExamples/ToggleResponseExample.json'), true);

    $mockSdk = Mockery::mock(Notion::class);
    $mockSdk->shouldReceive('act->getBlockChildren')
        ->once()
        ->andReturn($mockResponse);

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

    $dto = new ToggleDTO($block);

    expect($dto)->toBeInstanceOf(ToggleDTO::class)
        ->and($dto->richText)->toBeArray()
        ->and($dto->richText[0])->toBeInstanceOf(RichTextDTO::class)
        ->and($dto->richText[0]->plainText)->toBe('Toggle title here ')
        ->and($dto->color)->toBe('default');
});
