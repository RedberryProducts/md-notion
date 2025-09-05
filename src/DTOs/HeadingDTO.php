<?php

namespace RedberryProducts\MdNotion\DTOs;

class HeadingDTO
{
    public function __construct(
        public array $richText,
        public bool $isToggleable,
        public string $color,
        public string $level
    ) {}

    public static function from(array $block): self
    {
        $type = $block['type']; // heading_1, heading_2, or heading_3
        $heading = $block[$type];

        return new self(
            richText: $heading['rich_text'],
            isToggleable: $heading['is_toggleable'],
            color: $heading['color'],
            level: substr($type, -1) // extracts 1, 2, or 3 from heading_X
        );
    }
}
