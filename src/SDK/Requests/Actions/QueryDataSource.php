<?php

namespace Redberry\MdNotion\SDK\Requests\Actions;

use Saloon\Enums\Method;
use Saloon\Http\Request;

/**
 * Query a data source
 */
class QueryDataSource extends Request
{
    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/v1/data_sources/{$this->dataSourceId}/query";
    }

    public function __construct(
        protected string $dataSourceId,
        protected ?array $filter = null,
    ) {}

    // Potentially it can have a filter object in the body
    // @todo create separate request with filter, use this to fetch all items
}
