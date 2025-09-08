<?php

namespace RedberryProducts\MdNotion\Traits;

trait HasMeta
{
    // Required fields
    public string $created_time;

    public string $last_edited_time;

    public array $created_by;

    public array $last_edited_by;

    public bool $archived;

    public bool $in_trash;

    /**
     * Fill meta data from array
     */
    protected function fillMetaData(array $data): void
    {
        $this->created_time = $data['created_time'] ?? $this->created_time ?? '';
        $this->last_edited_time = $data['last_edited_time'] ?? $this->last_edited_time ?? '';
        $this->created_by = $data['created_by'] ?? $this->created_by ?? [];
        $this->last_edited_by = $data['last_edited_by'] ?? $this->last_edited_by ?? [];
        $this->archived = $data['archived'] ?? $this->archived ?? false;
        $this->in_trash = $data['in_trash'] ?? $this->in_trash ?? false;
    }

    // Created time accessors
    public function getCreatedTime(): string
    {
        return $this->created_time;
    }

    public function setCreatedTime(string $created_time): self
    {
        $this->created_time = $created_time;

        return $this;
    }

    // Last edited time accessors
    public function getLastEditedTime(): string
    {
        return $this->last_edited_time;
    }

    public function setLastEditedTime(string $last_edited_time): self
    {
        $this->last_edited_time = $last_edited_time;

        return $this;
    }

    // Created by accessors
    public function getCreatedBy(): array
    {
        return $this->created_by;
    }

    public function setCreatedBy(array $created_by): self
    {
        $this->created_by = $created_by;

        return $this;
    }

    // Last edited by accessors
    public function getLastEditedBy(): array
    {
        return $this->last_edited_by;
    }

    public function setLastEditedBy(array $last_edited_by): self
    {
        $this->last_edited_by = $last_edited_by;

        return $this;
    }

    // Archived boolean accessors
    public function isArchived(): bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

        return $this;
    }

    // In trash boolean accessors
    public function isTrashed(): bool
    {
        return $this->in_trash;
    }

    public function isInTrash(): bool
    {
        return $this->in_trash;
    }

    public function setInTrash(bool $in_trash): self
    {
        $this->in_trash = $in_trash;

        return $this;
    }

    /**
     * Get meta data for array conversion
     */
    protected function getMetaArrayData(): array
    {
        return [
            'created_time' => $this->created_time,
            'last_edited_time' => $this->last_edited_time,
            'created_by' => $this->created_by,
            'last_edited_by' => $this->last_edited_by,
            'archived' => $this->archived,
            'in_trash' => $this->in_trash,
        ];
    }
}
