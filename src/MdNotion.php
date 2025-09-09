<?php

namespace RedberryProducts\MdNotion;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\PageReader;

class MdNotion
{
    public function __construct(
        private string $pageId = '',
        private PageReader $pageReader,
        private DatabaseReader $databaseReader
    ) {}

    /**
     * Create a new MdNotion instance for the given page ID
     */
    public static function make(string $pageId = ''): self
    {
        return app(self::class, ['pageId' => $pageId]);
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

        // Build complete markdown recursively using Blade template
        $template = config('md-notion.templates.full_markdown', 'md-notion::full-md');
        
        return view($template, [
            'page' => $page,
        ])->render();
    }
}
