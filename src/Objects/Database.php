<?php

namespace RedberryProducts\MdNotion\Objects;

class Database extends BaseObject
{
    /**
     * Database content as markdown table
     */
    public ?string $tableContent = null;

    /**
     * Create a new Database instance
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
    }

    /**
     * Fill the database with data
     */
    public function fill(array $data): static
    {
        parent::fill($data);

        $this->tableContent = $data['tableContent'] ?? $this->tableContent;

        return $this;
    }

    /**
     * Read items content using PageReader service
     */
    public function readItemsContent(\RedberryProducts\MdNotion\Services\PageReader $pageReader): static
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
     * Get table content
     */
    public function getTableContent(): ?string
    {
        return $this->tableContent;
    }

    /**
     * Set table content
     */
    public function setTableContent(?string $tableContent): static
    {
        $this->tableContent = $tableContent;

        return $this;
    }

    /**
     * Check if database has table content
     */
    public function hasTableContent(): bool
    {
        return ! empty($this->tableContent);
    }

    /**
     * Fetch and populate this database using DatabaseReader
     */
    public function fetch(\RedberryProducts\MdNotion\Services\DatabaseReader $databaseReader): static
    {
        $fetchedDatabase = $databaseReader->read($this->getId());

        // Copy all data from the fetched database to this instance
        $this->fill($fetchedDatabase->toArray());

        return $this;
    }

    /**
     * Convert the database to an array
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'tableContent' => $this->tableContent,
            ]
        );
    }
}
