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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | TMDB API Configuration
    |--------------------------------------------------------------------------
    */
    'tmdb' => [
        'api_key' => env('APP_TMDB_API_KEY'),
        'access_token' => env('APP_TMDB_API_READ_ACCESS_TOKEN'),
        'base_url' => 'https://api.themoviedb.org/3',
        'image_base_url' => 'https://image.tmdb.org/t/p',
    ],

    /*
    |--------------------------------------------------------------------------
    | VidLink Streaming Provider
    |--------------------------------------------------------------------------
    */
    'vidlink' => [
        'base_url' => 'https://vidlink.pro',
    ],

    /*
    |--------------------------------------------------------------------------
    | Google OAuth2 (for Gmail SMTP)
    |--------------------------------------------------------------------------
    */
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect_uri' => env('GOOGLE_REDIRECT_URI'),
        'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
        'email' => env('MAIL_USERNAME', 'viewlix.site@gmail.com'),
    ],

];
