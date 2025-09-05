<?php

namespace RedberryProducts\MdNotion\DTOs;

class ToggleDTO
{
    public function __construct(
        public array $richText,
        public string $color
    ) {
    }

    public static function from(array $block): self
    {
        $toggle = $block['toggle'];
        return new self(
            richText: $toggle['rich_text'],
            color: $toggle['color']
        );
    }
}
