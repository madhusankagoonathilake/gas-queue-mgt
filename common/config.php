<?php

const CONFIG = [
    'app' => [
        'maxActivationAttempts' => 3,
        'maxLoginAttempts' => 3,
    ],
    'db' => [
        'host' => 'localhost',
        'port' => 3306,
        'database' => 'gqm',
        'username' => 'gqm',
        'password' => 'secret',
    ],
    'sms' => [
        'mask' => 'FastSMS',
        'baseUri' => 'https://fastsms.lk',
        'apiKey' => '1234',
    ],
];
