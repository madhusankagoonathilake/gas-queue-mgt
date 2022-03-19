<?php

require_once '../common/config.php';
require_once '../vendor/autoload.php';

use GuzzleHttp\Client;

function send_sms($to, $text): bool
{
    $client = new Client(['base_uri' => CONFIG['sms']['baseUri'], 'timeout' => 2.0]);
    $response = $client->request('POST', '/api/v1/send', [
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'bearer' => CONFIG['sms']['apiKey'],
        ],
        'json' => [
            'phone_number' => preg_replace('/^0/', '94', $to),
            'name' => 'Customer',
            'message' => $text,
            'mask' => CONFIG['sms']['mask'],
        ],
    ]);

    $result = json_decode($response->getBody());

    if ($result->error) {
        throw new \Exception(print_r($result));
    }

    return true;
}
