<?php

include_once '../common/session.php';

function isPostRequest(): bool
{
    $requestMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD');
    return ($requestMethod === 'POST');
}

function generateCsrfToken(string $sessionKey = 'csrfToken'): string
{
    try {
        $csrfToken = bin2hex(random_bytes(20));
    } catch (\Exception $e) {
        $csrfToken = uniqid('_csrfToken');
    }

    setSessionValue($sessionKey, $csrfToken);
    return $csrfToken;
}

function isCsrfTokenValid(string $token, string $sessionKey = 'csrfToken'): bool
{
    try {
        return ($token === getSessionValue($sessionKey));
    } catch (\Exception $e) {
        return false;
    }

}
