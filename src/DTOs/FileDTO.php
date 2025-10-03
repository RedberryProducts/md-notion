<?php

namespace Redberry\MdNotion\DTOs;

class FileDTO extends BlockDTO
{
    /**
     * The type of the file (external or file)
     */
    public string $type;

    /**
     * The caption of the file
     *
     * @var RichTextDTO[]
     */
    public array $caption;

    /**
     * The file data for internal files
     *
     * @var array{url: string, expiry_time?: string}|null
     */
    public ?array $file = null;

    /**
     * The external file data
     *
     * @var array{url: string}|null
     */
    public ?array $external = null;

    /**
     * The name of the file
     */
    public string $name;

    protected function fromArray(array $data): void
    {
        $this->type = $data['type'];
        $this->caption = RichTextDTO::collection($data['caption'] ?? []);

        if ($this->type === 'file') {
            $this->file = $data['file'];
            $this->name = $data['name'] ?? basename($this->file['url']);
        } else {
            $this->external = $data['external'];
            $this->name = $data['name'] ?? basename($this->external['url']);
        }
    }
}
