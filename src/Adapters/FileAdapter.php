<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\FileDTO;

class FileAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'file';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.file';
    }

    protected function prepareData(array $block): array
    {
        $dto = FileDTO::from($block);

        $url = $dto->type === 'file'
            ? $dto->file['url']
            : $dto->external['url'];

        return [
            'name' => $dto->name,
            'url' => $url,
            'type' => $dto->type,
            'caption' => $this->processRichText($dto->caption),
            'block' => $dto,
        ];
    }
}
