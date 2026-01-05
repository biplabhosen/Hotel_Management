<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'icon',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationships
     */

    // Amenity belongs to many room types
    public function roomTypes()
    {
        return $this->belongsToMany(
            RoomType::class,
            'amenity_room_type'
        )->withTimestamps();
    }
}
