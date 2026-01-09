<?php

namespace Redberry\MdNotion\SDK\Resource;

use Redberry\MdNotion\SDK\Requests\Actions\AddCommentToPage;
use Redberry\MdNotion\SDK\Requests\Actions\BlockChildren;
use Redberry\MdNotion\SDK\Requests\Actions\Database;
use Redberry\MdNotion\SDK\Requests\Actions\ListComments;
use Redberry\MdNotion\SDK\Requests\Actions\Page;
use Redberry\MdNotion\SDK\Requests\Actions\QueryDataSource;
use Redberry\MdNotion\SDK\Resource;
use Saloon\Http\Response;

class Actions extends Resource
{
    /**
     * Maximum items per API request (Notion API limit)
     */
    private const MAX_PAGE_SIZE = 100;

    public function getPage(string $id): Response
    {
        return $this->connector->send(new Page($id));
    }

    /**
     * Get block children with automatic pagination
     *
     * Always returns a consistent array structure, regardless of page size.
     * Automatically paginates when pageSize > 100.
     *
     * @param  string  $id  Block ID
     * @param  int|null  $pageSize  Total number of items desired (null = API default 100)
     * @return array{results: array, has_more: bool, next_cursor: string|null}
     */
    public function getBlockChildren(string $id, ?int $pageSize = null): array
    {
        // If pageSize is within single request limit, make single request and normalize to array
        if ($pageSize === null || $pageSize <= self::MAX_PAGE_SIZE) {
            $response = $this->connector->send(new BlockChildren($id, $pageSize));
            $data = $response->json();

            return [
                'results' => $data['results'] ?? [],
                'has_more' => $data['has_more'] ?? false,
                'next_cursor' => $data['next_cursor'] ?? null,
            ];
        }

        // Otherwise, paginate until we reach the desired count
        return $this->fetchPaginatedResults(
            fn (?string $cursor) => $this->connector->send(new BlockChildren($id, self::MAX_PAGE_SIZE, $cursor)),
            $pageSize
        );
    }

    public function getDatabase(string $databaseId): Response
    {
        return $this->connector->send(new Database($databaseId));
    }

    public function addCommentToPage(mixed $parent, mixed $richText): Response
    {
        return $this->connector->send(new AddCommentToPage($parent, $richText));
    }

    public function listComments(?string $blockId): Response
    {
        return $this->connector->send(new ListComments($blockId));
    }

    /**
     * Query a data source with automatic pagination
     *
     * Always returns a consistent array structure, regardless of page size.
     * Automatically paginates when pageSize > 100.
     *
     * @param  string  $dataSourceId  Data source ID
     * @param  array|null  $filter  Optional filter
     * @param  int|null  $pageSize  Total number of items desired (null = API default 100)
     * @return array{results: array, has_more: bool, next_cursor: string|null}
     */
    public function queryDataSource(string $dataSourceId, ?array $filter = null, ?int $pageSize = null): array
    {
        // If pageSize is within single request limit, make single request and normalize to array
        if ($pageSize === null || $pageSize <= self::MAX_PAGE_SIZE) {
            $response = $this->connector->send(new QueryDataSource($dataSourceId, $filter, $pageSize));
            $data = $response->json();

            return [
                'results' => $data['results'] ?? [],
                'has_more' => $data['has_more'] ?? false,
                'next_cursor' => $data['next_cursor'] ?? null,
            ];
        }

        // Otherwise, paginate until we reach the desired count
        return $this->fetchPaginatedResults(
            fn (?string $cursor) => $this->connector->send(new QueryDataSource($dataSourceId, $filter, self::MAX_PAGE_SIZE, $cursor)),
            $pageSize
        );
    }

    /**
     * Fetch paginated results up to the desired limit
     *
     * @param  callable  $fetcher  Function that takes a cursor and returns a Response
     * @param  int  $limit  Maximum number of items to fetch
     * @return array{results: array, has_more: bool, next_cursor: string|null}
     */
    private function fetchPaginatedResults(callable $fetcher, int $limit): array
    {
        $allResults = [];
        $cursor = null;
        $hasMore = false;
        $hadExtraResults = false;

        do {
            $response = $fetcher($cursor);
            $data = $response->json();

            $results = $data['results'] ?? [];
            $allResults = array_merge($allResults, $results);

            $hasMore = $data['has_more'] ?? false;
            $cursor = $data['next_cursor'] ?? null;

            // Stop if we've reached our desired limit
            if (count($allResults) >= $limit) {
                // Track if we fetched more items than requested (they will be trimmed)
                $hadExtraResults = count($allResults) > $limit;
                // Trim to exact limit
                $allResults = array_slice($allResults, 0, $limit);
                break;
            }
        } while ($hasMore && $cursor);

        return [
            'results' => $allResults,
            'has_more' => $hasMore || $hadExtraResults,
            'next_cursor' => $cursor,
        ];
    }
}
