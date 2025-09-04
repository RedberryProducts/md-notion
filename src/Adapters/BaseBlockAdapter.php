<?php

namespace RedberryProducts\MdNotion\Adapters;

use Illuminate\Support\Facades\View;

abstract class BaseBlockAdapter implements BlockAdapterInterface
{
    /**
     * The Notion block type this adapter handles
     *
     * @var string
     */
    protected string $type;

    /**
     * The blade template path for rendering markdown
     *
     * @var string
     */
    protected string $template;

    /**
     * Create a new block adapter instance
     */
    public function __construct()
    {
        $this->type = $this->getType();
        $this->template = $this->getTemplate();
    }

    /**
     * Get the Notion block type this adapter handles
     *
     * @return string
     */
    abstract public function getType(): string;

    /**
     * Get the blade template path for rendering markdown
     *
     * @return string
     */
    abstract public function getTemplate(): string;

    /**
     * Convert a Notion block to Markdown format
     *
     * @param array $block The Notion block data
     * @return string The markdown representation
     */
    public function toMarkdown(array $block): string
    {
        if ($block['type'] !== $this->type) {
            throw new \InvalidArgumentException("Block type '{$block['type']}' does not match adapter type '{$this->type}'");
        }

        $data = $this->prepareData($block);
        
        return trim(View::make($this->template, $data)->render());
    }

    /**
     * Prepare data for the template
     *
     * @param array $block The Notion block data
     * @return array
     */
    protected function prepareData(array $block): array
    {
        return [
            'content' => $this->getBlockContent($block),
            'block' => $block,
        ];
    }

    /**
     * Process text annotations and convert to markdown
     *
     * @param array $annotations Text annotations from Notion
     * @param string $text The text content
     * @return string
     */
    protected function processAnnotations(array $annotations, string $text): string
    {
        if ($annotations['code']) {
            $text = "`{$text}`";
        }
        if ($annotations['bold']) {
            $text = "**{$text}**";
        }
        if ($annotations['italic']) {
            $text = "_{$text}_";
        }
        if ($annotations['strikethrough']) {
            $text = "~~{$text}~~";
        }
        if ($annotations['underline']) {
            $text = "<u>{$text}</u>";
        }
        
        return $text;
    }

    /**
     * Process rich text blocks from Notion
     *
     * @param \RedberryProducts\MdNotion\DTOs\RichTextDTO[] $richText Array of rich text DTOs
     * @return string
     */
    protected function processRichText(array $richText): string
    {
        $result = '';
        
        foreach ($richText as $textBlock) {
            $text = trim($textBlock->plainText);

            if ($textBlock->href) {
                $text = "[$text]({$textBlock->href})";
            }
            
            $text = $this->processAnnotations($textBlock->annotations, $text);
            $result .= " " . $text;
        }
        
        return $result;
    }

    /**
     * Get the content from a block's text property
     *
     * @param array $block The Notion block
     * @return string
     */
    protected function getBlockContent(array $block): string
    {
        $blockType = $block['type'];
        return isset($block[$blockType]['rich_text']) 
            ? $this->processRichText($block[$blockType]['rich_text'])
            : '';
    }
}
