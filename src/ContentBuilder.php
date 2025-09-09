<?php

namespace RedberryProducts\MdNotion;

use RedberryProducts\MdNotion\Objects\Page;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\PageReader;

class ContentBuilder
{
    private bool $withPages = false;

    private bool $withDatabases = false;

    public function __construct(
        private string $pageId,
        private PageReader $pageReader,
        private DatabaseReader $databaseReader
    ) {}

    /**
     * Include child pages in the content
     */
    public function withPages(): self
    {
        $this->withPages = true;

        return $this;
    }

    /**
     * Include child databases in the content
     */
    public function withDatabases(): self
    {
        $this->withDatabases = true;

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
     * Get the Page object with all requested content
     */
    public function get(): Page
    {
        $this->validatePageId();
        // Read the main page
        $page = $this->pageReader->read($this->pageId);

        // Optionally fetch child pages content
        if ($this->withPages && $page->hasChildPages()) {
            $page->readChildPagesContent($this->pageReader);
        }

        // Optionally fetch child databases content
        if ($this->withDatabases && $page->hasChildDatabases()) {
            $page->readChildDatabasesContent($this->databaseReader);
        }

        return $page;
    }

    /**
     * Get the content as markdown string
     */
    public function read(): string
    {
        $this->validatePageId();
        $page = $this->get();

        // Use Blade template to render markdown
        $template = config('md-notion.templates.page_markdown', 'md-notion::page-md');
        
        return view($template, [
            'page' => $page,
            'withDatabases' => $this->withDatabases,
            'withPages' => $this->withPages,
        ])->render();
    }
}
