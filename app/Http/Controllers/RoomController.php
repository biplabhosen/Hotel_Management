<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class RoomController extends Controller
{
    public function index(){
        return view("pages.erp.rooms.index");
    }

    public function create(){
        return view("pages.erp.rooms.add");
    }

    public function store(Request $request)
{
    // print_r($request->all());
    $hotelId = auth()->user()->hotel_id;

    $room= new Room();
    $room= ;


//     // Check if room types exist for this hotel
//     $roomTypesExist = RoomType::where('hotel_id', $hotelId)->exists();

//     // Validation rules
//     $rules = [
//         'room_number' => [
//             'required',
//             'string',
//             Rule::unique('rooms')->where('hotel_id', $hotelId),
//         ],
//         'floor' => 'nullable|integer|min:0',
//         'status' => 'required|in:available,booked,maintenance',
//     ];

//     // Only require room_type_id if room types exist
//     if ($roomTypesExist) {
//         $rules['room_type_id'] = [
//             'required',
//             Rule::exists('room_types', 'id')->where('hotel_id', $hotelId),
//         ];
//     } else {
//         // If no room type exists, temporarily set it to null for validation
//         $request->merge(['room_type_id' => null]);
//     }

//     // Validate the request
//     $validated = $request->validate($rules);

//     // Add hotel_id to the validated data
//     $validated['hotel_id'] = $hotelId;

//     // --- Option 2: Create default room type if none exists ---
//     if (!$roomTypesExist) {
//         $defaultRoomType = RoomType::create([
//             'hotel_id' => $hotelId,
//             'name' => 'Default',
//             'bed_type' => $request->bed_type,
//         ]);
//         $validated['room_type_id'] = $defaultRoomType->id;
//     }
//     // --- End of Option 2 code ---

//     // Create the room
//     Room::create($validated);

//     return redirect()->back()->with('success', 'Room created successfully.');
// }

//     /**
//      * Update the specified room.
     */
    public function update(Request $request, Room $room)
    {
        // Security: hotel isolation
        if ($room->hotel_id !== auth()->user()->hotel_id) {
            abort(403);
        }

        $hotelId = auth()->user()->hotel_id;

        $validated = $request->validate([
            'room_type_id' => [
                'required',
                Rule::exists('room_types', 'id')->where('hotel_id', $hotelId),
            ],
            'room_number' => [
                'required',
                'string',
                Rule::unique('rooms')
                    ->where('hotel_id', $hotelId)
                    ->ignore($room->id),
            ],
            'floor' => 'nullable|integer|min:0',
            'status' => 'required|in:available,booked,maintenance',
        ]);

        $room->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Room updated successfully.');
    }
}
