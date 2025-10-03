<?php

namespace Redberry\MdNotion\DTOs;

class HeadingDTO extends BlockDTO
{
    /**
     * The rich text content of the heading
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * Whether the heading is toggleable
     */
    public bool $isToggleable;

    /**
     * The color of the heading
     */
    public string $color;

    /**
     * The level of the heading (1, 2, or 3)
     */
    public string $level;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->isToggleable = $data['is_toggleable'];
        $this->color = $data['color'];
        $this->level = substr($this->type, -1); // extracts 1, 2, or 3 from heading_X
    }
}
