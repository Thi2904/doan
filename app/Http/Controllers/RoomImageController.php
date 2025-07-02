<?php

namespace App\Http\Controllers;

use App\Models\RoomImage;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RoomImageController extends Controller
{
    /**
     * Hiển thị danh sách ảnh phòng.
     */
    public function index()
    {
        $images    = RoomImage::with('roomType')->paginate(10);
        $roomTypes = RoomType::all();

        return view('admin.room_images', compact('images', 'roomTypes'));
    }

    /**
     * Lưu ảnh phòng mới.
     */
    public function store(Request $request)
    {
        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'image_file'   => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Lưu file ảnh, chỉ lấy path: storage/app/public/room_images/xxx.jpg
        $imagePath = $request->file('image_file')->store('room_images', 'public');

        RoomImage::create([
            'room_type_id' => $request->room_type_id,
            'image_url'    => $imagePath,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()
            ->route('room_images.index')
            ->with('success', 'Ảnh phòng đã được thêm thành công.');
    }

    /**
     * Cập nhật thông tin ảnh phòng.
     */
    public function update(Request $request, $id)
    {
        $roomImage = RoomImage::findOrFail($id);

        $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
            'image_file'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:4048',
        ]);

        $roomImage->room_type_id = $request->room_type_id;
        $roomImage->is_active    = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image_file')) {
            // Xóa file cũ
            if ($roomImage->image_url) {
                Storage::disk('public')->delete($roomImage->image_url);
            }
            // Lưu file mới
            $newImagePath             = $request->file('image_file')->store('room_images', 'public');
            $roomImage->image_url     = $newImagePath;
        }

        $roomImage->save();

        return redirect()
            ->route('room_images.index')
            ->with('success', 'Thông tin ảnh phòng đã được cập nhật.');
    }

    /**
     * Xóa ảnh phòng.
     */
    public function destroy($id)
    {
        $roomImage = RoomImage::findOrFail($id);

        if ($roomImage->image_url) {
            Storage::disk('public')->delete($roomImage->image_url);
        }

        $roomImage->delete();

        return redirect()
            ->route('room_images.index')
            ->with('success', 'Ảnh phòng đã được xóa.');
    }
}
