<?php

namespace Redberry\MdNotion\DTOs;

class QuoteDTO extends BlockDTO
{
    /**
     * The rich text content of the quote
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The color of the quote
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->color = $data['color'];
    }
}
