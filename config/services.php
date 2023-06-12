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
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Adobe PDF Services API
    |--------------------------------------------------------------------------
    |
    | The following credentials are required to communicate with the Adobe PDF
    | Services API. The API provides a range of features, the great ability
    | to convert PDF files to the common formats such as Microsoft Word.
    |
    */

    'adobe' => [

        'client_id' => env('ADOBE_CLIENT_ID'),
        'client_secret' => env('ADOBE_CLIENT_SECRET'),

        'private_key' => env('ADOBE_PRIVATE_KEY'),

    ],

];
