<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomHousekeeping extends Model
{
    use HasFactory;

    protected $table = 'room_housekeeping';

    protected $fillable = [
        'hotel_id',
        'room_id',
        'booking_id',
        'booking_room_id',
        'staff_id',
        'task_type',
        'status',
        'notes',
        'assigned_at',
        'completed_at',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bookingRoom()
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }
}
