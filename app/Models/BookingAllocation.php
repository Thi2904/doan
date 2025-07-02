<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAllocation extends Model
{
    protected $table = 'booking_allocations';

    protected $fillable = [
        'booking_id',
        'room_id',
        'start_at', 'end_at'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
    ];

    /**
     * The booking that this allocation belongs to.
     */
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * The actual room that was allocated.
     */
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
