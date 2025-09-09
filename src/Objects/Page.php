<?php

namespace RedberryProducts\MdNotion\Objects;

use Illuminate\Support\Collection;

class Page extends BaseObject
{
    // Page-specific fields
    public bool $has_children;

    public ?string $content = null;

    public ?Collection $childDatabases = null;

    /**
     * Create a new Page instance
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->childDatabases = $this->childDatabases ?? collect();
    }

    /**
     * Fill the page with data
     */
    public function fill(array $data): static
    {
        parent::fill($data);

        $this->has_children = $data['has_children'] ?? $this->has_children ?? false;
        $this->content = $data['content'] ?? $this->content;

        // Handle child databases specific to Page
        if (isset($data['childDatabases'])) {
            $this->childDatabases = collect($data['childDatabases'])->map(function ($database) {
                return is_array($database) ? Database::from($database) : $database;
            });
        }

        return $this;
    }

    // Has children boolean accessors
    public function hasChildren(): bool
    {
        return $this->has_children;
    }

    public function setHasChildren(bool $has_children): self
    {
        $this->has_children = $has_children;

        return $this;
    }

    // Content accessors
    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function hasContent(): bool
    {
        return ! empty($this->content);
    }

    // Child databases accessors (Page-specific)
    public function getChildDatabases(): Collection
    {
        return $this->childDatabases ?? collect();
    }

    public function setChildDatabases(Collection $childDatabases): self
    {
        $this->childDatabases = $childDatabases;

        return $this;
    }

    public function addChildDatabase(Database $database): self
    {
        if ($this->childDatabases === null) {
            $this->childDatabases = collect();
        }
        $this->childDatabases->push($database);

        return $this;
    }

    public function hasChildDatabases(): bool
    {
        return $this->childDatabases && $this->childDatabases->isNotEmpty();
    }

    /**
     * Read child pages content using PageReader service
     *
     * @param \RedberryProducts\MdNotion\Services\PageReader $pageReader
     * @return static
     */
    public function readChildPagesContent(\RedberryProducts\MdNotion\Services\PageReader $pageReader): static
    {
        if ($this->hasChildPages()) {
            $this->setChildPages(
                $this->getChildPages()->map(function (Page $page) use ($pageReader) {
                    return $pageReader->read($page->getId());
                })
            );
        }

        return $this;
    }

    /**
     * Read child databases content using DatabaseReader service
     *
     * @param \RedberryProducts\MdNotion\Services\DatabaseReader $databaseReader
     * @return static
     */
    public function readChildDatabasesContent(\RedberryProducts\MdNotion\Services\DatabaseReader $databaseReader): static
    {
        if ($this->hasChildDatabases()) {
            $this->setChildDatabases(
                $this->getChildDatabases()->map(function (Database $database) use ($databaseReader) {
                    return $databaseReader->read($database->getId());
                })
            );
        }

        return $this;
    }

    /**
     * Read all child pages content recursively using PageReader service
     * 
     * WARNING: This method makes recursive API calls and may result in many requests.
     * It may slow down your application or hit Notion API limits.
     *
     * @param \RedberryProducts\MdNotion\Services\PageReader $pageReader
     * @return static
     */
    public function readAllPagesContent(\RedberryProducts\MdNotion\Services\PageReader $pageReader): static
    {
        if ($this->hasChildPages()) {
            // First, read the content of immediate child pages
            $this->readChildPagesContent($pageReader);
            
            // Then recursively read content of nested child pages
            $this->getChildPages()->each(function (Page $page) use ($pageReader) {
                $page->readAllPagesContent($pageReader);
            });
        }

        return $this;
    }

    /**
     * Convert the page to an array
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'has_children' => $this->has_children,
                'content' => $this->content,
                'childDatabases' => $this->childDatabases?->map(fn ($db) => $db->toArray())->toArray(),
            ]
        );
    }
}
