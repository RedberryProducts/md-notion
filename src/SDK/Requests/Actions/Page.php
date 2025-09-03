<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use DateTime;
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


	/**
	 * @param string $id
	 */
	public function __construct(
		protected string $id,
	) {
	}
}
