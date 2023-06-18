<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\UserController;
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

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => ['auth:sanctum', 'verified']], function () {
    Route::group(['prefix' => 'pin'], function () {
        Route::post('/create_pin', [UserController::class, 'storePin'])->name('user.create.pin');
        Route::post('/update_pin/{pinId}', [UserController::class, 'updatePin'])->name('user.update.pin');
    });
});

require __DIR__ . '/auth.php';
