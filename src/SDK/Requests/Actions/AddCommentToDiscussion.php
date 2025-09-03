<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use DateTime;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * add comment to discussion
 */
class AddCommentToDiscussion extends Request implements HasBody
{
	use HasJsonBody;

	protected Method $method = Method::POST;


	public function resolveEndpoint(): string
	{
		return "/v1/comments";
	}


	/**
	 * @param null|mixed $discussionId
	 * @param null|mixed $richText
	 * @param null|mixed $displayName
	 */
	public function __construct(
		protected mixed $discussionId = null,
		protected mixed $richText = null,
		protected mixed $displayName = null,
	) {
	}


	public function defaultBody(): array
	{
		return array_filter(['discussion_id ' => $this->discussionId, 'rich_text' => $this->richText, 'display_name' => $this->displayName]);
	}
}
