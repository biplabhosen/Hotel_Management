<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(){
        return view("pages.erp.bookings.index");
    }

    public function create(){
        return view("pages.erp.bookings.add");
    }

    public function occupency(){
        return view("pages.erp.occupancy.index");
    }
}
