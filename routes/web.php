<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/user', [UserController::class, 'index'])->middleware("auth");

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::match(["GET", "POST"], '/logout',[LoginController::class, 'logout'])->name('logout');
Route::get("sendmail", [UserController::class, "sendmail"]);

Route::middleware('auth')->prefix('booking')->controller(BookingController::class)->group(function(){

    Route::get('/', 'index');
    Route::get('create', 'create');
    Route::post('store', 'store');
    Route::post('check-in/{booking}', 'checkIn');
    Route::post('check-out/{booking}', 'checkOut');
    Route::get('edit', 'edit');
    Route::put('update', 'update');
    Route::get('available', 'availableRooms');
});

Route::middleware('auth')->prefix('payment')->controller(PaymentController::class)->group(function(){

    Route::get('/', 'index')->name('payment.index');
    Route::get('booking/{booking}', 'show')->name('booking.show');
    Route::get('booking/{booking}/create', 'create')->name('payment.create');
    Route::post('booking/{booking}', 'store')->name('payment.store');
    Route::get('{payment}/edit', 'edit')->name('payment.edit');
    Route::put('{payment}', 'update')->name('payment.update');
    Route::delete('{payment}', 'destroy')->name('payment.destroy');
    Route::post('{payment}/refund', 'refund')->name('payment.refund');
});

Route::middleware('auth')->prefix('room')->controller(RoomController::class)->group(function(){

    Route::get('/', 'index');
    Route::get('create', 'create');
    Route::post('store', 'store');
    Route::get('edit', 'edit');
    Route::put('update', 'update');
    Route::get('occupancy', 'occupency');
});

