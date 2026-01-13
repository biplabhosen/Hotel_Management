<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guest extends Model
{
    protected $fillable = [
        'hotel_id',
        'full_name',
        'phone',
        'email',
    ];


}
