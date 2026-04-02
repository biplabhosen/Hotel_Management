<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SslCommerzTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'booking_id',
        'transaction_id',
        'amount',
        'currency',
        'status',
        'payment_type',
        'payment_id',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Relationships

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    // Scopes

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeByTransactionId($query, $transactionId)
    {
        return $query->where('transaction_id', $transactionId);
    }
}
