<?php

namespace RedberryProducts\MdNotion\DTOs;

class ColumnDTO extends BlockDTO
{
    /**
     * The width ratio of the column
     */
    public float $widthRatio;

    protected function fromArray(array $data): void
    {
        $this->widthRatio = $data['width_ratio'];
    }
}
