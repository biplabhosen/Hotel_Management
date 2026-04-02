<?php

use App\Http\Controllers\Api\GuestApiController;
use App\Models\Hotel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('hotel', function () {
    return response()->json(Hotel::all());
});

Route::prefix('guest')->scopeBindings()->group(function () {
    Route::get('hotel-by-slug/{slug}', [GuestApiController::class, 'hotelBySlug']);
    Route::get('search-availability', [GuestApiController::class, 'searchAvailability']);
    Route::get('hotels/{hotel}', [GuestApiController::class, 'hotelInfo']);
    Route::get('hotels/{hotel}/amenities', [GuestApiController::class, 'amenities']);
    Route::get('hotels/{hotel}/room-types', [GuestApiController::class, 'roomTypes']);

    Route::get('hotels/{hotel}/rooms', [GuestApiController::class, 'rooms']);
    Route::get('hotels/{hotel}/rooms/{room}', [GuestApiController::class, 'roomDetails'])
        ->missing(fn() => response()->json(['success' => false, 'message' => 'Room not found.'], 404));

    Route::post('hotels/{hotel}/availability', [GuestApiController::class, 'availabilityCheck']);
    Route::post('hotels/{hotel}/bookings', [GuestApiController::class, 'createBooking']);
    Route::get('hotels/{hotel}/bookings', [GuestApiController::class, 'bookingLookup']);
    Route::get('hotels/{hotel}/bookings/{booking}/confirmation', [GuestApiController::class, 'bookingConfirmation'])
        ->missing(fn() => response()->json(['success' => false, 'message' => 'Booking not found.'], 404));
    Route::post('hotels/{hotel}/bookings/{booking}/cancel', [GuestApiController::class, 'cancelBooking'])
        ->missing(fn() => response()->json(['success' => false, 'message' => 'Booking not found.'], 404));
});
