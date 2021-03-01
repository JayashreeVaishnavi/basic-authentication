<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [\App\Http\Controllers\API\V1\AuthController::class, 'login']);
Route::post('register', [\App\Http\Controllers\API\V1\AuthController::class, 'register']);
Route::middleware('auth.jwt')->group(function () {
    Route::post('logout', [\App\Http\Controllers\API\V1\AuthController::class, 'logout']);
    Route::post('refresh', [\App\Http\Controllers\API\V1\AuthController::class, 'refresh']);
    Route::post('me', [\App\Http\Controllers\API\V1\AuthController::class, 'me']);
});
