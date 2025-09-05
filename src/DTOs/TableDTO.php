<?php

namespace RedberryProducts\MdNotion\DTOs;

class TableDTO
{
    public function __construct(
        public int $tableWidth,
        public bool $hasColumnHeader,
        public bool $hasRowHeader
    ) {}

    public static function from(array $block): self
    {
        $table = $block['table'];

        return new self(
            tableWidth: $table['table_width'],
            hasColumnHeader: $table['has_column_header'],
            hasRowHeader: $table['has_row_header']
        );
    }
}
