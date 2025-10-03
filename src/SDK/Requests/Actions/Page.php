<?php

namespace Redberry\MdNotion\SDK\Requests\Actions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Page
 */
class Page extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/v1/pages/{$this->id}";
    }

    public function __construct(
        protected string $id,
    ) {}
}
