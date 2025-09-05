<?php

namespace RedberryProducts\MdNotion\DTOs;

class TodoDTO
{
    public function __construct(
        public array $richText,
        public bool $checked,
        public string $color
    ) {
    }

    public static function from(array $block): self
    {
        $content = $block['to_do'];
        return new self(
            richText: $content['rich_text'],
            checked: $content['checked'],
            color: $content['color']
        );
    }
}
