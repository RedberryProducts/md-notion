<?php

namespace Redberry\MdNotion\DTOs;

class RichTextDTO
{
    /**
     * The plain text content
     */
    public string $plainText;

    /**
     * The text annotations
     */
    public array $annotations;

    /**
     * The type of rich text (text, mention, equation)
     */
    public string $type;

    /**
     * The href if the text is a link
     */
    public ?string $href;

    /**
     * Create a new rich text DTO from Notion API response
     */
    public function __construct(array $richText)
    {
        $this->plainText = $richText['plain_text'];
        $this->annotations = $richText['annotations'] ?? [];
        $this->type = $richText['type'];
        $this->href = $richText['href'] ?? null;

        // Handle specific types (text, mention, equation)
        if ($this->type === 'text' && isset($richText['text']['link']['url'])) {
            $this->href = $richText['text']['link']['url'];
        } elseif ($this->type === 'mention' && isset($richText['mention']['page']['id'])) {
            $this->href = "notion://page/{$richText['mention']['page']['id']}";
        }
    }

    /**
     * Create a collection of RichTextDTO from an array of rich text
     *
     * @param  array  $richTextArray  Array of rich text from Notion API
     * @return RichTextDTO[]
     */
    public static function collection(array $richTextArray): array
    {
        return array_map(fn ($item) => new static($item), $richTextArray);
    }
}
