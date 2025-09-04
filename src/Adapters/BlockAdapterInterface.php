<?php

namespace RedberryProducts\MdNotion\Adapters;

interface BlockAdapterInterface
{
    /**
     * Convert a Notion block to Markdown format
     *
     * @param  array  $block  The Notion block data
     * @return string The markdown representation
     */
    public function toMarkdown(array $block): string;

    /**
     * Get the Notion block type this adapter handles
     */
    public function getType(): string;

    /**
     * Get the blade template path for rendering markdown
     */
    public function getTemplate(): string;
}
