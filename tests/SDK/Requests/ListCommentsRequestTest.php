<?php

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\ListComments;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        ListComments::class => MockResponse::fixture('notion/listComments'),
    ]);
    
    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $blockId = '263d9316-605a-8057-b12e-f880bc565fcb';
    $request = new ListComments($blockId);
    
    expect($request->resolveEndpoint())->toBe("/v1/comments");
    expect($request->getMethod()->value)->toBe('GET');
    expect($request->query()->all())->toBe(['block_id' => $blockId]);
});

test('it can list comments', function () {
    $blockId = '263d9316-605a-8057-b12e-f880bc565fcb';
    $response = $this->notion->send(new ListComments($blockId));
    
    expect($response->ok())->toBeTrue();
    
    $this->mockClient->assertSent(function ($request) use ($blockId) {
        return $request instanceof ListComments 
            && $request->resolveEndpoint() === "/v1/comments"
            && $request->query()->get('block_id') === $blockId;
    });
});
