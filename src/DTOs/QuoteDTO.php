<?php

namespace RedberryProducts\MdNotion\DTOs;

class QuoteDTO
{
    public function __construct(
        public array $richText,
        public string $color
    ) {}

    public static function from(array $block): self
    {
        $content = $block['quote'];

        return new self(
            richText: $content['rich_text'],
            color: $content['color']
        );
    }
}
