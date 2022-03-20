<?php

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

    $_SESSION[$sessionKey] = $csrfToken;
    return $csrfToken;
}

function isCsrfTokenValid(string $token, string $sessionKey = 'csrfToken'): bool
{
    return ($token === $_SESSION['csrfToken']);
}
