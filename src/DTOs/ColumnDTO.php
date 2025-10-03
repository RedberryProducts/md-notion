<?php

namespace Redberry\MdNotion\DTOs;

class ColumnDTO extends BlockDTO
{
    /**
     * The width ratio of the column
     */
    public float $widthRatio;

    protected function fromArray(array $data): void
    {
        $this->widthRatio = $data['width_ratio'] ?? 1.0;
    }
}
