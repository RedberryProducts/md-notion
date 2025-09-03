<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * database items
 */
class DatabaseItems extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/v1/databases/{$this->databaseId}/query";
    }

    public function __construct(
        protected string $databaseId,
    ) {}
}
