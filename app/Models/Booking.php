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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(RoomHousekeeping::class);
    }

    /**
     * Helpers
     */

    public function getTotalAmountAttribute()
    {
        $total = 0;
        foreach ($this->bookingRooms as $br) {
            $checkIn = \Carbon\Carbon::parse($br->check_in);
            $checkOut = \Carbon\Carbon::parse($br->check_out);
            $nights = max(1, $checkIn->diffInDays($checkOut));
            $total += $nights * (float) $br->price_per_night;
        }

        return round($total, 2);
    }

    public function getPaidAmountAttribute()
    {
        $paid = $this->payments()
            ->where('status', 'paid')
            ->where('type', '!=', 'refund')
            ->sum('amount');

        $refunds = $this->payments()
            ->where('type', 'refund')
            ->sum('amount');

        return max(0, round($paid - $refunds, 2));
    }

    public function getDueAmountAttribute()
    {
        return round($this->total_amount - $this->paid_amount, 2);
    }

    /**
     * Accessors & Mutators
     */
    
    public function getPaymentStatusAttribute()
    {
        if ($this->paid_amount <= 0) return 'unpaid';
        if ($this->paid_amount < $this->total_amount) return 'partial';
        return 'paid';
    }
}
