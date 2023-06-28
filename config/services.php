<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'aws' => [
      'urlImage' => 'https://v-face.s3.amazonaws.com/',
      'AWS_DEFAULT_REGION' => 'ap-northeast-1',
      'AWS_ACCESS_KEY_ID' => 'AKIA6IVJOOLI5DZ3IXNC',
      'AWS_SECRET_ACCESS_KEY' => '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',
    ]
];
