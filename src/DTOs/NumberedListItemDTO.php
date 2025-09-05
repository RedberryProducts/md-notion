<?php

namespace RedberryProducts\MdNotion\DTOs;

class NumberedListItemDTO
{
    public function __construct(
        public array $richText,
        public string $color
    ) {}

    public static function from(array $block): self
    {
        $content = $block['numbered_list_item'];

        return new self(
            richText: $content['rich_text'],
            color: $content['color']
        );
    }
}
