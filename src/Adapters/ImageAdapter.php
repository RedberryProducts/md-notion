<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\ImageDTO;

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

        $url = $dto->imageType === 'file'
            ? $dto->file['url']
            : $dto->external['url'];

        return [
            'url' => $url,
            'caption' => trim($this->processRichText($dto->caption)),
            'block' => $dto,
        ];
    }
}
