<?php

namespace Redberry\MdNotion\SDK\Requests\Actions;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * Query a data source
 */
class QueryDataSource extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return "/v1/data_sources/{$this->dataSourceId}/query";
    }

    public function __construct(
        protected string $dataSourceId,
        protected ?array $filter = null,
        protected ?int $pageSize = null,
        protected ?string $startCursor = null,
    ) {}

    protected function defaultBody(): array
    {
        return array_filter([
            'filter' => $this->filter,
            'page_size' => $this->pageSize,
            'start_cursor' => $this->startCursor,
        ], fn ($value) => $value !== null);
    }
}
