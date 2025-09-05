<?php

namespace RedberryProducts\MdNotion\DTOs;

class TableRowDTO
{
    public function __construct(
        public array $cells
    ) {}

    public static function from(array $block): self
    {
        return new self(
            cells: $block['table_row']['cells']
        );
    }
}
