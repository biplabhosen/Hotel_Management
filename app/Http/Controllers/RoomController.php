<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::with([
            'bookingRooms.booking' => fn($q) =>
            $q->whereIn('status', ['reserved', 'confirmed', 'checked_in'])
        ])->get();

        return view("pages.erp.rooms.index" , compact('rooms'));
    }

    public function create()
    {
        $hotelId = auth()->user()->hotel_id;
        $roomTypes = RoomType::where('hotel_id', $hotelId)->orderBy('name')->get();
        return view("pages.erp.rooms.add", compact('roomTypes'));
    }

    public function store(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $validated = $request->validate([
            'room_number' => 'required|string',
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where(fn($query) => $query->where('hotel_id', $hotelId)),
            ],
            'floor' => 'required|integer',
            'status' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $hotelId) {
            Room::create([
                'hotel_id' => $hotelId,
                'room_number' => $validated['room_number'],
                'room_type_id' => $validated['room_type_id'],
                'floor' => $validated['floor'],
                'status' => $validated['status'],
            ]);
        });


        return redirect()->back()->with('success', 'Room created successfully.');
    }

    //     /**
    //      * Update the specified room.
    public function update(Request $request, Room $room)
    {
        $hotelId = auth()->user()->hotel_id;
        // Security: hotel isolation
        if ($room->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }

        $room = new RoomType();
        $room->hotel_id = $hotelId;
        $room->name = $request->room_type_id;
        $room->bed_type = $request->bed_type;
        $room->price_per_night = $request->price_per_night;
        $room->bed_count = $request->bed_count;
        $room->capacity = $request->capacity;
        $room_type_id = $room->update();

        $room = new Room();
        $room->hotel_id = $hotelId;
        $room->room_number = $request->room_number;
        $room->room_type_id = $room_type_id;
        $room->floor = $request->floor;
        $room->status = $request->status;
        $room->update();

        return redirect()->back()->with('success', 'Room updated successfully.');
    }

    public function occupency(Request $request)
    {
        $hotelId = auth()->user()->hotel_id;

        $query = Room::with(['roomType.amenities', 'bookingRooms.booking'])
            ->where('hotel_id', $hotelId);

        if ($request->filled('q')) {
            $query->where('room_number', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('room_type')) {
            $query->where('room_type_id', $request->room_type);
        }

        $roomsCollection = $query->get();

        // Date range parsing
        $start = $request->filled('start_date') ? \Carbon\Carbon::parse($request->start_date) : null;
        $end = $request->filled('end_date') ? \Carbon\Carbon::parse($request->end_date) : null;

        // Compute status for the selected range per room
        $roomsWithStatus = $roomsCollection->map(function ($room) use ($start, $end) {
            $status = 'available';

            foreach ($room->bookingRooms as $br) {
                $checkIn = \Carbon\Carbon::parse($br->check_in);
                $checkOut = \Carbon\Carbon::parse($br->check_out);

                // overlap condition
                $overlap = false;

                if ($start && $end) {
                    $overlap = $checkIn <= $end && $checkOut > $start;
                } elseif ($start) {
                    $overlap = $checkIn <= $start && $checkOut > $start;
                } elseif ($end) {
                    $overlap = $checkIn <= $end && $checkOut > $end;
                } else {
                    $today = \Carbon\Carbon::today();
                    $overlap = $checkIn <= $today && $checkOut > $today;
                }

                if ($overlap) {
                    $bstatus = $br->booking?->status;
                    if ($bstatus === 'checked_in') {
                        $status = 'occupied';
                        break; // occupied takes precedence
                    }

                    if (in_array($bstatus, ['reserved', 'confirmed'])) {
                        $status = 'booked';
                    }
                }
            }

            $room->range_status = $status;
            return $room;
        });

        // Keep a baseline (before status filter) to compute summary numbers for selected filters & date range
        $baseline = $roomsWithStatus;

        // Apply status filter if requested
        if ($request->filled('status')) {
            $roomsWithStatus = $roomsWithStatus->filter(fn($r) => $r->range_status === $request->status)->values();
        }

        $totalRooms = $baseline->count();
        $availableCount = $baseline->where('range_status', 'available')->count();
        $occupiedCount = $baseline->where('range_status', 'occupied')->count();
        $occupancyRate = $totalRooms > 0 ? round(($occupiedCount / $totalRooms) * 100, 2) : 0;

        $roomTypes = RoomType::where('hotel_id', $hotelId)->orderBy('name')->get();

        return view('pages.erp.occupancy.index', compact(
            'roomsWithStatus',
            'roomTypes',
            'totalRooms',
            'availableCount',
            'occupiedCount',
            'occupancyRate'
        ));
    }
}
