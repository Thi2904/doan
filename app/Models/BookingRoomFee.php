<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingRoomFee extends Model
{
    protected $fillable = [
        'booking_room_id',
        'fee_id',
        'price',
    ];

    public function bookingRoom()
    {
        return $this->belongsTo(BookingRoom::class);
    }

    public function fee()
    {
        return $this->belongsTo(AdditionalFee::class);
    }
}
