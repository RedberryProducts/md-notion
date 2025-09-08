<?php

namespace RedberryProducts\MdNotion\Objects;

use RedberryProducts\MdNotion\Traits\HasChildPages;
use RedberryProducts\MdNotion\Traits\HasIcon;
use RedberryProducts\MdNotion\Traits\HasMeta;
use RedberryProducts\MdNotion\Traits\HasParent;
use RedberryProducts\MdNotion\Traits\HasTitle;

abstract class BaseObject
{
    use HasChildPages, HasIcon, HasMeta, HasParent, HasTitle;

    // Required fields
    public string $id;

    // Optional fields
    public ?string $url = null;

    public ?string $publicUrl = null;

    public array $properties = [];

    /**
     * Create a new BaseObject instance
     */
    public function __construct(array $data = [])
    {
        if (! empty($data)) {
            $this->fill($data);
        }

        $this->initializeChildCollections();
    }

    /**
     * Create a new instance from data array
     */
    public static function from(array $data): static
    {
        return new static($data);
    }

    /**
     * Fill the object with data
     */
    public function fill(array $data): static
    {
        $this->id = $data['id'] ?? $this->id ?? '';
        $this->url = $data['url'] ?? $this->url;
        $this->publicUrl = $data['public_url'] ?? $this->publicUrl;
        $this->properties = $data['properties'] ?? $this->properties;

        // Fill data from traits
        $this->fillTitleData($data);
        $this->fillParentData($data);
        $this->fillMetaData($data);
        $this->fillChildData($data);
        $this->fillIconData($data);

        return $this;
    }

    // ID accessors
    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): static
    {
        $this->id = $id;

        return $this;
    }

    // URL accessors
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url): static
    {
        $this->url = $url;

        return $this;
    }

    public function hasUrl(): bool
    {
        return ! empty($this->url);
    }

    // Public URL accessors
    public function getPublicUrl(): ?string
    {
        return $this->publicUrl;
    }

    public function setPublicUrl(?string $publicUrl): static
    {
        $this->publicUrl = $publicUrl;

        return $this;
    }

    public function hasPublicUrl(): bool
    {
        return ! empty($this->publicUrl);
    }

    // Properties accessors
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function setProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function hasProperties(): bool
    {
        return ! empty($this->properties);
    }

    public function getProperty(string $key): mixed
    {
        return $this->properties[$key] ?? null;
    }

    public function setProperty(string $key, mixed $value): static
    {
        $this->properties[$key] = $value;

        return $this;
    }

    /**
     * Convert the object to an array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                'id' => $this->id,
                'url' => $this->url,
                'public_url' => $this->publicUrl,
                'properties' => $this->properties,
            ],
            $this->getTitleArrayData(),
            $this->getParentArrayData(),
            $this->getMetaArrayData(),
            $this->getChildArrayData(),
            $this->getIconArrayData()
        );
    }

    /**
     * Render title with icon as markdown heading
     *
     * @param  int  $level  The heading level (1-3)
     * @return string The rendered markdown title
     */
    public function renderTitle(int $level = 1): string
    {
        // Validate level
        if ($level < 1 || $level > 3) {
            throw new \InvalidArgumentException('Title level must be between 1 and 3');
        }

        // Generate heading prefix
        $prefix = str_repeat('#', $level).' ';

        // Combine icon and title
        $iconMarkdown = '';
        if ($this->hasIcon()) {
            $iconMarkdown = $this->processIcon().' ';
        }

        $title = $this->getTitle();

        return $prefix.$iconMarkdown.$title;
    }
}
