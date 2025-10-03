<?php

namespace Redberry\MdNotion\DTOs;

class NumberedListItemDTO extends BlockDTO
{
    /**
     * The rich text content of the numbered list item
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The color of the numbered list item
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->color = $data['color'];
    }
}
