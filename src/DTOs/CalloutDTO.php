<?php

namespace RedberryProducts\MdNotion\DTOs;

class CalloutDTO extends BlockDTO
{
    /**
     * The rich text content of the callout
     *
     * @var RichTextDTO[]
     */
    public array $richText;

    /**
     * The icon of the callout
     *
     * @var array{type: string, emoji?: string, external?: array{url: string}, file?: array{url: string, expiry_time: string}}
     */
    public array $icon;

    /**
     * The color of the callout
     */
    public string $color;

    protected function fromArray(array $data): void
    {
        $this->richText = RichTextDTO::collection($data['rich_text']);
        $this->icon = $data['icon'];
        $this->color = $data['color'];
    }
}
