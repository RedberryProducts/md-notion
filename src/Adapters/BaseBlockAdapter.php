<?php

namespace RedberryProducts\MdNotion\Adapters;

use Illuminate\Support\Facades\View;
use RedberryProducts\MdNotion\SDK\Notion;

abstract class BaseBlockAdapter implements BlockAdapterInterface
{
    /**
     * The Notion block type this adapter handles
     */
    protected string $type;

    /**
     * The blade template path for rendering markdown
     */
    protected string $template;

    /**
     * The Notion SDK instance
     */
    protected ?Notion $sdk = null;

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
     */
    abstract public function getType(): string;

    /**
     * Get the blade template path for rendering markdown
     */
    abstract public function getTemplate(): string;

    /**
     * Convert a Notion block to Markdown format
     *
     * @param  array  $block  The Notion block data
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
     * @param  array  $block  The Notion block data
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
     * @param  array  $annotations  Text annotations from Notion
     * @param  string  $text  The text content
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
     * @param  \RedberryProducts\MdNotion\DTOs\RichTextDTO[]  $richText  Array of rich text DTOs
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
            $result .= ' '.$text;
        }

        return $result;
    }

    /**
     * Get the content from a block's text property
     *
     * @param  array  $block  The Notion block
     */
    protected function getBlockContent(array $block): string
    {
        $blockType = $block['type'];

        return isset($block[$blockType]['rich_text'])
            ? $this->processRichText($block[$blockType]['rich_text'])
            : '';
    }

    /**
     * Process icon blocks from Notion
     *
     * @param  array  $icon  The icon data from Notion
     */
    protected function processIcon(array $icon): string
    {
        return match ($icon['type']) {
            'emoji' => $icon['emoji'],
            'external' => $this->processExternalIcon($icon['external']['url']),
            'file' => sprintf('[🔗](%s)', $icon['file']['url']),
            default => '💡',
        };
    }

    /**
     * Process external icon URL to extract icon name
     *
     * @param  string  $url  The external icon URL
     */
    private function processExternalIcon(string $url): string
    {
        if (preg_match('/\/([^\/]+)_[^\/]+\.svg$/', $url, $matches)) {
            $iconName = ucfirst($matches[1]);

            return sprintf('[%s](%s)', $iconName, $url);
        }

        return '[Icon]('.$url.')';
    }

    /**
     * Set the Notion SDK instance
     */
    public function setSdk(Notion $sdk): self
    {
        $this->sdk = $sdk;

        return $this;
    }

    /**
     * Get the Notion SDK instance
     *
     * @throws \RuntimeException if SDK is not set
     */
    protected function getSdk(): Notion
    {
        if ($this->sdk === null) {
            throw new \RuntimeException('Notion SDK not set. Please set SDK using setSdk() method.');
        }

        return $this->sdk;
    }
}
