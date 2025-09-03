<?php

namespace RedberryProducts\MdNotion\SDK\Requests\Actions;

use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Traits\Body\HasJsonBody;

/**
 * add comment to page
 */
class AddCommentToPage extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function resolveEndpoint(): string
    {
        return '/v1/comments';
    }

    public function __construct(
        protected mixed $parent = null,
        protected mixed $richText = null,
        protected mixed $displayName = null,
    ) {}

    public function defaultBody(): array
    {
        return array_filter(['parent' => $this->parent, 'rich_text' => $this->richText, 'display_name' => $this->displayName]);
    }
}
