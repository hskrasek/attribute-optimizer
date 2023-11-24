<?php

declare(strict_types=1);

return [
    'auth' => [
        'client_id' => env('CLIENT_ID'),
        'secret_key' => env('SECRET_KEY'),
        'callback' => env('CALLBACK_URL'),
        'scopes' => env('SCOPES'),
    ],
];
