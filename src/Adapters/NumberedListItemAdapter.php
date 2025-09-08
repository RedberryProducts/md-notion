<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\NumberedListItemDTO;
use RedberryProducts\MdNotion\DTOs\RichTextDTO;

class NumberedListItemAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'numbered_list_item';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.numbered-list-item';
    }

    protected function prepareData(array $block): array
    {
        $dto = NumberedListItemDTO::from($block);

        return [
            'content' => trim($this->processRichText($dto->richText)),
            'block' => $dto,
        ];
    }
}
