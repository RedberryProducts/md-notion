<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\DividerDTO;

class DividerAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'divider';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.divider';
    }

    protected function prepareData(array $block): array
    {
        return [
            'block' => DividerDTO::from($block),
        ];
    }
}
