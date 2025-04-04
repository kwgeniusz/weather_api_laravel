<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\FavoriteController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\WeatherController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (V1)
|--------------------------------------------------------------------------
*/

// Auth routes
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('login', [AuthController::class, 'login'])->name('login');

// Weather routes (public)
Route::get('weather/current', [WeatherController::class, 'current'])->name('weather.current');
Route::get('weather/forecast', [WeatherController::class, 'forecast'])->name('weather.forecast');
Route::get('weather/search', [WeatherController::class, 'search'])->name('weather.search');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    
    // User profile
    Route::get('profile', [UserController::class, 'profile'])->name('profile');
    Route::put('profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('profile/password', [UserController::class, 'changePassword'])->name('profile.password');
    
    // Weather history
    Route::get('weather/history', [WeatherController::class, 'history'])->name('weather.history');
    
    // Favorites
    Route::get('favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('favorites', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('favorites/{id}', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::put('favorites/{id}/default', [FavoriteController::class, 'setDefault'])->name('favorites.default');
    Route::get('favorites/default', [FavoriteController::class, 'getDefault'])->name('favorites.default.get');
});
