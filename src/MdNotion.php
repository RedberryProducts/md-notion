<?php

namespace RedberryProducts\MdNotion;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\PageReader;

class MdNotion
{
    public function __construct(
        private string $pageId,
        private PageReader $pageReader,
        private DatabaseReader $databaseReader
    ) {}

    /**
     * Create a new MdNotion instance for the given page ID
     */
    public static function make(string $pageId): self
    {
        return app(self::class, ['pageId' => $pageId]);
    }

    /**
     * Fetch only child pages as collection
     */
    public function pages(): Collection
    {
        $page = $this->pageReader->read($this->pageId);

        return $page->getChildPages();
    }

    /**
     * Fetch only child databases as collection
     */
    public function databases(): Collection
    {
        $page = $this->pageReader->read($this->pageId);

        return $page->getChildDatabases();
    }

    /**
     * Get content builder for fluent API
     */
    public function content(): ContentBuilder
    {
        return new ContentBuilder(
            $this->pageId,
            $this->pageReader,
            $this->databaseReader
        );
    }

    /**
     * Fetch databases and pages recursively, concatenate all results
     */
    public function full(): string
    {
        $page = $this->pageReader->read($this->pageId);

        // Read all child content recursively
        if ($page->hasChildDatabases()) {
            $page->readChildDatabasesContent($this->databaseReader);
        }

        if ($page->hasChildPages()) {
            $page->readAllPagesContent($this->pageReader);
        }

        // Build complete markdown recursively
        return $this->buildFullMarkdown($page);
    }

    /**
     * Build complete markdown content recursively
     */
    private function buildFullMarkdown(Page $page, int $level = 1): string
    {
        $markdown = '';

        // Add page title and content
        $markdown .= $page->renderTitle($level)."\n\n";
        if ($page->hasContent()) {
            $markdown .= $page->getContent()."\n\n";
        }

        // Add child databases
        if ($page->hasChildDatabases()) {
            foreach ($page->getChildDatabases() as $database) {
                $markdown .= $database->renderTitle(min($level + 1, 3))."\n\n";
                if ($database->hasTableContent()) {
                    $markdown .= $database->getTableContent()."\n\n";
                }
            }
        }

        // Add child pages recursively
        if ($page->hasChildPages()) {
            foreach ($page->getChildPages() as $childPage) {
                $markdown .= $this->buildFullMarkdown($childPage, min($level + 1, 3));
            }
        }

        return $markdown;
    }
}
