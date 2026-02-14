<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Room extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'hotel_id',
        'room_type_id',
        'room_number',
        'floor',
        'status',
    ];


    /**
     * Cast attributes
     */
    protected $casts = [
        'floor' => 'integer',
    ];

    public function getStatusAttribute($value): string
    {
        $persistedStatus = $value ?? 'available';

        if (in_array($persistedStatus, ['dirty', 'cleaning', 'maintenance', 'out_of_order'], true)) {
            return $persistedStatus;
        }

        if (! $this->relationLoaded('bookingRooms')) {
            $this->load('bookingRooms.booking');
        }
        $today = Carbon::today();

        // OCCUPIED
        if ($this->bookingRooms->contains(
            fn($br) =>
            $br->check_in <= $today &&
                $br->check_out > $today &&
                $br->booking?->status === 'checked_in'
        )) {
            return 'occupied';
        }

        // RESERVED
        if ($this->bookingRooms->contains(
            fn($br) =>
            $br->check_in <= $today &&
                $br->check_out > $today &&
                in_array($br->booking?->status, ['confirmed', 'reserved'])
        )) {
            return 'reserved';
        }

        return $persistedStatus;
    }



    /**
     * Relationships
     */

    // Each room belongs to one hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // Each room belongs to one room type
    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    // Optional: future booking relation
    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class, 'room_id');
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(RoomHousekeeping::class, 'room_id');
    }
}
