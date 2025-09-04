<?php

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\SDK\Requests\Actions\AddCommentToPage;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

beforeEach(function () {
    $this->notion = new Notion('token', '2025-09-03');
    $this->mockClient = new MockClient([
        AddCommentToPage::class => MockResponse::fixture('notion/addCommentToPage'),
    ]);
    
    $this->notion->withMockClient($this->mockClient);
});

test('it correctly builds the request', function () {
    $parent = ['page_id' => '263d9316605a806f9e95e1377a46ff3e'];
    $richText = [
        [
            'text' => ['content' => 'This is a comment']
        ]
    ];
    
    $request = new AddCommentToPage($parent, $richText);
    
    expect($request->resolveEndpoint())->toBe("/v1/comments");
    expect($request->getMethod()->value)->toBe('POST');
    
    $expectedBody = [
        'parent' => $parent,
        'rich_text' => $richText,
        'display_name' => ['type' => 'integration'],
    ];
    
    expect($request->body()->all())->toBe($expectedBody);
});

test('it can add comment to page', function () {
    $parent = ['page_id' => '263d9316605a806f9e95e1377a46ff3e'];
    $richText = [
        [
            'text' => ['content' => 'This is a comment']
        ]
    ];
    
    $response = $this->notion->send(new AddCommentToPage($parent, $richText));
    
    expect($response->ok())->toBeTrue();
    
    $this->mockClient->assertSent(function ($request) use ($parent, $richText) {
        return $request instanceof AddCommentToPage 
            && $request->resolveEndpoint() === "/v1/comments"
            && $request->body()->get('parent') === $parent
            && $request->body()->get('rich_text') === $richText
            && $request->body()->get('display_name') === ['type' => 'integration'];
    });
});
