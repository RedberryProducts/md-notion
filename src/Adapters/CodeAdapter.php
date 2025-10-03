<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\CodeDTO;

class CodeAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'code';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.code';
    }

    protected function prepareData(array $block): array
    {
        $dto = CodeDTO::from($block);

        $content = $this->processRichText($dto->richText);
        // Convert literal \n sequences to actual line breaks
        $content = str_replace('\\n', "\n", $content);

        return [
            'content' => $content,
            'language' => $dto->language,
            'caption' => $this->processRichText($dto->caption),
            'block' => $dto,
        ];
    }
}
