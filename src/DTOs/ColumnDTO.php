<?php

namespace RedberryProducts\MdNotion\DTOs;

class ColumnDTO
{
    public function __construct(
        public float $widthRatio
    ) {
    }

    public static function from(array $block): self
    {
        return new self(
            widthRatio: $block['column']['width_ratio']
        );
    }
}
