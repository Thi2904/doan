<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $fillable = ['name'];

    public function roomTypes()
    {
        return $this->belongsToMany(RoomType::class, 'feature_room_type')
            ->withTimestamps();
    }
}
