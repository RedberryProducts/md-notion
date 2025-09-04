<?php

namespace RedberryProducts\MdNotion\DTOs;

abstract class BlockDTO
{
    /**
     * The block ID from Notion
     */
    public string $id;

    /**
     * The block type from Notion
     */
    public string $type;

    /**
     * Whether the block has children
     */
    public bool $hasChildren;

    /**
     * Create a new block DTO from Notion API response
     */
    public function __construct(array $block)
    {
        $this->id = $block['id'];
        $this->type = $block['type'];
        $this->hasChildren = $block['has_children'] ?? false;

        $this->fromArray($block[$this->type]);
    }

    /**
     * Create a new instance of the DTO from a Notion API response
     */
    public static function from(array $block): static
    {
        return new static($block);
    }

    /**
     * Map the block type specific data to DTO properties
     */
    abstract protected function fromArray(array $data): void;
}
