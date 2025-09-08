<?php

namespace RedberryProducts\MdNotion\DTOs;

class TableRowDTO extends BlockDTO
{
    /**
     * The cells of the table row
     *
     * @var array[]
     */
    public array $cells;

    protected function fromArray(array $data): void
    {
        $this->cells = $data['cells'];
    }
}
