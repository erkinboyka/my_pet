<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Moderator;
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
 * Переключатели языка и валюты (настройки хранятся в сессии)
 */
Route::get('/lang/{lang}', function (string $lang) {
    if (in_array($lang, ['ru', 'en'], true)) {
        session(['lang' => $lang]);
    }

    return back();
})->name('lang.set');

Route::get('/currency/{code}', function (string $code) {
    if (in_array($code, ['TJS', 'USD'], true)) {
        session(['currency' => $code]);
    }

    return back();
})->name('currency.set');

/*
 * Ставки и личный кабинет участника
 */
Route::post('/auctions/{auction}/bids', [BidController::class, 'store'])
    ->middleware(['auth', 'throttle:30,1'])
    ->name('bids.store');

Route::middleware('auth')->prefix('account')->name('client.')->group(function () {
    Route::get('/my-bids', [BidController::class, 'myBids'])->name('my-bids');
    Route::get('/wins', [BidController::class, 'wins'])->name('wins');
});

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
    Route::post('/auctions/{auction}/approve', [Admin\AuctionController::class, 'approve'])->name('auctions.approve');
    Route::post('/auctions/{auction}/reject', [Admin\AuctionController::class, 'reject'])->name('auctions.reject');
    Route::post('/auctions/{auction}/cancel', [Admin\AuctionController::class, 'cancel'])->name('auctions.cancel');

    Route::get('/moderators', [Admin\ModeratorController::class, 'index'])->name('moderators.index');
});

/*
 * Кабинет модератора
 */
Route::middleware(['auth', 'role:moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/', [Moderator\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/auctions', [Moderator\AuctionController::class, 'index'])->name('auctions.index');
    Route::get('/auctions/create', [Moderator\AuctionController::class, 'create'])->name('auctions.create');
    Route::post('/auctions', [Moderator\AuctionController::class, 'store'])->name('auctions.store');
    Route::get('/auctions/{auction}', [Moderator\AuctionController::class, 'show'])->name('auctions.show');
    Route::get('/auctions/{auction}/edit', [Moderator\AuctionController::class, 'edit'])->name('auctions.edit');
    Route::post('/auctions/{auction}', [Moderator\AuctionController::class, 'update'])->name('auctions.update');
    Route::post('/auctions/{auction}/cancel', [Moderator\AuctionController::class, 'cancel'])->name('auctions.cancel');
});
