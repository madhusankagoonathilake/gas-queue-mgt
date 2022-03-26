<?php

function app_log(string $type, string $message): void
{
    try {
        $timestamp = date('Y-m-d H:i:s');
        $type = strtoupper(substr(str_pad($type, 4, ' '), 0, 4));
        file_put_contents('../logs/app.log', "[${timestamp}] [{$type}] - {$message}\n", FILE_APPEND);
    } catch (\Exception $e) {

    }
}

function notification_log($message): void
{
    try {
        $timestamp = date('Y-m-d H:i:s');
        file_put_contents('../logs/notification.log', "[${timestamp}] [INFO] - {$message}\n", FILE_APPEND);
    } catch (\Exception $e) {

    }
}
