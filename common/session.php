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

function getLanguage(): string {
    if (isset($_COOKIE['language']) && strlen($_COOKIE['language']) == 2) {
        $language = $_COOKIE['language'];
        if ($language == 'si') {
            return 'si';
        } else if ($language == 'en') {
            return 'en';
        } else if ($language == 'ta') {
            return 'ta';
        } else {
            return 'si';
        }
    } else {
        return 'si';
    }
}

function setLanguage($lang) {
    if ($lang == 'si') {
        setSessionValues(array('language', 'si'));
        setcookie('language', 'si', time() + (86400 * 30), "/");
    } else if ($lang == 'en') {
        setSessionValues(array('language', 'en'));
        setcookie('language', 'en', time() + (86400 * 30), "/");
    } else if ($lang == 'ta') {
        setSessionValues(array('language', 'ta'));
        setcookie('language', 'ta', time() + (86400 * 30), "/");
    } else {
        setSessionValues(array('laguage' => 'si'));
        setcookie('language', 'si', time() + (86400 * 30), "/");
    }
}