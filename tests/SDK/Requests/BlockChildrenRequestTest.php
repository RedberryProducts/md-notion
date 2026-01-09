<?php

use Redberry\MdNotion\SDK\Notion;
use Redberry\MdNotion\SDK\Requests\Actions\BlockChildren;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        BlockChildren::class => MockResponse::fixture('notion/blockChildren'),
    ]);

    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $blockId = '263d9316-605a-8057-b12e-f880bc565fcb';
    $request = new BlockChildren($blockId, null);

    expect($request->resolveEndpoint())->toBe("/v1/blocks/{$blockId}/children");
    expect($request->getMethod()->value)->toBe('GET');
});

test('it can set page size query parameter', function () {
    $blockId = '263d9316-605a-8057-b12e-f880bc565fcb';
    $pageSize = 25;
    $request = new BlockChildren($blockId, $pageSize);

    expect($request->query()->all())->toBe(['page_size' => $pageSize]);
});

test('it can get block children', function () {
    $blockId = '263d9316-605a-8057-b12e-f880bc565fcb';
    $response = $this->notion->send(new BlockChildren($blockId, null));

    expect($response->ok())->toBeTrue();

    $this->mockClient->assertSent(function ($request) use ($blockId) {
        return $request instanceof BlockChildren
            && $request->resolveEndpoint() === "/v1/blocks/{$blockId}/children";
    });
});
