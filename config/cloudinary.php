<?php

/*
|--------------------------------------------------------------------------
| Cloudinary Configuration
|--------------------------------------------------------------------------
|
| Configuration officielle et minimale pour le package
| cloudinary-labs/cloudinary-laravel
|
| ⚠️ Ne PAS ajouter cloud_url, upload_preset, upload_route, etc.
| ⚠️ Ne PAS utiliser CLOUDINARY_URL ici
|
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Credentials
    |--------------------------------------------------------------------------
    */

    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
    'api_key'    => env('CLOUDINARY_API_KEY'),
    'api_secret' => env('CLOUDINARY_API_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Options
    |--------------------------------------------------------------------------
    */

    'secure' => true,

];
