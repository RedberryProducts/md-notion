<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\TableDTO;

class TableAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'table';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.table';
    }

    protected function prepareData(array $block): array
    {
        $dto = TableDTO::from($block);

        // Get table rows from SDK
        $rowBlocks = $this->getSdk()->act()->getBlockChildren($block['id'], null)->json();

        // Process each row
        $rowAdapter = new TableRowAdapter;
        $rowAdapter->setSdk($this->sdk);

        $rows = [];

        foreach ($rowBlocks['results'] as $i => $rowBlock) {
            $rowMd = $rowAdapter->toMarkdown($rowBlock);

            if ($i === 0) {
                $rows[] = $rowMd;
                $separatorCells = array_fill(0, $dto->tableWidth, '---');
                $rows[] = '| '.implode(' | ', $separatorCells).' |';
            } else {
                $rows[] = $rowMd;
            }
        }

        return [
            'rows' => $rows,
            'hasHeader' => false, // Header is now handled in the loop
            'columnCount' => $dto->tableWidth,
            'block' => $dto,
        ];
    }
}
