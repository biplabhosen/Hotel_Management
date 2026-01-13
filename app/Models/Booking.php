<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    // protected $fillable = [
    //     'hotel_id',
    //     'guest_name',
    //     'guest_email',
    //     'guest_phone',
    //     'check_in',
    //     'check_out',
    //     'status',
    //     'total_amount',
    //     'paid_amount',
    //     'booking_reference',
    // ];
    protected $guarded = [];

    protected $casts = [
        'check_in' => 'date',
        'check_out' => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    /**
     * Relationships
     */

    // Booking belongs to a hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // Booking has many booked rooms
    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    // Access rooms directly
    public function rooms()
    {
        return $this->belongsToMany(
            Room::class,
            'booking_rooms'
        )->withPivot([
            'price_per_night',
            'check_in',
            'check_out',
        ]);
    }

    public function guest()
    {
        return $this->belongsTo(Guest::class);
    }
    
    /**
     * Helpers
     */

    public function getDueAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }
}
