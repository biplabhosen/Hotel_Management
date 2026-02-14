<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    protected $fillable = [
        'hotel_id',
        'name',
        'email',
        'phone',
        'role',
        'status',
    ];

    public function hotel()
    {
        return $this->belongsTo(Hotel::class);
    }

    public function housekeepingTasks()
    {
        return $this->hasMany(RoomHousekeeping::class, 'staff_id');
    }
}
