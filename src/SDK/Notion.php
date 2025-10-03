<?php

namespace Redberry\MdNotion\SDK;

use Redberry\MdNotion\SDK\Resource\Actions;
use Saloon\Http\Auth\TokenAuthenticator;
use Saloon\Http\Connector;

/**
 * Notion
 */
class Notion extends Connector
{
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
}
