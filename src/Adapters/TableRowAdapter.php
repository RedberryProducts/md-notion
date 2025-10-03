<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\RichTextDTO;
use Redberry\MdNotion\DTOs\TableRowDTO;

class TableRowAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'table_row';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.table-row';
    }

    protected function prepareData(array $block): array
    {
        $dto = TableRowDTO::from($block);

        $cells = [];
        foreach ($dto->cells as $cell) {
            $cells[] = trim($this->processRichText(RichTextDTO::collection($cell)));
        }

        return [
            'cells' => $cells,
            'block' => $dto,
        ];
    }
}
