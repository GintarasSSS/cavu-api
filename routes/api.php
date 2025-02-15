<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\BookingController;

Route::controller(AuthenticationController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout')->middleware('auth:sanctum');
});

Route::prefix('bookings')->group(function () {
    Route::get('/', [BookingController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [BookingController::class, 'store']);
        Route::delete('/', [BookingController::class, 'destroy']);
        Route::put('/', [BookingController::class, 'update']);
        Route::get('/details', [BookingController::class, 'show']);
    });
});
