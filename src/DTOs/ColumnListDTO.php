<?php

namespace RedberryProducts\MdNotion\DTOs;

class ColumnListDTO
{
    public function __construct()
    {
        // Column list doesn't have any properties in the JSON
    }

    public static function from(array $block): self
    {
        return new self;
    }
}
