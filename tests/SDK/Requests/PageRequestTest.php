<?php

use Redberry\MdNotion\SDK\Notion;
use Redberry\MdNotion\SDK\Requests\Actions\Page;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        Page::class => MockResponse::fixture('notion/page'),
    ]);

    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $pageId = '263d9316605a806f9e95e1377a46ff3e';
    $request = new Page($pageId);

    expect($request->resolveEndpoint())->toBe("/v1/pages/{$pageId}");
    expect($request->getMethod()->value)->toBe('GET');
});

test('it can get a page', function () {
    $pageId = '263d9316605a806f9e95e1377a46ff3e';
    $response = $this->notion->send(new Page($pageId));

    expect($response->ok())->toBeTrue();

    $this->mockClient->assertSent(function ($request) use ($pageId) {
        return $request instanceof Page
            && $request->resolveEndpoint() === "/v1/pages/{$pageId}";
    });
});
