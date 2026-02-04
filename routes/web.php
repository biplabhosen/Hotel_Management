<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HotelController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::match(["GET", "POST"], '/logout', [LoginController::class, 'logout'])->name('logout');
Route::get("sendmail", [UserController::class, "sendmail"]);


Route::middleware(['auth'])->group(function () {
    Route::patch('users/{user}/archive', [UserController::class, 'archive'])->name('users.archive');

    Route::patch('users/{user}/restore', [UserController::class, 'restore'])->name('users.restore');
    Route::resource('users', UserController::class);
});

Route::middleware(['auth'])->resource('hotels', HotelController::class);

Route::middleware('auth')->prefix('booking')->controller(BookingController::class)->group(function () {

    Route::get('/', 'index');
    Route::get('create', 'create');
    Route::post('store', 'store');
    Route::post('check-in/{booking}', 'checkIn');
    Route::post('check-out/{booking}', 'checkOut');
    Route::post('cancel/{booking}', 'cancel')->name('booking.cancel');
    Route::get('edit', 'edit');
    Route::put('update', 'update');
    Route::get('available', 'availableRooms');
    Route::get('calendar', 'calendar')->name('room.calendar');
    Route::get('calendar/api', 'apiCalendar')->name('room.calendar.api');
    Route::get('calendar/resources', 'calendarResources')->name('room.calendar.resources');
    Route::get('night-report', 'nightReport')->name('booking.night_report');
});

Route::middleware('auth')->prefix('payment')->controller(PaymentController::class)->group(function () {

    Route::get('/', 'index')->name('payment.index');
    Route::get('booking/{booking}', 'show')->name('booking.show');
    Route::get('booking/{booking}/create', 'create')->name('payment.create');
    Route::post('booking/{booking}', 'store')->name('payment.store');
    Route::get('booking/{booking}/invoice', 'invoicePdf')->name('payment.invoice');
    Route::get('{payment}/receipt', 'receipt')->name('payment.receipt');
    Route::get('{payment}/edit', 'edit')->name('payment.edit');
    Route::put('{payment}', 'update')->name('payment.update');
    Route::delete('{payment}', 'destroy')->name('payment.destroy');
    Route::post('{payment}/refund', 'refund')->name('payment.refund');
});

Route::middleware('auth')->prefix('room')->controller(RoomController::class)->group(function () {

    Route::get('/', 'index');
    Route::get('create', 'create');
    Route::post('store', 'store');
    Route::get('edit/{room}', 'edit');
    Route::put('update/{room}', 'update');
    Route::get('occupancy', 'occupency');
    Route::get('occupancy/ajax', 'occupencyAjax');
});

// Simple web API endpoint to provide occupancy summary to dashboard (requires auth)
Route::get('api/occupancy/summary', [RoomController::class, 'apiSummary'])->middleware('auth');

// Booking statistics API (used by dashboard SPA polling)
Route::get('api/bookings/stats', [App\Http\Controllers\HomeController::class, 'apiBookingStats'])->middleware('auth');
