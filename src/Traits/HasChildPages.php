<?php

namespace RedberryProducts\MdNotion\Traits;

use Illuminate\Support\Collection;
use RedberryProducts\MdNotion\Objects\Page;

trait HasChildPages
{
    // Optional field
    public ?Collection $childPages = null;

    /**
     * Initialize child collections
     */
    protected function initializeChildCollections(): void
    {
        $this->childPages = $this->childPages ?? collect();
    }

    /**
     * Fill child data from array
     */
    protected function fillChildData(array $data): void
    {
        if (isset($data['childPages'])) {
            $this->childPages = collect($data['childPages'])->map(function ($page) {
                return is_array($page) ? Page::from($page) : $page;
            });
        }
    }

    // Child pages accessors
    public function getChildPages(): Collection
    {
        return $this->childPages ?? collect();
    }

    public function setChildPages(Collection $childPages): self
    {
        $this->childPages = $childPages;

        return $this;
    }

    public function addChildPage(Page $page): self
    {
        if ($this->childPages === null) {
            $this->childPages = collect();
        }
        $this->childPages->push($page);

        return $this;
    }

    public function hasChildPages(): bool
    {
        return $this->childPages && $this->childPages->isNotEmpty();
    }

    /**
     * Get child data for array conversion
     */
    protected function getChildArrayData(): array
    {
        return [
            'childPages' => $this->childPages?->map(fn ($page) => $page->toArray())->toArray(),
        ];
    }
}
