<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Hotel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'status',
    ];

    /**
     * Relationships
     */

    // Hotel has many users (managers/staff)
    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Hotel has many room types
    public function roomTypes()
    {
        return $this->hasMany(RoomType::class);
    }

    // Hotel has many rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Hotel has many bookings
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(RoomHousekeeping::class);
    }
}
