<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalFee extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'default_price', 'is_active'];

    // Quan hệ với Booking
    public function bookings()
    {
        return $this->belongsToMany(Booking::class);
    }
}
