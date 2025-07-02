<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BedType extends Model
{
    protected $fillable = ['name'];

    public function roomType() {
        return $this->belongsToMany(RoomType::class)
            ->withPivot('quantity')
            ->withTimestamps();
    }
}
