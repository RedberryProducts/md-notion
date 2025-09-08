<?php

namespace RedberryProducts\MdNotion\Services;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\SDK\Notion;

class DatabaseTable
{
    public function __construct(
        private Notion $sdk,
        private ContentManager $contentManager
    ) {}

    /**
     * Fetch database items and convert to markdown table
     *
     * @param string $databaseId The database ID
     * @return string The markdown table representation
     */
    public function fetchDatabaseAsMarkdownTable(string $databaseId): string
    {
        // Query database to get all items
        $queryResponse = $this->sdk->act()->queryDataSource($databaseId, null);
        $results = $queryResponse->json()['results'] ?? [];

        if (empty($results)) {
            return "<!-- Empty database: {$databaseId} -->\n\n";
        }

        // Get database schema to understand properties
        $databaseResponse = $this->sdk->act()->getDatabase($databaseId);
        $databaseData = $databaseResponse->json();
        $properties = $databaseData['properties'] ?? [];

        // Build markdown table
        $markdown = $this->buildMarkdownTable($results, $properties);

        return $markdown;
    }

    /**
     * Fetch database items as Page objects with markdown content
     *
     * @param string $databaseId The database ID
     * @return Collection<Page> Collection of pages with content
     */
    public function fetchDatabaseItems(string $databaseId): Collection
    {
        $queryResponse = $this->sdk->act()->queryDataSource($databaseId, null);
        $results = $queryResponse->json()['results'] ?? [];

        $pages = collect();

        foreach ($results as $result) {
            // Create page object from database item
            $page = Page::from($result);
            
            // Fetch content for each page
            $pageWithContent = $this->contentManager->fetchPageContent($page->getId());
            $page->setContent($pageWithContent->getContent());
            
            $pages->push($page);
        }

        return $pages;
    }

    /**
     * Build markdown table from database results
     *
     * @param array $results Database query results
     * @param array $properties Database properties schema
     * @return string Markdown table
     */
    private function buildMarkdownTable(array $results, array $properties): string
    {
        if (empty($results)) {
            return '';
        }

        // Determine columns to include (limit to important ones)
        $columns = $this->getTableColumns($properties);
        
        if (empty($columns)) {
            return "<!-- No displayable properties found in database -->\n\n";
        }

        // Build table header
        $header = '| ' . implode(' | ', array_map(fn($col) => $col['title'], $columns)) . ' |' . "\n";
        $separator = '| ' . implode(' | ', array_fill(0, count($columns), '---')) . ' |' . "\n";

        // Build table rows
        $rows = '';
        foreach ($results as $result) {
            $cells = [];
            foreach ($columns as $column) {
                $cells[] = $this->extractPropertyValue($result['properties'][$column['key']] ?? null, $column['type']);
            }
            $rows .= '| ' . implode(' | ', $cells) . ' |' . "\n";
        }

        return $header . $separator . $rows . "\n";
    }

    /**
     * Get columns to display in the table
     *
     * @param array $properties Database properties
     * @return array Column definitions
     */
    private function getTableColumns(array $properties): array
    {
        $columns = [];

        // Prioritize certain property types
        $priorityTypes = ['title', 'rich_text', 'url', 'email', 'date', 'number', 'select', 'multi_select'];
        
        foreach ($properties as $key => $property) {
            if (in_array($property['type'], $priorityTypes)) {
                $columns[] = [
                    'key' => $key,
                    'title' => $property['name'] ?? $key,
                    'type' => $property['type']
                ];
            }
        }

        // Limit to first 5 columns to keep table readable
        return array_slice($columns, 0, 5);
    }

    /**
     * Extract and format property value for table display
     *
     * @param array|null $property Property data
     * @param string $type Property type
     * @return string Formatted value
     */
    private function extractPropertyValue(?array $property, string $type): string
    {
        if (!$property) {
            return '';
        }

        switch ($type) {
            case 'title':
                return $this->extractRichText($property['title'] ?? []);
            
            case 'rich_text':
                return $this->extractRichText($property['rich_text'] ?? []);
            
            case 'url':
                $url = $property['url'] ?? '';
                return $url ? "[Link]({$url})" : '';
            
            case 'email':
                $email = $property['email'] ?? '';
                return $email ? "[{$email}](mailto:{$email})" : '';
            
            case 'date':
                $date = $property['date']['start'] ?? '';
                return $date ?: '';
            
            case 'number':
                return (string) ($property['number'] ?? '');
            
            case 'select':
                return $property['select']['name'] ?? '';
            
            case 'multi_select':
                $options = $property['multi_select'] ?? [];
                return implode(', ', array_column($options, 'name'));
            
            default:
                return '';
        }
    }

    /**
     * Extract plain text from rich text array
     *
     * @param array $richText Rich text array
     * @return string Plain text
     */
    private function extractRichText(array $richText): string
    {
        $text = '';
        foreach ($richText as $item) {
            $text .= $item['plain_text'] ?? '';
        }
        return $text;
    }
}