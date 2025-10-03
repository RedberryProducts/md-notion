<?php

namespace Redberry\MdNotion\DTOs;

class TableDTO extends BlockDTO
{
    /**
     * The width of the table
     */
    public int $tableWidth;

    /**
     * Whether the table has column headers
     */
    public bool $hasColumnHeader;

    /**
     * Whether the table has row headers
     */
    public bool $hasRowHeader;

    protected function fromArray(array $data): void
    {
        $this->tableWidth = $data['table_width'];
        $this->hasColumnHeader = $data['has_column_header'];
        $this->hasRowHeader = $data['has_row_header'];
    }
}
