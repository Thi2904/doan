<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingPayment extends Model
{
    protected $fillable = [
        'booking_id', 'payment_method_id', 'amount',
        'entry_type', 'status', 'description', 'paid_at'
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Thêm quan hệ này để gọi $payment->paymentMethod
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id');
    }
}
