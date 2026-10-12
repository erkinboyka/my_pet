<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/auctions', [AuctionController::class, 'index'])->name('auctions.index');
Route::get('/auctions/{auction}', [AuctionController::class, 'show'])->name('auctions.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

/*
 * Панель администратора
 */
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [Admin\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/block', [Admin\UserController::class, 'toggleBlock'])->name('users.block');
    Route::post('/users/{user}/role', [Admin\UserController::class, 'changeRole'])->name('users.role');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/auctions', [Admin\AuctionController::class, 'index'])->name('auctions.index');
    Route::get('/auctions/create', [Admin\AuctionController::class, 'create'])->name('auctions.create');
    Route::post('/auctions', [Admin\AuctionController::class, 'store'])->name('auctions.store');
    Route::get('/auctions/{auction}/edit', [Admin\AuctionController::class, 'edit'])->name('auctions.edit');
    Route::post('/auctions/{auction}', [Admin\AuctionController::class, 'update'])->name('auctions.update');
    Route::post('/auctions/{auction}/cancel', [Admin\AuctionController::class, 'cancel'])->name('auctions.cancel');

    Route::get('/moderators', [Admin\ModeratorController::class, 'index'])->name('moderators.index');
});
