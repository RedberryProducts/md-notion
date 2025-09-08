<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\ColumnDTO;

class ColumnAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'column';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.column';
    }

    protected function prepareData(array $block): array
    {
        $dto = ColumnDTO::from($block);
        
        // Get column contents from SDK
        $response = $this->getSdk()->act()->getBlockChildren($block['id'], null);
        $contentBlocks = $response->json();
        
        // Process each child block using BlockAdapterFactory
        $contents = [];
        foreach ($contentBlocks['results'] ?? [] as $childBlock) {
            // Create adapter based on block type
            $type = $childBlock['type'];
            
            // Convert snake_case to PascalCase for class name
            $className = str_replace('_', '', ucwords($type, '_'));
            $adapterClass = '\\RedberryProducts\\MdNotion\\Adapters\\' . $className . 'Adapter';
            
            if (class_exists($adapterClass)) {
                $adapter = new $adapterClass();
            } else {
                $adapter = new \RedberryProducts\MdNotion\Adapters\ParagraphAdapter(); // Fallback to paragraph
            }
            $adapter->setSdk($this->sdk);
            $contents[] = trim($adapter->toMarkdown($childBlock));
        }

        return [
            'content' => implode("\n\n", $contents),
            'widthRatio' => $dto->widthRatio,
            'block' => $dto,
        ];
    }
}
