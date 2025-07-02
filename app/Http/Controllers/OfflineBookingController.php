<?php

namespace App\Http\Controllers;

use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;

class OfflineBookingController extends Controller
{
    public function createA()
    {
        $roomTypes = RoomType::all();
        $rooms = Room::where('status', 'available')->get();
        // thêm dữ liệu khác nếu cần

        return view('admin.bookings_offline', compact('roomTypes', 'rooms'));
    }
}
