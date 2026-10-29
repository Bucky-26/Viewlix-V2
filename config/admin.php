<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Admin Panel Secret Gate
    |--------------------------------------------------------------------------
    |
    | When an administrator clicks the Admin Panel button, a dynamic, single-use,
    | time-limited token is generated on demand and passed in the query string
    | (e.g. /admin?token=40_random_chars).
    |
    | The token is consumed immediately upon first use and expires after
    | token_lifetime seconds. Any request without a valid token or unlocked
    | session returns 404 to conceal the panel from penetration testers.
    |
    */
    'secret_param' => env('ADMIN_SECRET_PARAM', 'token'),
    'secret_token' => env('ADMIN_SECRET_TOKEN', null), // Optional fallback static token
    'token_lifetime' => (int) env('ADMIN_TOKEN_LIFETIME', 60), // Lifetime in seconds

];
