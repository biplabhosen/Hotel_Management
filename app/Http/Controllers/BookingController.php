<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index()
    {
        return view("pages.erp.bookings.index");
    }

    public function create()
    {
        $hotelId = auth()->user()->hotel_id;
        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->get();
        $roomTypes = RoomType::where('hotel_id', $hotelId)
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
        return view("pages.erp.bookings.create", compact('rooms', 'roomTypes'));
    }

    public function availableRooms(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        $rooms = Room::with('roomType')
            ->where('hotel_id', $hotelId)
            ->where('room_type_id', $request->room_type_id)
            ->whereDoesntHave('bookingRooms', function ($q) use ($request) {
                $q->where(function ($overlap) use ($request) {
                    $overlap->whereBetween('check_in', [$request->check_in, $request->check_out])
                        ->orWhereBetween('check_out', [$request->check_in, $request->check_out])
                        ->orWhere(function ($q) use ($request) {
                            $q->where('check_in', '<=', $request->check_in)
                                ->where('check_out', '>=', $request->check_out);
                        });
                });
            })
            ->get();

        return response()->json($rooms);
    }



    public function store(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $g = $request->validate([
            'full_name' => 'required|string|min:2|max:255',
            'phone' => 'required|string|min:11|max:20',
            'email' => 'nullable|email',

            'total_guests' => 'required|integer|min:1',

            'rooms' => 'required|array|min:1',
            // 'rooms.*.room_id' => 'required|exists:rooms,id',
            // 'rooms.*.check_in' => 'required|date',
            // 'rooms.*.check_out' => 'required|date|after:rooms.*.check_in',
            'rooms.*' => 'exists:rooms,id',

            'check_in' => 'required|date',
            'check_out' => 'required|date|after:check_in',
        ]);

        DB::beginTransaction();

        try {

            /*
        |--------------------------------------------------------------------------
        | 1️⃣ Create or reuse guest
        |--------------------------------------------------------------------------
        */
            $guest = Guest::firstOrCreate(
                [
                    'hotel_id' => $hotelId,
                    'phone' => $request->phone,
                ],
                [
                    'full_name' => $request->full_name,
                    'email' => $request->email,
                ]
            );

            /*
        |--------------------------------------------------------------------------
        | 2️⃣ Create booking (header)
        |--------------------------------------------------------------------------
        */
            $booking = Booking::create([
                'hotel_id' => $hotelId,
                'guest_id' => $guest->id,
                'total_guests' => $request->total_guests,
                'status' => 'reserved',
            ]);

            /*
        |--------------------------------------------------------------------------
        | 3️⃣ Loop rooms and check availability
        |--------------------------------------------------------------------------
        */
            // foreach ($request->rooms as $item) {

            //     $room = Room::with('roomType')
            //         ->where('hotel_id', $hotelId)
            //         ->findOrFail($item['room_id']);

            //     // ❌ Availability check (CRITICAL)
            //     $isBooked = BookingRoom::where('room_id', $room->id)
            //         ->where('check_in', '<', $item['check_out'])
            //         ->where('check_out', '>', $item['check_in'])
            //         ->exists();

            //     if ($isBooked) {
            //         throw new \Exception(
            //             "Room {$room->room_number} is not available for selected dates."
            //         );
            //     }

            //     /*
            // |--------------------------------------------------------------------------
            // | 4️⃣ Freeze price and insert booking_rooms
            // |--------------------------------------------------------------------------
            // */
            //     BookingRoom::create([
            //         'booking_id' => $booking->id,
            //         'room_id' => $room->id,
            //         'price_per_night' => $room->roomType->price_per_night,
            //         'check_in' => $item['check_in'],
            //         'check_out' => $item['check_out'],
            //     ]);

            foreach ($request->rooms as $roomId) {

                $room = Room::with('roomType')
                    ->where('hotel_id', $hotelId)
                    ->findOrFail($roomId);

                $isBooked = BookingRoom::where('room_id', $room->id)
                    ->where('check_in', '<', $request->check_out)
                    ->where('check_out', '>', $request->check_in)
                    ->exists();

                if ($isBooked) {
                    throw new \Exception(
                        "Room {$room->room_number} is not available for selected dates."
                    );
                }

                BookingRoom::create([
                    'booking_id' => $booking->id,
                    'room_id' => $room->id,
                    'price_per_night' => $room->roomType->price_per_night,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                ]);
            }

            DB::commit();

            return redirect()
                ->back()
                ->with('success', 'Booking created successfully.');
        } catch (\Exception $e) {

            DB::rollBack();

            return redirect()
                ->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function checkIn(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'reserved') {
            return back()->withErrors('Only reserved bookings can be checked in.');
        }

        if ($booking->check_in !== now()->toDateString()) {
            return back()->withErrors('Check-in allowed only on arrival date.');
        }

        $booking->update(['status' => 'checked_in']);

        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'checked_in') {
            return back()->withErrors('Only checked-in bookings can be checked out.');
        }

        $booking->update(['status' => 'checked_out']);

        return back()->with('success', 'Guest checked out successfully.');
    }

    protected function authorizeBooking(Booking $booking)
    {
        if ($booking->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }
    }



    // public function occupency(){
    //     $hotelId = auth()->user()->hotel_id;

    // $rooms = Room::with('roomType')
    //     ->where('hotel_id', $hotelId)
    //     ->get();

    // $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

    // return view('pages.erp.occupancy.index', compact('rooms', 'roomTypes'));
    // }
}
