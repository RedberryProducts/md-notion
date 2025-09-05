<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\CalloutDTO;

class CalloutAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'callout';
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.callout';
    }

    protected function prepareData(array $block): array
    {
        $dto = CalloutDTO::from($block);

        $icon = $this->processIcon($dto->icon);

        return [
            'content' => $this->processRichText($dto->richText),
            'icon' => $icon,
            'block' => $dto,
        ];
    }
}
