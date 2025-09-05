<?php

namespace RedberryProducts\MdNotion\DTOs;

class VideoDTO
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
        $video = $block['video'];
        return new self(
            type: $video['type'],
            file: $video['type'] === 'file' ? $video['file'] : [],
            external: $video['type'] === 'external' ? $video['external'] : [],
            caption: $video['caption'] ?? []
        );
    }
}
