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
    public static function make(string $pageId = ''): self
    {
        return new self($pageId, app(PageReader::class), app(DatabaseReader::class));
    }

    /**
     * Set the page ID for this instance
     */
    public function setPage(string $pageId): self
    {
        $this->pageId = $pageId;

        return $this;
    }

    /**
     * Validate that pageId is set
     */
    private function validatePageId(): void
    {
        if (empty($this->pageId)) {
            throw new \InvalidArgumentException('Page ID must be set. Use setPage() method or provide pageId when creating the instance.');
        }
    }

    /**
     * Fetch only child pages as collection
     */
    public function pages(): Collection
    {
        $this->validatePageId();
        $page = $this->pageReader->read($this->pageId);

        return $page->getChildPages();
    }

    /**
     * Fetch only child databases as collection
     */
    public function databases(): Collection
    {
        $this->validatePageId();
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
        $this->validatePageId();
        $page = $this->pageReader->read($this->pageId);

        // Read all child content recursively
        if ($page->hasChildDatabases()) {
            $page->readChildDatabasesContent($this->databaseReader);

            // Read content of all database items (pages within databases)
            foreach ($page->getChildDatabases() as $database) {
                $database->readItemsContent($this->pageReader);
            }
        }

        if ($page->hasChildPages()) {
            $page->readAllPagesContent($this->pageReader);
        }

        // Build structured data for template
        $data = $this->buildFullMarkdownData($page);

        // Build complete markdown recursively using Blade template
        $template = config('md-notion.templates.full_markdown', 'md-notion::full-md');

        return view($template, $data)->render();
    }

    /**
     * Build structured data for full markdown template
     */
    private function buildFullMarkdownData($page, $level = 1): array
    {
        $currentPage = [
            'title' => $page->renderTitle($level),
            'content' => $page->hasContent() ? $page->getContent() : null,
            'hasContent' => $page->hasContent(),
        ];

        $childDatabases = [];
        if ($page->hasChildDatabases()) {
            foreach ($page->getChildDatabases() as $database) {
                $databaseData = [
                    'title' => $database->renderTitle(min($level + 1, 3)),
                    'table_content' => $database->hasTableContent() ? $database->getTableContent() : null,
                    'hasTableContent' => $database->hasTableContent(),
                    'child_pages' => [],
                ];

                // Add content of database items (pages within the database)
                if ($database->hasChildPages()) {
                    foreach ($database->getChildPages() as $itemPage) {
                        $databaseData['child_pages'][] = $this->buildFullMarkdownData($itemPage, min($level + 2, 3));
                    }
                }

                $childDatabases[] = $databaseData;
            }
        }

        $childPages = [];
        if ($page->hasChildPages()) {
            foreach ($page->getChildPages() as $childPage) {
                $childPages[] = $this->buildFullMarkdownData($childPage, min($level + 1, 3));
            }
        }

        return [
            'current_page' => $currentPage,
            'child_databases' => $childDatabases,
            'child_pages' => $childPages,
            'hasChildDatabases' => $page->hasChildDatabases(),
            'hasChildPages' => $page->hasChildPages(),
            'level' => $level,
        ];
    }
}
