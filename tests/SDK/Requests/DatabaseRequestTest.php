<?php

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\Database;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        Database::class => MockResponse::fixture('notion/database'),
    ]);
    
    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $databaseId = '263d9316-605a-80e8-8f08-cc0acb533046';
    $request = new Database($databaseId);
    
    expect($request->resolveEndpoint())->toBe("/v1/databases/{$databaseId}");
    expect($request->getMethod()->value)->toBe('GET');
});

test('it can get a database', function () {
    $databaseId = '263d9316-605a-80e8-8f08-cc0acb533046';
    $response = $this->notion->send(new Database($databaseId));
    
    expect($response->ok())->toBeTrue();
    
    $this->mockClient->assertSent(function ($request) use ($databaseId) {
        return $request instanceof Database 
            && $request->resolveEndpoint() === "/v1/databases/{$databaseId}";
    });
});
