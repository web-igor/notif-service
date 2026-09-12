<?php

declare(strict_types=1);

use App\Models\Client;

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Default guard for the application. Since this is an API-only service
    | for machine-to-machine communication, the default guard is "client".
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'client'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | The "client" guard uses Sanctum for API token authentication.
    | Each Client represents an external application that sends
    | notifications through this service.
    |
    */

    'guards' => [
        'client' => [
            'driver'   => 'sanctum',
            'provider' => 'clients',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | The "clients" provider uses the Client model, which represents
    | external applications authenticated via Sanctum API tokens.
    |
    */

    'providers' => [
        'clients' => [
            'driver' => 'eloquent',
            'model'  => Client::class,
        ],
    ],
];
