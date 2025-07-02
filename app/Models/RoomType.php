<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomType extends Model
{
    protected $fillable = [
        'name',
        'base_price',
        'area',
        'view',
        'description', // Thêm dòng này
        'max_adult',
        'max_children',
    ];

    public function features()
    {
        return $this->belongsToMany(Feature::class, 'feature_room_type')
            ->withTimestamps();
    }

    public function bedTypes()
    {
        return $this->belongsToMany(
            BedType::class,
            'room_type_bed_type',   // tên bảng pivot
            'room_type_id',         // khóa ngoại tới RoomType
            'bed_type_id'           // khóa ngoại tới BedType
        )
            ->withPivot('quantity')
            ->withTimestamps();
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class, 'room_type_id', 'id');
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

}
