<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\ImageDTO;
use RedberryProducts\MdNotion\DTOs\RichTextDTO;

class ImageAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'image';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.image';
    }

    protected function prepareData(array $block): array
    {
        $dto = ImageDTO::from($block);

        $url = $dto->type === 'file'
            ? $dto->file['url']
            : $dto->external['url'];

        return [
            'url' => $url,
            'caption' => trim($this->processRichText(RichTextDTO::collection($dto->caption))),
            'block' => $dto,
        ];
    }
}
