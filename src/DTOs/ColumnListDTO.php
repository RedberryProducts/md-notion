<?php

namespace RedberryProducts\MdNotion\DTOs;

class ColumnListDTO extends BlockDTO
{
    protected function fromArray(array $data): void
    {
        // Column list doesn't have any properties in the JSON
    }
}
