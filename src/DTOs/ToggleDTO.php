<?php

namespace Redberry\MdNotion\DTOs;

class ToggleDTO extends BlockDTO
{
    /**
     * The rich text content of the toggle
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The color of the toggle
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->color = $data['color'];
    }
}
