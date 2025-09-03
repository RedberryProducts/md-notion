<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * List comments
 */
class ListComments extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return '/v1/comments';
    }

    public function __construct(
        protected ?string $blockId = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['block_id' => $this->blockId]);
    }
}
