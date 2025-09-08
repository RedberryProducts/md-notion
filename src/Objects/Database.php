<?php

namespace RedberryProducts\MdNotion\Objects;

use Illuminate\Support\Collection;

class Database extends BaseObject
{
    /**
     * Database content as markdown table
     */
    public ?string $tableContent = null;

    /**
     * Database items as Page objects
     */
    public ?Collection $items = null;

    /**
     * Create a new Database instance
     */
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->items = $this->items ?? collect();
    }

    /**
     * Fill the database with data
     */
    public function fill(array $data): static
    {
        parent::fill($data);

        $this->tableContent = $data['tableContent'] ?? $this->tableContent;

        // Handle database items
        if (isset($data['items'])) {
            $this->items = collect($data['items'])->map(function ($item) {
                return is_array($item) ? Page::from($item) : $item;
            });
        }

        return $this;
    }

    /**
     * Fetch database items as markdown table using DatabaseTable service
     *
     * @param \RedberryProducts\MdNotion\Services\DatabaseTable $databaseTable
     * @return static
     */
    public function fetchAsTable(\RedberryProducts\MdNotion\Services\DatabaseTable $databaseTable): static
    {
        $this->tableContent = $databaseTable->fetchDatabaseAsMarkdownTable($this->id);
        
        return $this;
    }

    /**
     * Fetch database items as Page objects with content
     *
     * @param \RedberryProducts\MdNotion\Services\DatabaseTable $databaseTable
     * @return static
     */
    public function fetchItems(\RedberryProducts\MdNotion\Services\DatabaseTable $databaseTable): static
    {
        $this->items = $databaseTable->fetchDatabaseItems($this->id);
        
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
     * Get database items
     */
    public function getItems(): Collection
    {
        return $this->items ?? collect();
    }

    /**
     * Set database items
     */
    public function setItems(Collection $items): static
    {
        $this->items = $items;
        
        return $this;
    }

    /**
     * Check if database has items
     */
    public function hasItems(): bool
    {
        return $this->items && $this->items->isNotEmpty();
    }

    /**
     * Check if database has table content
     */
    public function hasTableContent(): bool
    {
        return !empty($this->tableContent);
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
                'items' => $this->items?->map(fn ($item) => $item->toArray())->toArray(),
            ]
        );
    }
}
