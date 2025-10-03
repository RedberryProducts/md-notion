<?php

namespace Redberry\MdNotion\SDK\Requests\Actions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * block children
 */
class BlockChildren extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/v1/blocks/{$this->id}/children";
    }

    public function __construct(
        protected string $id,
        protected ?string $pageSize = null,
    ) {}

    public function defaultQuery(): array
    {
        return array_filter(['page_size' => $this->pageSize]);
    }
}
