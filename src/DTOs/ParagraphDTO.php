<?php

namespace Redberry\MdNotion\DTOs;

class ParagraphDTO extends BlockDTO
{
    /**
     * The rich text content of the paragraph
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The color of the paragraph
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->color = $data['color'];
    }
}
