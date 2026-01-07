<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomType extends Model
{
    use HasFactory;

    protected $fillable = [
        'hotel_id',
        'name',
        'code',
        'bed_type',
        'bed_count',
        'capacity',
        'price_per_night',
        'description',
        'is_active',
    ];

    protected $casts = [
        'bed_count' => 'integer',
        'capacity' => 'integer',
        'price_per_night' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Room type belongs to a hotel
    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    // Room type has many rooms
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    // Optional: amenities via pivot
    // public function amenities()
    // {
    //     return $this->belongsToMany(
    //         Amenity::class,
    //         'amenity_room_type'
    //     )->withTimestamps();
    // }
    public function amenities()
{
    return $this->belongsToMany(
        Amenity::class,
        'amenity_room_type',
        'room_type_id',
        'amenity_id'
    );
}
}
