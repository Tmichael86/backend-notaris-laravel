<?php

use App\Http\Controllers\Administrator\AuthController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\DebuggingController;
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

Route::get('/debug', [DebuggingController::class, 'index']);
Route::post('/auth', [AuthController::class, 'authentication']);

Route::middleware('jwt_auth')->group(function () {
    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'save']);
});
