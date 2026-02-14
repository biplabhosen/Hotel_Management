<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomHousekeeping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingCheckoutController extends Controller
{
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $bookings = Booking::with(['guest', 'bookingRooms.room'])
            ->where('hotel_id', $hotelId)
            ->where('status', 'checked_in')
            ->orderByDesc('id')
            ->get();

        return view('pages.erp.checkout.index', compact('bookings'));
    }

    public function checkout(Booking $booking)
    {
        $hotelId = auth()->user()->hotel_id;

        if ((int) $booking->hotel_id !== (int) $hotelId) {
            abort(403);
        }

        if ($booking->status !== 'checked_in') {
            return back()->withErrors('Only checked-in bookings can be checked out.');
        }

        DB::transaction(function () use ($booking, $hotelId) {
            $booking->load('bookingRooms.room');

            $booking->update(['status' => 'checked_out']);
            $booking->bookingRooms()->update(['checked_out_at' => now()]);

            foreach ($booking->bookingRooms as $bookingRoom) {
                Room::where('hotel_id', $hotelId)
                    ->where('id', $bookingRoom->room_id)
                    ->update(['status' => 'dirty']);

                RoomHousekeeping::create([
                    'hotel_id' => $hotelId,
                    'room_id' => $bookingRoom->room_id,
                    'booking_id' => $booking->id,
                    'booking_room_id' => $bookingRoom->id,
                    'task_type' => 'checkout_cleaning',
                    'status' => 'pending',
                    'notes' => 'Auto-created from checkout #' . $booking->id,
                ]);
            }
        });

        return back()->with('success', 'Booking checked out and housekeeping tasks created.');
    }
}
