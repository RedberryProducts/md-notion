<?php

use Redberry\MdNotion\Objects\Page;
use Redberry\MdNotion\Services\PropertiesTable;

beforeEach(function () {
    // Mock the PropertiesTable service
    $this->app->singleton(PropertiesTable::class, function () {
        return new PropertiesTable;
    });
});

test('page can check if it has properties', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [
            'Name' => [
                'type' => 'title',
                'title' => [['plain_text' => 'Test']],
            ],
        ],
    ]);

    expect($page->hasProperties())->toBeTrue();
});

test('page returns false when it has no properties', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [],
    ]);

    expect($page->hasProperties())->toBeFalse();
});

test('page can render properties table', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [
            'Name' => [
                'id' => 'title',
                'type' => 'title',
                'title' => [
                    ['plain_text' => 'Test Page'],
                ],
            ],
            'Status' => [
                'id' => 'status',
                'type' => 'status',
                'status' => [
                    'name' => 'In Progress',
                ],
            ],
        ],
    ]);

    $result = $page->renderPropertiesTable();

    expect($result)->toContain('| Property | Value |')
        ->and($result)->toContain('| Name | Test Page |')
        ->and($result)->toContain('| Status | In Progress |');
});

test('page returns empty string when rendering properties table without properties', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [],
    ]);

    $result = $page->renderPropertiesTable();

    expect($result)->toBe('');
});

test('page can get and set properties', function () {
    $page = new Page(['id' => 'test-id']);

    $properties = [
        'Name' => [
            'type' => 'title',
            'title' => [['plain_text' => 'Test']],
        ],
    ];

    $page->setProperties($properties);

    expect($page->getProperties())->toBe($properties);
});

test('page can get individual property', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [
            'Name' => [
                'type' => 'title',
                'title' => [['plain_text' => 'Test']],
            ],
        ],
    ]);

    expect($page->getProperty('Name'))->toBe([
        'type' => 'title',
        'title' => [['plain_text' => 'Test']],
    ]);
});

test('page returns null for non-existent property', function () {
    $page = new Page([
        'id' => 'test-id',
        'properties' => [],
    ]);

    expect($page->getProperty('NonExistent'))->toBeNull();
});

test('page can set individual property', function () {
    $page = new Page(['id' => 'test-id']);

    $property = [
        'type' => 'title',
        'title' => [['plain_text' => 'Test']],
    ];

    $page->setProperty('Name', $property);

    expect($page->getProperty('Name'))->toBe($property);
});
