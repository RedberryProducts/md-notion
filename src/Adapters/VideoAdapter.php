<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\VideoDTO;

class VideoAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'video';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.video';
    }

    protected function prepareData(array $block): array
    {
        $dto = VideoDTO::from($block);

        $url = $dto->videoType === 'file'
            ? $dto->file['url']
            : $dto->external['url'];

        return [
            'url' => $url,
            'caption' => trim($this->processRichText($dto->caption)),
            'block' => $dto,
        ];
    }
}
