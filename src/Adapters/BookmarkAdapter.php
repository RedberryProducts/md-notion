<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\BookmarkDTO;

class BookmarkAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'bookmark';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.bookmark';
    }

    protected function prepareData(array $block): array
    {
        $dto = BookmarkDTO::from($block);

        return [
            'url' => $dto->url,
            'domain' => preg_replace('/^https?:\/\/(www\.)?([^\/]+).*$/', '$2', $dto->url),
            'caption' => $this->processRichText($dto->caption),
            'block' => $dto,
        ];
    }
}
