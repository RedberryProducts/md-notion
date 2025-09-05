<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\QuoteDTO;
use RedberryProducts\MdNotion\DTOs\RichTextDTO;

class QuoteAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'quote';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.quote';
    }

    protected function prepareData(array $block): array
    {
        $dto = QuoteDTO::from($block);

        return [
            'content' => trim($this->processRichText(RichTextDTO::collection($dto->richText))),
            'block' => $dto,
        ];
    }
}
