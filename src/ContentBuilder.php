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
     * Get the Page object with all requested content
     */
    public function get(): Page
    {
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
        $page = $this->get();

        $markdown = '';

        // Add main page title and content
        $markdown .= $page->renderTitle(1)."\n\n";
        if ($page->hasContent()) {
            $markdown .= $page->getContent()."\n\n";
        }

        // Add child databases content
        if ($this->withDatabases && $page->hasChildDatabases()) {
            $markdown .= "## Databases\n\n";
            foreach ($page->getChildDatabases() as $database) {
                $markdown .= $database->renderTitle(3)."\n\n";
                if ($database->hasTableContent()) {
                    $markdown .= $database->getTableContent()."\n\n";
                }
            }
        }

        // Add child pages content
        if ($this->withPages && $page->hasChildPages()) {
            $markdown .= "## Child Pages\n\n";
            foreach ($page->getChildPages() as $childPage) {
                $markdown .= $childPage->renderTitle(3)."\n\n";
                if ($childPage->hasContent()) {
                    $markdown .= $childPage->getContent()."\n\n";
                }
            }
        }

        return trim($markdown);
    }
}
