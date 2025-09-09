<?php

namespace RedberryProducts\MdNotion\Services;

use RedberryProducts\MdNotion\Objects\Database;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\SDK\Notion;

class PageReader
{
    public function __construct(
        private Notion $sdk,
        private BlockRegistry $registry
    ) {}

    /**
     * Read page content and build complete Page object
     *
     * @param  string  $pageId  The Notion page ID
     * @return Page The page object with all content and children
     */
    public function read(string $pageId): Page
    {
        // Get page details and build initial Page object
        $pageResponse = $this->sdk->act()->getPage($pageId);
        $pageData = $pageResponse->json();
        $page = Page::from($pageData);

        // Get block children only once
        $blocksResponse = $this->sdk->act()->getBlockChildren($pageId, null);
        $blocks = $blocksResponse->json()['results'] ?? [];

        // Process blocks to extract different types of content
        $markdown = '';
        $childPages = collect();
        $childDatabases = collect();

        foreach ($blocks as $block) {
            $blockType = $block['type'];

            if ($blockType === 'child_page') {
                // Create page object from child_page block context
                $childPage = Page::from($block);
                $childPages->push($childPage);
            } elseif ($blockType === 'child_database') {
                // Create database object from child_database block context
                $childDatabase = Database::from($block);
                $childDatabases->push($childDatabase);
            } else {
                // Convert regular blocks to markdown
                $markdown .= $this->processBlock($block);
                $markdown .= "\n";
                $markdown .= "\n";
            }
        }

        // Fill the page with all resolved content
        $page->setContent($markdown);
        $page->setChildPages($childPages);
        $page->setChildDatabases($childDatabases);

        return $page;
    }

    /**
     * Process a single block and convert to markdown
     *
     * @param  array  $block  The block data
     * @return string The markdown representation
     */
    private function processBlock(array $block): string
    {
        $blockType = $block['type'];

        try {
            $adapter = $this->registry->resolve($blockType);

            return $adapter->toMarkdown($block);
        } catch (\InvalidArgumentException $e) {
            // If no adapter found, return a comment indicating unsupported block
            return "<!-- Unsupported block type: {$blockType} -->\n\n";
        }
    }
}
