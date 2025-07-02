<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomChange extends Model
{
    protected $fillable = [
        'booking_id',
        'booking_room_id',
        'from_room_type_id',
        'to_room_type_id',
        'from_room_id',
        'to_room_id',
        'change_start_date',
        'change_end_date',
        'changed_at',
        'note',
    ];

    // Nếu bạn dùng changed_at riêng, disable timestamps mặc định cho created_at
    // protected $timestamps = true;

    // Quan hệ (nếu cần)
    public function bookingRoom() { return $this->belongsTo(BookingRoom::class); }
    public function booking()     { return $this->belongsTo(Booking::class); }
    public function fromRoomType(){ return $this->belongsTo(RoomType::class, 'from_room_type_id'); }
    public function toRoomType()  { return $this->belongsTo(RoomType::class, 'to_room_type_id'); }
    public function fromRoom()    { return $this->belongsTo(Room::class, 'from_room_id'); }
    public function toRoom()      { return $this->belongsTo(Room::class, 'to_room_id'); }
}
