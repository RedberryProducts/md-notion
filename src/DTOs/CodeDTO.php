<?php

namespace RedberryProducts\MdNotion\DTOs;

class CodeDTO extends BlockDTO
{
    /**
     * The rich text content of the code block
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The programming language of the code block
     */
    public string $language;

    /**
     * The caption for the code block
     *
     * @var RichTextDTO[]
     */
    public array $caption;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->language = $data['language'];
        $this->caption = RichTextDTO::collection($data['caption'] ?? []);
    }
}
