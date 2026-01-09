<?php

/**
 * Manual Test: Notion API Error Handling
 *
 * This script demonstrates how the SDK now throws NotionApiException
 * when the Notion API returns an error response.
 */

require_once __DIR__.'/../vendor/autoload.php';

use Redberry\MdNotion\SDK\Exceptions\NotionApiException;
use Redberry\MdNotion\SDK\Notion;

echo "=== Manual Test: Notion API Error Handling ===\n\n";

// Test 1: Invalid Token
echo "Test 1: Invalid Token (401 Unauthorized)\n";
echo str_repeat('-', 50)."\n";

try {
    $notion = new Notion('invalid-token', '2025-09-03');
    $notion->act()->getPage('some-page-id');
    echo "❌ Expected exception was not thrown\n";
} catch (NotionApiException $e) {
    echo "✅ NotionApiException caught!\n";
    echo '   Status:  '.$e->getResponse()->status()."\n";
    echo '   Code:    '.$e->getNotionCode()."\n";
    echo '   Message: '.$e->getNotionMessage()."\n";
    echo '   isUnauthorized(): '.($e->isUnauthorized() ? 'true' : 'false')."\n";
    echo '   isRetryable(): '.($e->isRetryable() ? 'true' : 'false')."\n";
} catch (Exception $e) {
    echo '❌ Unexpected exception: '.get_class($e)."\n";
    echo '   Message: '.$e->getMessage()."\n";
}

echo "\n";

// Test 2: Object Not Found (valid token, non-existent page)
echo "Test 2: Object Not Found (404)\n";
echo str_repeat('-', 50)."\n";

// Load real token if available
$tokenFile = __DIR__.'/../notion-token.php';
if (file_exists($tokenFile)) {
    $token = include $tokenFile;

    try {
        $notion = new Notion($token, '2025-09-03');
        // Try to fetch a non-existent page
        $notion->act()->getPage('00000000-0000-0000-0000-000000000000');
        echo "❌ Expected exception was not thrown\n";
    } catch (NotionApiException $e) {
        echo "✅ NotionApiException caught!\n";
        echo '   Status:  '.$e->getResponse()->status()."\n";
        echo '   Code:    '.$e->getNotionCode()."\n";
        echo '   Message: '.$e->getNotionMessage()."\n";
        echo '   isNotFound(): '.($e->isNotFound() ? 'true' : 'false')."\n";
        echo '   isRetryable(): '.($e->isRetryable() ? 'true' : 'false')."\n";
    } catch (Exception $e) {
        echo '❌ Unexpected exception: '.get_class($e)."\n";
        echo '   Message: '.$e->getMessage()."\n";
    }
} else {
    echo "⏭️  Skipped (no notion-token.php found)\n";
}

echo "\n";

// Test 3: Successful Request (for comparison)
echo "Test 3: Successful Request\n";
echo str_repeat('-', 50)."\n";

if (file_exists($tokenFile)) {
    $token = include $tokenFile;

    try {
        $notion = new Notion($token, '2025-09-03');
        // Use a known valid page ID from your workspace
        $response = $notion->act()->getPage('24cd937adaa8811c8dd5c2a5ed7eb453');
        echo "✅ Request successful!\n";
        echo '   Status: '.$response->status()."\n";
        echo '   Page ID: '.$response->json()['id']."\n";
    } catch (NotionApiException $e) {
        echo '❌ NotionApiException: '.$e->getMessage()."\n";
        echo "   You may need to update the page ID to one accessible by your integration.\n";
    } catch (Exception $e) {
        echo '❌ Unexpected exception: '.get_class($e)."\n";
        echo '   Message: '.$e->getMessage()."\n";
    }
} else {
    echo "⏭️  Skipped (no notion-token.php found)\n";
}

echo "\n=== Error Handling Summary ===\n\n";
echo "The SDK now throws NotionApiException for all API errors.\n";
echo "Available helper methods:\n";
echo "  - getNotionCode()     - Get the error code (e.g., 'unauthorized', 'object_not_found')\n";
echo "  - getNotionMessage()  - Get the human-readable error message\n";
echo "  - isUnauthorized()    - Check if 401 unauthorized\n";
echo "  - isForbidden()       - Check if 403 restricted_resource\n";
echo "  - isNotFound()        - Check if 404 object_not_found\n";
echo "  - isRateLimited()     - Check if 429 rate_limited\n";
echo "  - isValidationError() - Check if 400 validation_error\n";
echo "  - isServerError()     - Check if 5xx server error\n";
echo "  - isRetryable()       - Check if error is retryable (rate limits, server errors, conflicts)\n";
echo "  - getResponse()       - Access the full Saloon Response object\n";
