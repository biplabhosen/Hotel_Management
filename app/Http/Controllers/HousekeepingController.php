<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomHousekeeping;
use App\Models\Staff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HousekeepingController extends Controller
{
    public function index()
    {
        $hotelId = auth()->user()->hotel_id;

        $tasksByRoom = RoomHousekeeping::with(['room', 'staff', 'booking.guest'])
            ->where('hotel_id', $hotelId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('room_id')
            ->orderBy('id')
            ->get()
            ->groupBy('room_id');

        $staff = Staff::where('hotel_id', $hotelId)
            ->where('status', 'active')
            ->where('role', 'housekeeping')
            ->orderBy('name')
            ->get();

        return view('pages.erp.housekeeping.index', compact('tasksByRoom', 'staff'));
    }

    public function show(Room $room)
    {
        $hotelId = auth()->user()->hotel_id;

        if ((int) $room->hotel_id !== (int) $hotelId) {
            abort(403);
        }

        $tasks = RoomHousekeeping::with(['staff', 'booking.guest'])
            ->where('hotel_id', $hotelId)
            ->where('room_id', $room->id)
            ->whereIn('status', ['pending', 'in_progress'])
            ->orderBy('id')
            ->get();

        $staff = Staff::where('hotel_id', $hotelId)
            ->where('status', 'active')
            ->where('role', 'housekeeping')
            ->orderBy('name')
            ->get();

        return view('pages.erp.housekeeping.show', compact('room', 'tasks', 'staff'));
    }

    public function assign(Request $request, RoomHousekeeping $task)
    {
        $hotelId = auth()->user()->hotel_id;

        if ((int) $task->hotel_id !== (int) $hotelId) {
            abort(403);
        }

        $data = $request->validate([
            'staff_id' => 'required|integer',
        ]);

        $isValidStaff = Staff::where('hotel_id', $hotelId)
            ->where('id', $data['staff_id'])
            ->where('status', 'active')
            ->exists();

        if (! $isValidStaff) {
            return back()->withErrors('Invalid staff selection.');
        }

        $task->update([
            'staff_id' => $data['staff_id'],
            'status' => $task->status === 'completed' ? 'completed' : 'in_progress',
            'assigned_at' => now(),
        ]);

        Room::where('hotel_id', $hotelId)
            ->where('id', $task->room_id)
            ->whereNotIn('status', ['maintenance', 'out_of_order'])
            ->update(['status' => 'cleaning']);

        return back()->with('success', 'Housekeeping task assigned.');
    }

    public function complete(RoomHousekeeping $task)
    {
        $hotelId = auth()->user()->hotel_id;

        if ((int) $task->hotel_id !== (int) $hotelId) {
            abort(403);
        }

        DB::transaction(function () use ($task, $hotelId) {
            $task->update([
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $hasOpenTasks = RoomHousekeeping::where('hotel_id', $hotelId)
                ->where('room_id', $task->room_id)
                ->whereIn('status', ['pending', 'in_progress'])
                ->exists();

            if (! $hasOpenTasks) {
                Room::where('hotel_id', $hotelId)
                    ->where('id', $task->room_id)
                    ->whereNotIn('status', ['maintenance', 'out_of_order'])
                    ->update(['status' => 'available']);
            }
        });

        return back()->with('success', 'Task marked as completed.');
    }
}
