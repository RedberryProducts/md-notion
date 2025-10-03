<?php

namespace Redberry\MdNotion\Traits;

trait HasIcon
{
    // Optional icon field
    public ?array $icon = null;

    /**
     * Fill icon data from array
     */
    protected function fillIconData(array $data): void
    {
        $this->icon = $data['icon'] ?? $this->icon;
    }

    // Icon accessors
    public function getIcon(): ?array
    {
        return $this->icon;
    }

    public function setIcon(?array $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    public function hasIcon(): bool
    {
        return ! empty($this->icon);
    }

    /**
     * Process icon blocks from Notion and return markdown representation
     *
     * @param  array|null  $icon  The icon data from Notion
     * @return string The markdown representation of the icon
     */
    public function processIcon(?array $icon = null): string
    {
        $iconData = $icon ?? $this->icon;

        if (empty($iconData)) {
            return '';
        }

        return match ($iconData['type']) {
            'emoji' => $iconData['emoji'],
            'external' => $this->processExternalIcon($iconData['external']['url']),
            'file' => sprintf('[🔗](%s)', $iconData['file']['url']),
            default => '💡',
        };
    }

    /**
     * Process external icon URL to extract icon name
     *
     * @param  string  $url  The external icon URL
     * @return string The markdown representation of the external icon
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
     * Get icon data for array conversion
     */
    protected function getIconArrayData(): array
    {
        return [
            'icon' => $this->icon,
        ];
    }
}
