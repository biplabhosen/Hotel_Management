<?php

namespace App\Models;

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
    public function bookings()
    {
        return $this->hasMany(BookingRoom::class);
    }
}
