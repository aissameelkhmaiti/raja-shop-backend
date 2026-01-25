<?php

return [
    // ⬇️ AJOUTE 'broadcasting/auth' ICI
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'broadcasting/auth'], 
    'supports_credentials' => true, // Indispensable pour Sanctum

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000', 
        'http://localhost:5173',
        'http://127.0.0.1:5173'
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,
];