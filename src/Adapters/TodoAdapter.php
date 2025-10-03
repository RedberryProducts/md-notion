<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\TodoDTO;

class TodoAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'to_do';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.todo';
    }

    protected function prepareData(array $block): array
    {
        $dto = TodoDTO::from($block);

        return [
            'content' => trim($this->processRichText($dto->richText)),
            'checked' => $dto->checked,
            'block' => $dto,
        ];
    }
}
