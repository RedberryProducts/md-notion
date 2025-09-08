<?php

namespace RedberryProducts\MdNotion\DTOs;

class VideoDTO extends BlockDTO
{
    /**
     * The type of video (file or external)
     */
    public string $videoType;

    /**
     * File data if type is file
     */
    public array $file;

    /**
     * External data if type is external
     */
    public array $external;

    /**
     * The caption of the video
     *
     * @var RichTextDTO[]
     */
    public array $caption;

    protected function fromArray(array $data): void
    {
        $this->videoType = $data['type'];
        $this->file = $data['type'] === 'file' ? $data['file'] : [];
        $this->external = $data['type'] === 'external' ? $data['external'] : [];
        $this->caption = RichTextDTO::collection($data['caption'] ?? []);
    }
}
