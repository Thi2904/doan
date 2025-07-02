<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_code',
        'user_id',
        'guest_name',
        'guest_email',
        'guest_phone',
        'check_in',
        'check_out',
        'num_adults',
        'num_children',
        'status',
        'total_price',          //Đã chuẩn hóa tên trường
        'payment_method_id',    //Khóa ngoại đến phương thức thanh toán
    ];

    /**
     * Các phòng đã đặt
     */
    public function bookingRooms()
    {
        return $this->hasMany(BookingRoom::class);
    }

    public function booking_rooms()
    {
        // Giả sử bảng booking_rooms có khóa ngoại booking_id liên kết với booking
        return $this->hasMany(BookingRoom::class, 'booking_id', 'id');
    }

    /**
     * Các phụ phí cho mỗi phòng đã đặt
     */
    public function roomFees()
    {
        return $this->hasManyThrough(
            BookingRoomFee::class,
            BookingRoom::class,
            'booking_id',         // foreign key on booking_rooms table
            'booking_room_id',    // foreign key on booking_room_fees table
            'id',                 // local key on bookings table
            'id'                  // local key on booking_rooms table
        );
    }
    /**
     * Các lần thanh toán cho booking này
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    public function rooms() {
        return $this->hasMany(BookingRoom::class);
    }

    public function allocations() {
        return $this->hasMany(BookingAllocation::class);
    }

    public function payments() {
        return $this->hasMany(BookingPayment::class);
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }

    protected $casts = [
        'check_in'  => 'date',
        'check_out' => 'date',
    ];
}
