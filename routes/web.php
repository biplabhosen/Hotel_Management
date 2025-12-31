<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/user', [UserController::class, 'index'])->middleware("auth");

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::match(["GET", "POST"], '/logout',[LoginController::class, 'logout'])->name('logout');
Route::get("sendmail", [UserController::class, "sendmail"]);
