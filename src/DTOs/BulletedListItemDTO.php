<?php

namespace RedberryProducts\MdNotion\DTOs;

class BulletedListItemDTO extends BlockDTO
{
    /**
     * The rich text content of the bulleted list item
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The color of the bulleted list item
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->color = $data['color'];
    }
}
