<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BorrowerController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [AuthController::class, 'login'])->name('login');
Route::get('/register', [AuthController::class, 'reg'])->name('reg');
// Route::post('/register', [AuthController::class, 'register'])->name('register');
// Route::post('/login', [AuthController::class, 'login'])->name('login');
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Tambahkan route untuk dashboard borrower
Route::get('/borrower/dashboard', [BorrowerController::class, 'index'])
    ->name('borrower.dashboard')
    ->middleware('auth');
