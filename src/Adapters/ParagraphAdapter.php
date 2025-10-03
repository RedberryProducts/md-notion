<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\ParagraphDTO;

class ParagraphAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'paragraph';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.paragraph';
    }

    protected function prepareData(array $block): array
    {
        $dto = ParagraphDTO::from($block);

        return [
            'content' => $this->processRichText($dto->richText),
            'block' => $dto,
        ];
    }
}
