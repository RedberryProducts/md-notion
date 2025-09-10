<?php

use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Services\DatabaseReader;

beforeEach(function () {
    $this->mockDatabaseReader = Mockery::mock(DatabaseReader::class);

    // Bind the mock to the container so app() can resolve it
    app()->instance(DatabaseReader::class, $this->mockDatabaseReader);
});

afterEach(function () {
    Mockery::close();
});

it('can fetch content using DatabaseReader', function () {
    $databaseId = 'test-database-id';

    // Create original database with minimal data
    $originalDatabase = Database::from([
        'id' => $databaseId,
        'title' => 'Original Database',
    ]);

    // Create fetched database with full content
    $fetchedDatabase = Database::from([
        'id' => $databaseId,
        'title' => 'Fetched Database',
        'tableContent' => '| Column 1 | Column 2 |\n|----------|----------|\n| Value 1  | Value 2  |',
    ]);

    $this->mockDatabaseReader
        ->shouldReceive('read')
        ->with($databaseId)
        ->once()
        ->andReturn($fetchedDatabase);

    // Fetch content
    $result = $originalDatabase->fetch();

    // Should return the same instance
    expect($result)->toBe($originalDatabase);

    // Should have updated content
    expect($originalDatabase->getTitle())->toBe('Fetched Database');
    expect($originalDatabase->hasTableContent())->toBeTrue();
    expect($originalDatabase->getTableContent())->toContain('| Column 1 | Column 2 |');
});

it('preserves object identity after fetch', function () {
    $databaseId = 'test-database-id';

    $originalDatabase = Database::from(['id' => $databaseId]);
    $fetchedDatabase = Database::from([
        'id' => $databaseId,
        'tableContent' => 'Table content',
    ]);

    $this->mockDatabaseReader
        ->shouldReceive('read')
        ->with($databaseId)
        ->once()
        ->andReturn($fetchedDatabase);

    $beforeFetch = $originalDatabase;
    $afterFetch = $originalDatabase->fetch();

    expect($beforeFetch)->toBe($afterFetch);
    expect($originalDatabase->getTableContent())->toBe('Table content');
});
