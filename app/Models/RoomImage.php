<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomImage extends Model
{
    protected $primaryKey = 'image_id';

    protected $fillable = [
        'room_type_id',
        'image_url',
        'is_active',
    ];

    /**
     * Quan hệ tới RoomType
     */
    public function roomType()
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class, 'room_type_id');
    }

}
