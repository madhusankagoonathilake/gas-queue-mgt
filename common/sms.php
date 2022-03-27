<?php

require_once '../common/config.php';
require_once '../vendor/autoload.php';

use GuzzleHttp\Client;

function sendSMS($to, $text): bool
{
    $data = [
        'phone_number' => preg_replace('/^0/', '94', $to),
        'name' => 'Customer',
        'message' => $text,
        'mask' => CONFIG['sms']['mask'],
    ];
    if(CONFIG['sms']['prod']){
        $client = new Client(['base_uri' => CONFIG['sms']['baseUri'], 'timeout' => 2.0]);
        $response = $client->request('POST', '/api/v1/send', [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'bearer' => CONFIG['sms']['apiKey'],
            ],
            'json' => $data,
        ]);

        $result = json_decode($response->getBody());

        if ($result->error) {
            throw new \Exception($result->message, $result->code);
        }
    } else {
        mockTestSMS($data);
        return true;
    }


    return true;
}

function mockTestSMS($get_input): void
{

    $fp = fopen('api/v1/display'.$get_input['phone_number'].'.txt', 'w+');//opens file in append mode
    fwrite($fp, "===========================================================\n");
    fwrite($fp, $get_input['phone_number'] . '('.$get_input['name'].')' );
    fwrite($fp, "\n=========================**================================\n");
    fwrite($fp, $get_input['message'].')' );
    fwrite($fp, "\n=========================**================================");
    fclose($fp);
}