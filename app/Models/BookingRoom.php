<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BookingRoom extends Model
{
    use HasFactory;

    protected $table = 'booking_rooms';

    protected $fillable = [
        'booking_id',
        'room_id',
        'price_per_night',
        'check_in',
        'check_out',
    ];

    protected $casts = [
        'price_per_night' => 'decimal:2',
        'check_in' => 'date',
        'check_out' => 'date',
    ];

    /**
     * Relationships
     */

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
