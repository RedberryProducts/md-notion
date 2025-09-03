<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * database
 */
class Database extends Request
{
    protected Method $method = Method::GET;

    public function resolveEndpoint(): string
    {
        return "/v1/databases/{$this->databaseId}";
    }

    public function __construct(
        protected string $databaseId,
    ) {}
}
