<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BalanceController;
use App\Http\Controllers\ExtractController;
use App\Http\Controllers\OperationController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/login', [UserController::class, 'index'])->name('login');
Route::post('/login', [UserController::class, 'login'])->name('loginUser');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [UserController::class, 'home'])->name('home');
    Route::get('/logout', [UserController::class, 'logout'])->name('logout');
    Route::post('/register', [UserController::class, 'register'])->name('registerUser');

    Route::get('/extrato', [ExtractController::class, 'index'])->name('extract');
    Route::get('/fetch-refunds', [BalanceController::class, 'getAllReversibleTransactions'])->name('fetchRefunds');
    Route::get('/balance/update', [BalanceController::class, 'getBalance'])->name('balanceUpdate');


    Route::get('/operar', [OperationController::class, 'index'])->name('operate');
    Route::post('/transfer', [OperationController::class, 'transfer'])->name('executeTransfer');
    Route::post('/refund/{originOperationId}', [OperationController::class, 'refund'])->name('refund');
    Route::post('/deposit', [OperationController::class, 'deposit'])->name('deposit');
});
