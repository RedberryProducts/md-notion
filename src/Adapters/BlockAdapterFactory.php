<?php

namespace RedberryProducts\MdNotion\Adapters;

use RedberryProducts\MdNotion\SDK\Notion;
use Illuminate\Support\Arr;

class BlockAdapterFactory
{
    private Notion $sdk;
    private array $adapters = [];
    private array $adapterMap;

    public function __construct(Notion $sdk, array $adapterMap)
    {
        $this->sdk = $sdk;
        $this->adapterMap = $adapterMap;
    }

    public function create(string $blockType): BlockAdapterInterface
    {
        if (!isset($this->adapters[$blockType])) {
            $adapterClass = Arr::get($this->adapterMap, $blockType);

            if (!$adapterClass || !class_exists($adapterClass)) {
                throw new \InvalidArgumentException(
                    "No adapter configured for block type: {$blockType}. " .
                    "Please check your md-notion.php config file."
                );
            }

            /** @var BlockAdapterInterface $adapter */
            $adapter = is_callable($adapterClass) ? $adapterClass() : new $adapterClass();
            $adapter->setSdk($this->sdk);
            $this->adapters[$blockType] = $adapter;
        }

        return $this->adapters[$blockType];
    }

    /**
     * Get all registered block types
     *
     * @return array<string>
     */
    public function getRegisteredBlockTypes(): array
    {
        return array_keys($this->adapterMap);
    }
}
