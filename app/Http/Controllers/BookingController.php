<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingRoom;
use App\Models\Guest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\User;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $query = Booking::with(['guest', 'bookingRooms.room.roomType'])
            ->where('hotel_id', $hotelId);

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('q')) {
            $q = $request->q;
            $query->whereHas('guest', function ($qb) use ($q) {
                $qb->where('full_name', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('email', 'like', "%{$q}%");
            });
        }

        if ($request->filled('from') && $request->filled('to')) {
            $from = $request->from;
            $to = $request->to;
            $query->whereHas('bookingRooms', function ($qb) use ($from, $to) {
                $qb->where(function ($overlap) use ($from, $to) {
                    $overlap->whereBetween('check_in', [$from, $to])
                        ->orWhereBetween('check_out', [$from, $to])
                        ->orWhere(function ($q2) use ($from, $to) {
                            $q2->where('check_in', '<=', $from)
                                ->where('check_out', '>=', $to);
                        });
                });
            });
        }

        $bookings = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        // Compute totals and paid amounts per booking
        $bookings->getCollection()->transform(function ($booking) {
            $total = 0;
            foreach ($booking->bookingRooms as $br) {
                $checkIn = Carbon::parse($br->check_in);
                $checkOut = Carbon::parse($br->check_out);
                $nights = max(1, $checkIn->diffInDays($checkOut));
                $total += $nights * (float) $br->price_per_night;
            }

            $paid = DB::table('payments')
                ->where('booking_id', $booking->id)
                ->where('status', 'paid')
                ->sum('amount');

            $booking->computed_total = number_format($total, 2, '.', '');
            $booking->computed_paid_amount = number_format((float) $paid, 2, '.', '');
            $booking->computed_due_amount = number_format(max(0, $total - (float) $paid), 2, '.', '');

            // also expose convenient fields used by views
            $booking->paid_amount = $booking->computed_paid_amount;
            $booking->due_amount = $booking->computed_due_amount;

            // payment summary
            $booking->payments_count = $booking->payments()->count();
            $lastPayment = $booking->payments()->orderBy('created_at', 'desc')->value('created_at');
            $booking->last_payment_date = $lastPayment ? Carbon::parse($lastPayment)->toDateString() : null;

            // handy min/max dates for display and rules (use plain date strings to avoid Optional wrapping)
            $min = $booking->bookingRooms->min('check_in');
            $max = $booking->bookingRooms->max('check_out');
            $booking->arrival = $min ? Carbon::parse($min)->toDateString() : null;
            $booking->departure = $max ? Carbon::parse($max)->toDateString() : null;

            return $booking;
        });

        // status counts
        $counts = Booking::where('hotel_id', $hotelId)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $statuses = ['reserved', 'checked_in', 'checked_out', 'cancelled'];

        return view('pages.erp.bookings.index', compact('bookings', 'statuses', 'counts'));
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
        // $this->authorizeBooking($booking);

        if ($booking->status !== 'reserved') {
            return back()->withErrors('Only reserved bookings can be checked in.');
        }

        // Get all booking rooms for this booking
        $bookingRooms = $booking->bookingRooms()->get();

        if ($bookingRooms->isEmpty()) {
            return back()->withErrors('No rooms assigned to this booking.');
        }

        // Check if any room's check-in date is today (compare date-only to ignore time portion)
        $today = now()->toDateString();
        $hasCheckInToday = $bookingRooms->contains(function ($br) use ($today) {
            return Carbon::parse($br->check_in)->toDateString() === $today;
        });

        if (!$hasCheckInToday) {
            $minDate = $bookingRooms->min('check_in');
            $minDate = $minDate ? Carbon::parse($minDate)->toDateString() : null;
            return back()->withErrors('Check-in allowed only on arrival date (' . $minDate . '). Today is ' . $today);
        }

        // Check if advance payment is done (50% minimum)
        $total = 0;
        foreach ($bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paid = DB::table('payments')
            ->where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->where('type', '!=', 'refund')
            ->sum('amount');

        $advanceRequired = $total * 0.5;
        if ($paid < $advanceRequired) {
            return back()->withErrors("Minimum advance (50%) of BDT {$advanceRequired} required. Paid: BDT {$paid}");
        }

        // Update all rooms with today's check-in timestamp
        $booking->bookingRooms()->update(['checked_in_at' => now()]);

        // Update booking status
        $booking->update(['status' => 'checked_in']);

        return back()->with('success', 'Guest checked in successfully.');
    }

    public function checkOut(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if ($booking->status !== 'checked_in') {
            return back()->withErrors('Only checked-in bookings can be checked out.');
        }

        // Get all booking rooms for this booking
        $bookingRooms = $booking->bookingRooms()->get();

        if ($bookingRooms->isEmpty()) {
            return back()->withErrors('No rooms assigned to this booking.');
        }

        // Check if any room's check-out date is today (compare date-only to ignore time portion)
        $today = now()->toDateString();
        $hasCheckOutToday = $bookingRooms->contains(function ($br) use ($today) {
            return Carbon::parse($br->check_out)->toDateString() === $today;
        });

        if (!$hasCheckOutToday) {
            $maxDate = $bookingRooms->max('check_out');
            $maxDate = $maxDate ? Carbon::parse($maxDate)->toDateString() : null;
            return back()->withErrors('Check-out allowed only on departure date (' . $maxDate . '). Today is ' . $today);
        }

        // Check if full payment is done
        $total = 0;
        foreach ($bookingRooms as $br) {
            $checkIn = Carbon::parse($br->check_in);
            $checkOut = Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float)$br->price_per_night;
        }

        $paid = DB::table('payments')
            ->where('booking_id', $booking->id)
            ->where('status', 'paid')
            ->where('type', '!=', 'refund')
            ->sum('amount');

        if ($paid < $total) {
            $due = $total - $paid;
            return back()->withErrors("Outstanding balance: BDT {$due}. Please settle before check-out.");
        }

        // Update all rooms with today's check-out timestamp
        $booking->bookingRooms()->update(['checked_out_at' => now()]);

        // Update booking status
        $booking->update(['status' => 'checked_out']);

        return back()->with('success', 'Guest checked out successfully.');
    }

    public function canAddPayment(User $user, Booking $booking): bool
    {
        $this->authorizeBooking($booking);

        if ($booking->due_amount <= 0) {
            return false;
        }

        if (in_array($booking->status, ['cancelled', 'no_show'])) {
            return false;
        }

        if ($booking->status === 'checked_out') {
            return $user->hasRole('manager');
        }

        return true; // reserved or checked-in with due
    }

    protected function authorizeBooking(Booking $booking)
    {
        if ($booking->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }
    }

    /**
     * Cancel a booking (set status to cancelled)
     */
    public function cancel(Booking $booking)
    {
        $this->authorizeBooking($booking);

        if (in_array($booking->status, ['checked_out', 'cancelled'])) {
            return back()->withErrors('Cannot cancel a booking that is already checked-out or cancelled.');
        }

        $booking->update(['status' => 'cancelled']);

        return back()->with('success', 'Booking cancelled successfully.');
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
