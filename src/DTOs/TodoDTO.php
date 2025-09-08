<?php

namespace RedberryProducts\MdNotion\DTOs;

class TodoDTO extends BlockDTO
{
    /**
     * The rich text content of the todo
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * Whether the todo is checked
     */
    public bool $checked;

    /**
     * The color of the todo
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->checked = $data['checked'];
        $this->color = $data['color'];
    }
}
