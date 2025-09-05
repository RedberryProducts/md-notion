<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\DTOs\HeadingDTO;
use RedberryProducts\MdNotion\DTOs\RichTextDTO;

class HeadingAdapter extends BaseBlockAdapter
{
    public function getType(): string
    {
        return 'heading_' . $this->level;
    }

    public function getTemplate(): string
    {
        return 'md-notion::blocks.heading';
    }

    protected function prepareData(array $block): array
    {
        $dto = HeadingDTO::from($block);

        return [
            'level' => (int) $dto->level,
            'content' => trim($this->processRichText(RichTextDTO::collection($dto->richText))),
            'block' => $dto,
        ];
    }

    protected int $level;

    public function __construct(int $level)
    {
        $this->level = $level;
        parent::__construct();
    }
}
