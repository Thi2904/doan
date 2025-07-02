<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class BookingRoom extends Model
{
    protected $fillable = [
        'booking_id',
        'room_type_id',
        'adults',
        'children',
        'price_per_night',
        'nights',
        'sub_total',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }

    public function fees()
    {
        return $this->hasMany(BookingRoomFee::class);
    }
    public function surcharges()
    {
        return $this->belongsToMany(
            AdditionalFee::class,
            'booking_room_fees',   // tên bảng pivot
            'booking_room_id',     // foreign key pivot tới booking_rooms
            'fee_id'               // foreign key pivot tới additional_fees
        )
            ->withPivot('price')
            ->withTimestamps();
    }


    public function currentAllocation(Carbon $date = null)
    {
        $date = $date ?: Carbon::now();
        return $this->allocations()
            ->where('start_at', '<=', $date)
            ->where('end_at',   '>',  $date)
            ->first();
    }

    /**
     * Accessor để gọi $bookingRoom->room trả về đối tượng Room
     */
    public function getRoomAttribute()
    {
        $alloc = $this->currentAllocation();
        return $alloc ? $alloc->room : null;
    }
}

