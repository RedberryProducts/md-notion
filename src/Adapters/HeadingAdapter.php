<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\HeadingDTO;
use RedberryProducts\MdNotion\DTOs\RichTextDTO;

class HeadingAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'heading';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.heading';
    }

    protected function prepareData(array $block): array
    {
        $dto = HeadingDTO::from($block);
        
        // Extract level from the block type (heading_1 -> 1, heading_2 -> 2, etc.)
        $level = $this->extractLevelFromBlockType($block['type']);

        return [
            'level' => $level,
            'content' => trim($this->processRichText($dto->richText)),
            'block' => $dto,
        ];
    }

    /**
     * Extract heading level from block type string
     */
    private function extractLevelFromBlockType(string $blockType): int
    {
        if (preg_match('/heading_(\d+)/', $blockType, $matches)) {
            return (int) $matches[1];
        }
        
        return 1; // Default to level 1
    }

    /**
     * Override toMarkdown to handle dynamic type checking for heading types
     */
    public function toMarkdown(array $block): string
    {
        // Validate that this is a heading type
        if (!preg_match('/^heading_\d+$/', $block['type'])) {
            throw new \InvalidArgumentException("Block type '{$block['type']}' is not a valid heading type");
        }

        $data = $this->prepareData($block);
        
        return trim(\Illuminate\Support\Facades\View::make($this->template, $data)->render());
    }
}
