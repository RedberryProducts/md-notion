<?php

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\QueryDataSource;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        QueryDataSource::class => MockResponse::fixture('notion/queryDataSource'),
    ]);

    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $dataSourceId = '263d9316-605a-80a3-a132-000bfb8600d6';

    $request = new QueryDataSource($dataSourceId);

    expect($request->resolveEndpoint())->toBe("/v1/data_sources/{$dataSourceId}/query");
    expect($request->getMethod()->value)->toBe('POST');
});

test('it can query a data source', function () {
    $dataSourceId = '263d9316-605a-80a3-a132-000bfb8600d6';

    $response = $this->notion->act()->queryDataSource($dataSourceId);

    expect($response->ok())->toBeTrue();

    $this->mockClient->assertSent(function ($request) use ($dataSourceId) {
        return $request instanceof QueryDataSource
            && $request->resolveEndpoint() === "/v1/data_sources/{$dataSourceId}/query";
    });

    $data = $response->json();
    expect($data)->toBeArray();
    expect($data)->toHaveKey('results');
    expect($data['results'])->toHaveCount(2);
});
