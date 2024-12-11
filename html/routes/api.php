<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\BalanceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/cashIn', [BalanceController::class, 'cashIn']);

Route::post('/cashOut', [BalanceController::class, 'cashOut']);

Route::post('/transfer', [BalanceController::class, 'transfer']);

Route::get('/getBalance', [BalanceController::class, 'getBalance']);

Route::get('/currencyConverter', [BalanceController::class, 'currencyConverter']);

Route::get('/listTransactions', [BalanceController::class, 'listTransactions']);
