<?php

namespace RedberryProducts\MdNotion\DTOs;

class ImageDTO
{
    public function __construct(
        public string $type,
        public array $file,
        public array $external,
        public array $caption
    ) {
    }

    public static function from(array $block): self
    {
        $image = $block['image'];
        return new self(
            type: $image['type'],
            file: $image['type'] === 'file' ? $image['file'] : [],
            external: $image['type'] === 'external' ? $image['external'] : [],
            caption: $image['caption'] ?? []
        );
    }
}
