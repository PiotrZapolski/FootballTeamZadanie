<?php

use App\Http\Controllers\Api\Authorization\LoginController;
use App\Http\Controllers\CardController;
use App\Http\Controllers\DuelController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout']);

Route::group(['middleware' => ['auth:sanctum']], function () {

    Route::group(['prefix' => 'duels'], function () {
        Route::post('/', [DuelController::class, 'startDuel']);
        Route::get('/active', [DuelController::class, 'getCurrentDuelData']);
        Route::post('/action', [DuelController::class, 'selectCard']);
        Route::get('/', [DuelController::class, 'getDuelsHistory']);
    });

    Route::post('cards', [CardController::class, 'getCard']);

    Route::get('user-data', [UserController::class, 'getUserData']);
});
