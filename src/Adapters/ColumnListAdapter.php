<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\ColumnListDTO;

class ColumnListAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'column_list';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.column_list';
    }

    protected function prepareData(array $block): array
    {
        $dto = ColumnListDTO::from($block);

        // Get columns from SDK
        $response = $this->getSdk()->act()->getBlockChildren($block['id'], null);
        $columnBlocks = $response->json();

        // Process each column using ColumnAdapter
        $columns = [];
        foreach ($columnBlocks['results'] ?? [] as $index => $columnBlock) {
            $columnAdapter = new ColumnAdapter;
            $columnAdapter->setSdk($this->sdk);

            // Add column number as title
            $columnNumber = $index + 1;
            $columns[] = [
                'title' => "Column {$columnNumber}",
                'content' => trim($columnAdapter->toMarkdown($columnBlock)),
            ];
        }

        return [
            'columns' => $columns,
            'block' => $dto,
        ];
    }
}
