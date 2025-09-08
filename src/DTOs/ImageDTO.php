<?php

namespace RedberryProducts\MdNotion\DTOs;

class ImageDTO extends BlockDTO
{
    /**
     * The type of image (file or external)
     */
    public string $imageType;

    /**
     * File data if type is file
     */
    public array $file;

    /**
     * External data if type is external
     */
    public array $external;

    /**
     * The caption of the image
     *
     * @var RichTextDTO[]
     */
    public array $caption;

    protected function fromArray(array $data): void
    {
        $this->imageType = $data['type'];
        $this->file = $data['type'] === 'file' ? $data['file'] : [];
        $this->external = $data['type'] === 'external' ? $data['external'] : [];
        $this->caption = RichTextDTO::collection($data['caption'] ?? []);
    }
}
