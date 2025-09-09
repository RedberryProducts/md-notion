<?php

namespace RedberryProducts\MdNotion\Services;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\SDK\Notion;

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
     * @return Database The database object with all content and items
     */
    public function read(string $databaseId): Database
    {
        // Get database details and build initial Database object
        $databaseResponse = $this->sdk->act()->getDatabase($databaseId);
        $databaseData = $databaseResponse->json();
        $database = Database::from($databaseData);

        // Query database data source only once
        if (isset($databaseData['data_sources']) && is_array($databaseData['data_sources'])) {
            foreach ($databaseData['data_sources'] as $dataSource) {
                $dataSourceId = $dataSource['id'] ?? null;
                if ($dataSourceId) {
                    // Query the data source to get its content
                    $queryResponse = $this->sdk->act()->queryDataSource($dataSourceId, null);
                    $queryData = $queryResponse->json();
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
        $database->setTableContent($tableContent);

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
