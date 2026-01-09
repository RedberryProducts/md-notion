<?php

namespace Redberry\MdNotion\SDK;

use Redberry\MdNotion\SDK\Exceptions\NotionApiException;
use Redberry\MdNotion\SDK\Resource\Actions;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;
use Saloon\Http\Response;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;
use Throwable;

/**
 * Notion
 */
class Notion extends Connector
{
    use AlwaysThrowOnErrors;

    public function __construct(
        public readonly string $token,
        public readonly string $version
    ) {}

    protected function defaultAuth(): TokenAuthenticator
    {
        return new TokenAuthenticator($this->token);
    }

    public function resolveBaseUrl(): string
    {
        return 'https://api.notion.com/';
    }

    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Notion-Version' => $this->version,
        ];
    }

    public function act(): Actions
    {
        return new Actions($this);
    }

    /**
     * Get the custom exception for failed requests
     *
     * This returns a NotionApiException which includes the Notion-specific
     * error code and message from the response body.
     */
    public function getRequestException(Response $response, ?Throwable $senderException): ?Throwable
    {
        return new NotionApiException($response, $senderException);
    }
}
