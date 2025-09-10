<?php

namespace RedberryProducts\MdNotion\Traits;

use RedberryProducts\MdNotion\DTOs\RichTextDTO;

trait HasTitle
{
    /**
     * The title of the object
     */
    public string $title;

    /**
     * Process and extract title from various title structures
     *
     * @param  mixed  $titleData  The title data from Notion API
     * @return string The processed title
     */
    protected function processTitle(mixed $titleData): string
    {
        if (is_string($titleData)) {
            return $titleData;
        }

        if (is_array($titleData)) {
            // Handle rich text title format (array of rich text objects)
            if (isset($titleData[0]['type'])) {
                $richTextObjects = RichTextDTO::collection($titleData);

                return $this->processRichTextTitle($richTextObjects);
            }

            // Handle nested title structures like child_page.title or child_database.title
            if (isset($titleData['title'])) {
                return $this->processTitle($titleData['title']);
            }
        }

        return '';
    }

    /**
     * Process rich text array to extract plain title
     *
     * @param  RichTextDTO[]  $richText  Array of rich text DTOs
     * @return string The combined plain text title
     */
    protected function processRichTextTitle(array $richText): string
    {
        $result = '';

        foreach ($richText as $textBlock) {
            $result .= $textBlock->plainText;
        }

        return trim($result);
    }

    /**
     * Fill title data from the provided data array
     *
     * @param  array  $data  The data array from Notion API
     */
    protected function fillTitleData(array $data): void
    {
        $title = '';

        // Try different title structures
        if (isset($data['title']) && ! empty($data['title'])) {
            $title = $this->processTitle($data['title']);
        } elseif (isset($data['child_page']['title'])) {
            $title = $this->processTitle($data['child_page']['title']);
        } elseif (isset($data['child_database']['title'])) {
            $title = $this->processTitle($data['child_database']['title']);
        } elseif (isset($data['properties']['title']['title'])) {
            // Handle object context where title is in properties.title.title
            $title = $this->processTitle($data['properties']['title']['title']);
        } elseif (isset($data['properties']['Name']['title'][0]['plain_text'])) {
            // Handle object context where title is in properties.Name.title
            $title = $this->processTitle($data['properties']['Name']['title'][0]['plain_text']);
        }

        // Only update title if we found one in the new data
        if ($title !== '') {
            $this->title = $title;
        } elseif (! isset($this->title)) {
            $this->title = '';
        }
    }

    /**
     * Get title array data for serialization
     */
    protected function getTitleArrayData(): array
    {
        return [
            'title' => $this->title,
        ];
    }

    // Title accessors
    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }
}
