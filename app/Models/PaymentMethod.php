<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'name',
        'is_active',
    ];

    public function payments()
    {
        return $this->hasMany(BookingPayment::class);
    }
}
