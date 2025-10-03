<?php

namespace Redberry\MdNotion\DTOs;

class BookmarkDTO extends BlockDTO
{
    /**
     * The URL of the bookmark
     */
    public string $url;

    /**
     * The caption of the bookmark
     *
     * @var RichTextDTO[]
     */
    public array $caption;

    protected function fromArray(array $data): void
    {
        $this->url = $data['url'];
        $this->caption = RichTextDTO::collection($data['caption'] ?? []);
    }
}
