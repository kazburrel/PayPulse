<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\TransactionHistoryController;
use App\Http\Controllers\TransferController;
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
    Route::group(['prefix' => 'transfer'], function () {
        Route::get('/get_user_name/{accountNum}', [TransferController::class, 'getUserName'])->name('user.store.transfer');
        Route::post('/confirm/{tranId}', [TransferController::class, 'sameBankTransferConfirm'])->name('user.same.bank.transfer.confirm');
        Route::group(['prefix' => 'internal'], function () {
            Route::post('/same_bank', [TransferController::class, 'store'])->name('user.store.transfer');
            Route::get('/same_bank/{tranId}', [TransferController::class, 'getTranDetails'])->name('user.get.transfer.details');
        });
        Route::group(['prefix' => 'external'], function () {
            Route::post('/bank', [TransferController::class, 'storeExternalTransfer'])->name('user.store.external.transfer');
            Route::get('/bank/{tranId}', [TransferController::class, 'getExtTranDetails'])->name('user.get.external.transfer.details');
        });
        Route::group(['prefix' => 'international'], function () {
            Route::post('/bank', [TransferController::class, 'storeInternationalTransfer'])->name('user.store.international.transfer');
            Route::get('/bank/{tranId}', [TransferController::class, 'getInternationalTranDetails'])->name('user.get.international.transfer.details');
            Route::post('/confirm/{tranId}', [TransferController::class, 'internationalTransferConfirm'])->name('user.international.bank.transfer.confirm');
            Route::post('/otp/confirm/{tranId}', [TransferController::class, 'internationalTransferOTPConfirm'])->name('user.international.bank.otp.transfer.confirm');
        });
    });
    Route::group(['prefix' => 'history'], function () {
        Route::get('/{userId}', [TransactionHistoryController::class, 'getTransactionHistory'])->name('user.get.transfer.history');

    });
});

require __DIR__ . '/auth.php';
