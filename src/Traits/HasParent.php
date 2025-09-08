<?php

namespace RedberryProducts\MdNotion\Traits;

trait HasParent
{
    // Required field
    public array $parent;

    /**
     * Fill parent data from array
     */
    protected function fillParentData(array $data): void
    {
        $this->parent = $data['parent'] ?? $this->parent ?? [];
    }

    // Parent accessors
    public function getParent(): array
    {
        return $this->parent;
    }

    public function setParent(array $parent): self
    {
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get the parent type
     */
    public function getParentType(): ?string
    {
        return $this->parent['type'] ?? null;
    }

    /**
     * Get the parent ID based on type
     */
    public function getParentId(): ?string
    {
        $type = $this->getParentType();
        
        if (!$type) {
            return null;
        }

        return $this->parent[$type] ?? null;
    }

    /**
     * Check if has parent
     */
    public function hasParent(): bool
    {
        return !empty($this->parent);
    }

    /**
     * Get parent data for array conversion
     */
    protected function getParentArrayData(): array
    {
        return [
            'parent' => $this->parent,
        ];
    }
}
