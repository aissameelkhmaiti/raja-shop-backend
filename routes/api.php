<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\OrderController;

// -----------------------------
// Broadcast routes (doit être avant toutes les autres)
// -----------------------------
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// -----------------------------
// Public routes
// -----------------------------
Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-ccc', function () {
    try {
        $cloudinary = config('cloudinary');
        return response()->json([
            'status' => 'Configuration OK',
            'cloud_name' => $cloudinary['cloud_name'],
            'api_key_set' => !empty($cloudinary['api_key']),
            'api_secret_set' => !empty($cloudinary['api_secret']),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'Error',
            'message' => $e->getMessage(),
        ], 500);
    }
});


//visoritor 
Route::post('/visit', [VisitController::class, 'store']);
Route::get('/stats/visits', [VisitController::class, 'stats']);
// Auth
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);

// Products & Categories (public)
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);
Route::get('/products/category/{id}', [ProductController::class, 'getByCategory']);
Route::get('/categories', [CategoryController::class, 'index']);

// -----------------------------
// Authenticated routes (all users)
// -----------------------------
Route::middleware('auth:sanctum')->group(function () {

    // User profile
    Route::get('/user', [AuthController::class, 'fetchUser']);
    Route::post('/users', [AuthController::class, 'updateUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Test real-time notification
    Route::post('/test-notification', function (\Illuminate\Http\Request $request) {
        \App\Events\RealTimeNotification::dispatch(
            'Notification de test à ' . now()->format('H:i:s'),
            $request->user()->id
        );
        return response()->json(['message' => 'Notification envoyée']);
    });

    // Notifications
    Route::prefix('notifications')->group(function () {
        Route::get('/', [NotificationController::class, 'index']);
        Route::get('/unread', [NotificationController::class, 'unread']);
        Route::post('/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
        Route::delete('/{id}', [NotificationController::class, 'destroy']);
    });

    // -----------------------------
    // Admin routes
    // -----------------------------
    Route::middleware('role:admin')->group(function () {

        // Products
        Route::post('/products', [ProductController::class, 'store']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);

        // Categories
        Route::post('/categories', [CategoryController::class, 'store']);
        Route::put('/categories/{id}', [CategoryController::class, 'update']);
        Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    });

    // -----------------------------
    // Customer routes
    // -----------------------------
    Route::middleware('role:customer')->group(function () {

        // Orders
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{id}', [OrderController::class, 'show']);
        Route::post('/orders', [OrderController::class, 'store']);
        Route::put('/orders/{id}', [OrderController::class, 'update']);
        Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    });
});
