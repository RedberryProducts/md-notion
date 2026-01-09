<?php

use Redberry\MdNotion\SDK\Exceptions\NotionApiException;
use Redberry\MdNotion\SDK\Notion;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    config(['md-notion.default_page_size' => 100]);
});

test('notion SDK throws NotionApiException on 400 validation error', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 400,
            'code' => 'validation_error',
            'message' => 'body failed validation: body.properties should be defined, instead was undefined.',
        ], 400),
    ]);

    $notion->withMockClient($mockClient);

    $this->expectException(NotionApiException::class);
    $this->expectExceptionMessage('Notion API Error [400] validation_error: body failed validation');

    $notion->act()->getPage('test-page-id');
});

test('notion SDK throws NotionApiException on 401 unauthorized', function () {
    $notion = new Notion('invalid-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 401,
            'code' => 'unauthorized',
            'message' => 'API token is invalid.',
        ], 401),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('unauthorized');
        expect($e->getNotionMessage())->toBe('API token is invalid.');
        expect($e->isUnauthorized())->toBeTrue();
        expect($e->isRetryable())->toBeFalse();
    }
});

test('notion SDK throws NotionApiException on 403 restricted resource', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 403,
            'code' => 'restricted_resource',
            'message' => 'API token does not have access to this resource.',
        ], 403),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('restricted_resource');
        expect($e->isForbidden())->toBeTrue();
        expect($e->isRetryable())->toBeFalse();
    }
});

test('notion SDK throws NotionApiException on 404 object not found', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 404,
            'code' => 'object_not_found',
            'message' => 'Could not find database with ID: test-id. Make sure the relevant pages and databases are shared with your integration.',
        ], 404),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getDatabase('test-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('object_not_found');
        expect($e->isNotFound())->toBeTrue();
        expect($e->isRetryable())->toBeFalse();
    }
});

test('notion SDK throws NotionApiException on 429 rate limited', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 429,
            'code' => 'rate_limited',
            'message' => 'You have been rate limited. Please try again in a few minutes.',
        ], 429),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('rate_limited');
        expect($e->isRateLimited())->toBeTrue();
        expect($e->isRetryable())->toBeTrue();
    }
});

test('notion SDK throws NotionApiException on 500 internal server error', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 500,
            'code' => 'internal_server_error',
            'message' => 'Unexpected error occurred.',
        ], 500),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('internal_server_error');
        expect($e->isServerError())->toBeTrue();
        expect($e->isRetryable())->toBeTrue();
    }
});

test('notion SDK throws NotionApiException on 503 service unavailable', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 503,
            'code' => 'service_unavailable',
            'message' => 'Notion is unavailable, please try again later.',
        ], 503),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('service_unavailable');
        expect($e->isServerError())->toBeTrue();
        expect($e->isRetryable())->toBeTrue();
    }
});

test('notion SDK throws NotionApiException on 409 conflict error', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 409,
            'code' => 'conflict_error',
            'message' => 'Conflict occurred while saving. Please try again.',
        ], 409),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBe('conflict_error');
        expect($e->isRetryable())->toBeTrue();
    }
});

test('NotionApiException provides access to response', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'error',
            'status' => 400,
            'code' => 'invalid_request',
            'message' => 'Unsupported request.',
        ], 400),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getResponse())->not->toBeNull();
        expect($e->getResponse()->status())->toBe(400);
        expect($e->getResponse()->json()['code'])->toBe('invalid_request');
    }
});

test('NotionApiException handles missing code gracefully', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'error' => 'Something went wrong',
        ], 500),
    ]);

    $notion->withMockClient($mockClient);

    try {
        $notion->act()->getPage('test-page-id');
        $this->fail('Expected NotionApiException to be thrown');
    } catch (NotionApiException $e) {
        expect($e->getNotionCode())->toBeNull();
        expect($e->getNotionMessage())->toBeNull();
        expect($e->getMessage())->toContain('500');
    }
});

test('successful request does not throw exception', function () {
    $notion = new Notion('test-token', '2025-09-03');

    $mockClient = new MockClient([
        MockResponse::make([
            'object' => 'page',
            'id' => 'test-page-id',
            'properties' => [],
        ], 200),
    ]);

    $notion->withMockClient($mockClient);

    $response = $notion->act()->getPage('test-page-id');

    expect($response->status())->toBe(200);
    expect($response->json()['id'])->toBe('test-page-id');
});

afterEach(function () {
    Mockery::close();
});
