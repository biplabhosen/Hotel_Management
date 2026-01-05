<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(){
        return view("pages.erp.bookings.index");
    }

    public function create(){
        return view("pages.erp.bookings.add");
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
