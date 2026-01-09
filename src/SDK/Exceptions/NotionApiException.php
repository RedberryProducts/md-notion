<?php

namespace Redberry\MdNotion\SDK\Exceptions;

use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Response;
use Throwable;

/**
 * Exception thrown when the Notion API returns an error response.
 *
 * Error responses from the Notion API contain:
 * - HTTP status code (400, 401, 403, 404, 409, 429, 500, 502, 503, 504)
 * - `code` property with the error type (e.g., "validation_error", "unauthorized")
 * - `message` property with a human-readable description
 *
 * @see https://developers.notion.com/reference/status-codes
 */
class NotionApiException extends RequestException
{
    /**
     * The Notion API error code (e.g., "validation_error", "unauthorized", "object_not_found")
     */
    protected ?string $notionCode = null;

    /**
     * The Notion API error message
     */
    protected ?string $notionMessage = null;

    public function __construct(
        Response $response,
        ?Throwable $previous = null
    ) {
        $body = $response->json();

        $this->notionCode = $body['code'] ?? null;
        $this->notionMessage = $body['message'] ?? null;

        $message = $this->buildMessage($response);

        parent::__construct($response, $message, 0, $previous);
    }

    /**
     * Build a descriptive error message
     */
    protected function buildMessage(Response $response): string
    {
        $status = $response->status();

        if ($this->notionCode && $this->notionMessage) {
            return "Notion API Error [{$status}] {$this->notionCode}: {$this->notionMessage}";
        }

        if ($this->notionCode) {
            return "Notion API Error [{$status}] {$this->notionCode}";
        }

        return "Notion API Error [{$status}]: Unknown error";
    }

    /**
     * Get the Notion error code
     *
     * Possible values:
     * - invalid_json
     * - invalid_request_url
     * - invalid_request
     * - invalid_grant
     * - validation_error
     * - missing_version
     * - unauthorized
     * - restricted_resource
     * - object_not_found
     * - conflict_error
     * - rate_limited
     * - internal_server_error
     * - bad_gateway
     * - service_unavailable
     * - database_connection_unavailable
     * - gateway_timeout
     */
    public function getNotionCode(): ?string
    {
        return $this->notionCode;
    }

    /**
     * Get the Notion error message
     */
    public function getNotionMessage(): ?string
    {
        return $this->notionMessage;
    }

    /**
     * Check if the error is a rate limit error
     */
    public function isRateLimited(): bool
    {
        return $this->notionCode === 'rate_limited';
    }

    /**
     * Check if the error is an authorization error
     */
    public function isUnauthorized(): bool
    {
        return $this->notionCode === 'unauthorized';
    }

    /**
     * Check if the error is a permission error
     */
    public function isForbidden(): bool
    {
        return $this->notionCode === 'restricted_resource';
    }

    /**
     * Check if the error is a not found error
     */
    public function isNotFound(): bool
    {
        return $this->notionCode === 'object_not_found';
    }

    /**
     * Check if the error is a validation error
     */
    public function isValidationError(): bool
    {
        return $this->notionCode === 'validation_error';
    }

    /**
     * Check if the error is a server error (5xx)
     */
    public function isServerError(): bool
    {
        return in_array($this->notionCode, [
            'internal_server_error',
            'bad_gateway',
            'service_unavailable',
            'database_connection_unavailable',
            'gateway_timeout',
        ], true);
    }

    /**
     * Check if the error is retryable (rate limits, server errors)
     */
    public function isRetryable(): bool
    {
        return $this->isRateLimited() || $this->isServerError() || $this->notionCode === 'conflict_error';
    }
}
