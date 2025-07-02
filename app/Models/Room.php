<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = ['room_type_id', 'room_number', 'room_status']; // Các cột có thể điền dữ liệu

    public function roomType()
    {
        return $this->belongsTo(RoomType::class);
    }
    public function allocations()
    {
        return $this->hasMany(BookingAllocation::class);
    }
}
