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
        return view("pages.erp.rooms.add");
    }

    public function store(Request $request)
    {
        // print_r($request->all());
        $hotelId = auth()->user()->hotel_id;

        // $roomType = new RoomType();
        // $roomType->hotel_id = $hotelId;
        // $roomType->name = $request->room_type_id;
        // $roomType->bed_type = $request->bed_type;
        // $roomType->price_per_night = $request->price_per_night;
        // $roomType->bed_count = $request->bed_count;
        // $roomType->capacity = $request->capacity;
        // $roomType->save();

        // $room = new Room();
        // $room->hotel_id = $hotelId;
        // $room->room_number = $request->room_number;
        // $room->room_type_id = $roomType->id;
        // $room->floor = $request->floor;
        // $room->status = $request->status;
        // $room->save();
        DB::transaction(function () use ($request, $hotelId) {

            // $roomType = RoomType::create([
            //     'hotel_id' => $hotelId,
            //     'name' => $request->room_type_id,
            //     'bed_type' => $request->bed_type,
            //     'price_per_night' => $request->price_per_night,
            //     'bed_count' => $request->bed_count,
            //     'capacity' => $request->capacity,
            // ]);

            Room::create([
                'hotel_id' => $hotelId,
                'room_number' => $request->room_number,
                'room_type_id' => $request->roomType_id,
                'floor' => $request->floor,
                'status' => $request->status,
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

    public function occupency()
    {
        $hotelId = auth()->user()->hotel_id;

        $rooms = Room::with('roomType.amenities')
            ->where('hotel_id', $hotelId)
            ->get();

        $roomTypes = RoomType::where('hotel_id', $hotelId)->get();

        return view('pages.erp.occupancy.index', compact('rooms'));
    }
}
