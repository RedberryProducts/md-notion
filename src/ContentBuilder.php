<?php

namespace Redberry\MdNotion;

use Redberry\MdNotion\Objects\Page;
use Redberry\MdNotion\Services\DatabaseReader;
use Redberry\MdNotion\Services\PageReader;

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

        // Build structured data for template
        $currentPage = [
            'title' => $page->renderTitle(1),
            'content' => $page->hasContent() ? $page->getContent() : null,
            'hasContent' => $page->hasContent(),
        ];

        $childDatabases = [];
        if ($this->withDatabases && $page->hasChildDatabases()) {
            foreach ($page->getChildDatabases() as $database) {
                $childDatabases[] = [
                    'title' => $database->renderTitle(3),
                    'table_content' => $database->hasTableContent() ? $database->getTableContent() : null,
                    'hasTableContent' => $database->hasTableContent(),
                ];
            }
        }

        $childPages = [];
        if ($this->withPages && $page->hasChildPages()) {
            foreach ($page->getChildPages() as $childPage) {
                $childPages[] = [
                    'title' => $childPage->renderTitle(3),
                    'content' => $childPage->hasContent() ? $childPage->getContent() : null,
                    'hasContent' => $childPage->hasContent(),
                ];
            }
        }

        // Use Blade template to render markdown
        $template = config('md-notion.templates.page_markdown', 'md-notion::page-md');

        return view($template, [
            'current_page' => $currentPage,
            'child_databases' => $childDatabases,
            'child_pages' => $childPages,
            'withDatabases' => $this->withDatabases,
            'withPages' => $this->withPages,
            'hasChildDatabases' => $this->withDatabases && $page->hasChildDatabases(),
            'hasChildPages' => $this->withPages && $page->hasChildPages(),
        ])->render();
    }
}
