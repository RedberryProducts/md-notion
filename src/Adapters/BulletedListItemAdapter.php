<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\BulletedListItemDTO;

class BulletedListItemAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'bulleted_list_item';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.bulleted-list-item';
    }

    protected function prepareData(array $block): array
    {
        $dto = BulletedListItemDTO::from($block);

        return [
            'content' => $this->processRichText($dto->richText),
            'block' => $dto,
        ];
    }
}
