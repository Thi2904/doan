<?php

namespace App\Http\Controllers;

use App\Models\RoomChange;
use Illuminate\Http\Request;

class RoomChangeController extends Controller
{
    public function index()
    {
        $changes = RoomChange::with([
            'booking',
            'bookingRoom',
            'fromRoomType',
            'toRoomType',
        ])->orderBy('changed_at','desc')->get();

        return view('admin.room_change', compact('changes'));
    }

}
