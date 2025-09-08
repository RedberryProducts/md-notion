<?php

namespace RedberryProducts\MdNotion\Services;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\SDK\Notion;
use Saloon\Http\Response;

class ContentManager
{
    public function __construct(
        private Notion $sdk,
        private BlockRegistry $registry
    ) {}

    /**
     * Fetch page content and build Page object with markdown content
     *
     * @param string $pageId The Notion page ID
     * @return Page The page object with content
     */
    public function fetchPageContent(string $pageId): Page
    {
        // Get page details
        $pageResponse = $this->sdk->act()->getPage($pageId);
        $pageData = $pageResponse->json();

        // Create page object
        $page = Page::from($pageData);

        // Fetch blocks and convert to markdown
        $markdown = $this->fetchBlocksAsMarkdown($pageId);
        $page->setContent($markdown);

        return $page;
    }

    /**
     * Fetch blocks from a page/block and convert to markdown
     *
     * @param string $blockId The block or page ID
     * @return string The markdown content
     */
    public function fetchBlocksAsMarkdown(string $blockId): string
    {
        $blocksResponse = $this->sdk->act()->getBlockChildren($blockId, null);
        $blocks = $blocksResponse->json()['results'] ?? [];

        $markdown = '';
        foreach ($blocks as $block) {
            $markdown .= $this->processBlock($block);
        }

        return $markdown;
    }

    /**
     * Fetch child pages for a given page
     *
     * @param string $pageId The parent page ID
     * @return Collection<Page> Collection of child pages
     */
    public function fetchChildPages(string $pageId): Collection
    {
        $blocksResponse = $this->sdk->act()->getBlockChildren($pageId, null);
        $blocks = $blocksResponse->json()['results'] ?? [];

        $childPages = collect();

        foreach ($blocks as $block) {
            if ($block['type'] === 'child_page') {
                // Create page object from child_page block
                $pageData = [
                    'id' => $block['id'],
                    'title' => $block['child_page']['title'] ?? '',
                    'has_children' => $block['has_children'] ?? false,
                ];

                $childPage = Page::from($pageData);
                $childPages->push($childPage);
            }
        }

        return $childPages;
    }

    /**
     * Fetch child databases for a given page
     *
     * @param string $pageId The parent page ID
     * @return Collection<Database> Collection of child databases
     */
    public function fetchChildDatabases(string $pageId): Collection
    {
        $blocksResponse = $this->sdk->act()->getBlockChildren($pageId, null);
        $blocks = $blocksResponse->json()['results'] ?? [];

        $childDatabases = collect();

        foreach ($blocks as $block) {
            if ($block['type'] === 'child_database') {
                // Create database object from child_database block
                $databaseData = [
                    'id' => $block['id'],
                    'title' => $block['child_database']['title'] ?? '',
                ];

                $childDatabase = Database::from($databaseData);
                $childDatabases->push($childDatabase);
            }
        }

        return $childDatabases;
    }

    /**
     * Fetch page with all child content (pages and databases)
     *
     * @param string $pageId The page ID
     * @param bool $withPages Include child pages
     * @param bool $withDatabases Include child databases
     * @return Page The page with all child content
     */
    public function fetchPageWithChildren(string $pageId, bool $withPages = false, bool $withDatabases = false): Page
    {
        $page = $this->fetchPageContent($pageId);

        if ($withPages) {
            $childPages = $this->fetchChildPages($pageId);
            $page->setChildPages($childPages);

            // Recursively fetch content for child pages
            foreach ($childPages as $childPage) {
                $childPageWithContent = $this->fetchPageContent($childPage->getId());
                $childPage->setContent($childPageWithContent->getContent());
            }
        }

        if ($withDatabases) {
            $childDatabases = $this->fetchChildDatabases($pageId);
            $page->setChildDatabases($childDatabases);
        }

        return $page;
    }

    /**
     * Process a single block and convert to markdown
     *
     * @param array $block The block data
     * @return string The markdown representation
     */
    private function processBlock(array $block): string
    {
        $blockType = $block['type'];

        // Skip child_page and child_database blocks as they are handled separately
        if (in_array($blockType, ['child_page', 'child_database'])) {
            return '';
        }

        try {
            $adapter = $this->registry->resolve($blockType);
            return $adapter->toMarkdown($block);
        } catch (\InvalidArgumentException $e) {
            // If no adapter found, return a comment indicating unsupported block
            return "<!-- Unsupported block type: {$blockType} -->\n\n";
        }
    }
}