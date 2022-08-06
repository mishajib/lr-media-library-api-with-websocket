<?php

use App\Http\Controllers\API\Auth\AuthenticationController;
use App\Http\Controllers\API\ImagesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Login & Register Routes
Route::post('register', [AuthenticationController::class, 'register']);
Route::post('login', [AuthenticationController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    // Get current/authenticated user route
    Route::get('current-user', [AuthenticationController::class, 'currentUser']);

    // Refresh token route
    Route::post('refresh-token', [AuthenticationController::class, 'refreshToken']);

    // Logout route
    Route::post('logout', [AuthenticationController::class, 'logout']);

    // Image routes
    Route::resource('images', ImagesController::class)->except(['show', 'edit', 'update']);

});
