<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;


class RoomController extends Controller
{
    public function index()
    {
        $rooms = Room::all(); // Lấy tất cả các phòng
        $roomTypes = RoomType::all(); // Lấy tất cả các loại phòng
        return view('admin.rooms', compact('rooms', 'roomTypes')); // Trả về view với dữ liệu phòng và loại phòng
    }

    public function store(Request $request)
    {
        // Thêm mới phòng
        $request->validate([
            'room_number' => 'required|string|max:20',
            'room_type_id' => 'required|exists:room_types,id',
            'room_status' => 'required|in:available,booked,under_maintenance',
        ]);

        Room::create($request->all());
        return redirect()->route('rooms.index');
    }

    public function update(Request $request, Room $room)
    {
        // Validate dữ liệu đầu vào
        $request->validate([
            'room_number' => 'required|string|max:255',
            'room_type_id' => 'required|exists:room_types,id',
            'room_status' => 'required|in:available,booked,under_maintenance',
        ]);

        // Cập nhật thông tin phòng
        $room->update([
            'room_number' => $request->room_number,
            'room_type_id' => $request->room_type_id,
            'room_status' => $request->room_status,
        ]);

        // Chuyển hướng về danh sách phòng sau khi cập nhật
        return redirect()->route('rooms.index')->with('success', 'Phòng đã được cập nhật');
    }


    public function destroy(Room $room)
    {
        // Xóa phòng
        $room->delete();
        return redirect()->route('rooms.index');
    }

    public function roomStatusMatrix(Request $request)
    {
        $selectedDate = $request->input('date', now()->toDateString());

        $roomTypes = RoomType::with(['rooms.allocations.booking'])->get();

        return view('admin.room_matrix', [
            'roomTypes' => $roomTypes,
            'selectedDate' => $selectedDate,
        ]);
    }

    public function roomTableView(Request $request)
    {
        // Lấy ngày được chọn, mặc định là hôm nay theo GMT+7
        $selectedDate = $request->input('date', Carbon::now('Asia/Ho_Chi_Minh')->toDateString());

        // Lấy danh sách room types với phòng và allocations liên quan
        $roomTypes = RoomType::with(['rooms.allocations.booking'])->get();

        return view('admin.room_view', compact('roomTypes', 'selectedDate'));
    }

    public function getRoomDetails(Request $request)
    {
        $roomId = $request->query('room_id');
        $room = Room::with('roomType')->find($roomId);

        if (!$room) {
            return response()->json(['error' => 'Phòng không tồn tại'], 404);
        }

        return response()->json([
            'room_number' => $room->room_number,
            'room_type' => $room->roomType->name ?? 'Không rõ',
            'status' => $room->room_status,
            'description' => $room->description,
        ]);
    }
}
