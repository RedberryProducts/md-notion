<?php

namespace Redberry\MdNotion\Adapters;

use Redberry\MdNotion\DTOs\CalloutDTO;

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

        // @todo: process children if any block (rare for callout, but possible)
        // Check example in src\Adapters\ColumnAdapter.php

        return [
            'content' => $this->processRichText($dto->richText),
            'icon' => $icon,
            'block' => $dto,
        ];
    }
}
