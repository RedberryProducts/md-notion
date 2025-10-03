<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\ToggleDTO;

class ToggleAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'toggle';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.toggle';
    }

    protected function prepareData(array $block): array
    {
        $dto = new ToggleDTO($block);

        // Get the title from rich text
        $title = trim($this->processRichText($dto->richText));

        // Get toggle contents from SDK
        $response = $this->getSdk()->act()->getBlockChildren($block['id'], null);
        $contentBlocks = $response->json();

        // Process each child block using corresponding adapters
        $contents = [];
        foreach ($contentBlocks['results'] as $childBlock) {
            // Create adapter based on block type
            $type = $childBlock['type'];
            $className = str_replace('_', '', ucwords($type, '_'));
            $adapterClass = '\\Redberry\\MdNotion\\Adapters\\' . $className . 'Adapter';
            if (class_exists($adapterClass)) {
                $adapter = new $adapterClass;
            } else {
                $adapter = new ParagraphAdapter; // Fallback to paragraph
            }
            $adapter->setSdk($this->sdk);
            $contents[] = trim($adapter->toMarkdown($childBlock));
        }

        return [
            'title' => $title,
            'content' => implode("\n\n", $contents),
            'block' => $dto,
        ];
    }
}
