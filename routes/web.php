<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
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
    // Route::get('occupancy', 'occupency');
});

Route::middleware('auth')->prefix('room')->controller(RoomController::class)->group(function(){

    Route::get('/', 'index');
    Route::get('create', 'create');
    Route::post('store', 'store');
    Route::get('edit', 'edit');
    Route::put('update', 'update');
    Route::get('occupancy', 'occupency');
});
