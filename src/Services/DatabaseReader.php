<?php

namespace Redberry\MdNotion\Services;

use Illuminate\Support\Collection;
use Redberry\MdNotion\Objects\Database;
use Redberry\MdNotion\Objects\Page;
use Redberry\MdNotion\SDK\Notion;

class DatabaseReader
{
    public function __construct(
        private Notion $sdk,
        private DatabaseTable $databaseTable
    ) {}

    /**
     * Read database content and build complete Database object
     *
     * @param  string  $databaseId  The Notion database ID
     * @param  int|null  $pageSize  Optional page size for data source query (uses config default if null)
     * @return Database The database object with all content and items
     */
    public function read(string $databaseId, ?int $pageSize = null): Database
    {
        // Get database details and build initial Database object
        $databaseResponse = $this->sdk->act()->getDatabase($databaseId);
        $databaseData = $databaseResponse->json();
        $database = Database::from($databaseData);

        // Resolve page size from argument or config default
        $resolvedPageSize = $pageSize ?? config('md-notion.default_page_size');

        // Query database data source only once
        if (isset($databaseData['data_sources']) && is_array($databaseData['data_sources'])) {
            foreach ($databaseData['data_sources'] as $dataSource) {
                $dataSourceId = $dataSource['id'] ?? null;
                if ($dataSourceId) {
                    // Query the data source to get all content with pagination
                    $queryData = $this->sdk->act()->queryDataSource($dataSourceId, null, $resolvedPageSize);
                    
                    // Handle both Response and array returns from queryDataSource
                    if ($queryData instanceof \Saloon\Http\Response) {
                        $queryData = $queryData->json();
                    }
                    
                    // Convert query data to markdown table
                    $tableContent = $this->databaseTable->convertQueryToMarkdownTable($queryData);
                    // Optionally, add data source name as a note above the table
                    $name = $dataSource['name'] ?? '---';
                    $newTableContent = $database->getTableContent()."\n\n_source {$name}_\n\n".$tableContent;
                    $database->setTableContent($newTableContent);
                }
            }
        }

        // Resolve database as markdown content using DatabaseTable service
        $database->setTableContent($tableContent ?? '');

        // Resolve database items as collection of Page objects
        $items = collect();
        $results = $queryData['results'] ?? [];

        foreach ($results as $itemData) {
            if ($itemData['object'] === 'page') {
                $page = Page::from($itemData);
                $items->push($page);
            }
        }

        // Add items to database in childPages (as requested)
        $database->setChildPages($items);

        return $database;
    }
}
