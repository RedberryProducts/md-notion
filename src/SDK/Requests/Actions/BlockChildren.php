<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use DateTime;
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


	/**
	 * @param string $id
	 * @param null|string $pageSize
	 */
	public function __construct(
		protected string $id,
		protected ?string $pageSize = null,
	) {
	}


	public function defaultQuery(): array
	{
		return array_filter(['page_size' => $this->pageSize]);
	}
}
