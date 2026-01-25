<?php

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;
use App\Services\CloudinaryService;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

Route::get('/cloudinary', function() {
    try {
        $url = Cloudinary::upload(public_path('test.jpg'))->getSecurePath();
        return $url;
    } catch (\Exception $e) {
        return $e->getMessage();
    }
});
Route::get('/test-cloudinary', function () {
    return [
        'cloud_name' => config('cloudinary.cloud_name'),
        'api_key'    => config('cloudinary.api_key'),
        'api_secret' => config('cloudinary.api_secret'),
     
    ];
});

