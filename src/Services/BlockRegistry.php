<?php

namespace RedberryProducts\MdNotion\Services;

use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\Adapters\BlockAdapterInterface;

class BlockRegistry
{
    public function __construct(
        private BlockAdapterFactory $factory
    ) {}

    /**
     * Resolve a block type to its corresponding adapter
     *
     * @param string $blockType The Notion block type
     * @return BlockAdapterInterface The adapter instance
     */
    public function resolve(string $blockType): BlockAdapterInterface
    {
        return $this->factory->create($blockType);
    }

    /**
     * Get all registered block types
     *
     * @return array<string>
     */
    public function getRegisteredBlockTypes(): array
    {
        return $this->factory->getRegisteredBlockTypes();
    }
}