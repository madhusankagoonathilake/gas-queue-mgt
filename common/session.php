<?php

function setSessionValue($key, $value): void
{
    $_SESSION[$key] = $value;
}

function getSessionValue($key)
{
    if (array_key_exists($key, $_SESSION)) {
        return $_SESSION[$key];
    } else {
        throw new \Exception("Undefined key {$key} in session.");
    }
}

function incrementSessionValue($key, $step = 1): void
{
    if (array_key_exists($key, $_SESSION)) {
        if (is_numeric($_SESSION[$key])) {
            $_SESSION[$key] += $step;
        } else {
            throw new \Exception("Value of {$key} in session is not numeric.");
        }
    } else {
        throw new \Exception("Undefined key {$key} in session.");
    }
}

function setSessionValues(array $keyValuePair): void
{
    foreach ($keyValuePair as $key => $value) {
        $_SESSION[$key] = $value;
    }
}

function isLoggedIn(): bool
{
    try {
        $agencyId = getSessionValue('agency-id');
        return !empty($agencyId);
    } catch (\Exception $e) {
        return false;
    }
}
