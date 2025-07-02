<?php

namespace App\Http\Controllers;

use App\Models\RoomType;
use App\Models\Feature;
use App\Models\BedType;
use Illuminate\Http\Request;

class RoomTypeController extends Controller
{
    /**
     * Hiển thị danh sách các loại phòng cùng tiện nghi và cấu hình giường
     */
    public function index()
    {
        $roomTypes = RoomType::with(['features', 'bedTypes'])->get();
        $features = Feature::all();
        $bedTypes = BedType::all();

        return view('admin.room_types', compact('roomTypes', 'features', 'bedTypes'));
    }

    /**
     * Lưu loại phòng mới
     */
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:1',
            'view' => 'required|string|max:100',
            'description' => 'nullable|string',
            'max_adult' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
            'beds' => 'nullable|array',
            'beds.*' => 'integer|min:0',
        ]);

        // Tạo loại phòng mới
        $roomType = RoomType::create([
            'name' => $data['name'],
            'base_price' => $data['base_price'],
            'area' => $data['area'],
            'view' => $data['view'],
            'description' => $data['description'],
            'max_adult' => $data['max_adult'],
            'max_children' => $data['max_children'] ?? null,
        ]);

        // Gắn tiện nghi
        if (!empty($data['features'])) {
            $roomType->features()->sync($data['features']);
        }

        // Gắn cấu hình giường
        if (!empty($data['beds'])) {
            $bedSync = [];
            foreach ($data['beds'] as $bedTypeId => $qty) {
                if ($qty > 0) {
                    $bedSync[$bedTypeId] = ['quantity' => $qty];
                }
            }
            $roomType->bedTypes()->sync($bedSync);
        }

        return redirect()
            ->route('room_types.index')
            ->with('success', 'Loại phòng đã được thêm thành công.');
    }

    /**
     * Cập nhật loại phòng
     */
    public function update(Request $request, RoomType $roomType)
    {
        // Xác thực dữ liệu, sử dụng description
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'base_price' => 'required|numeric|min:0',
            'area' => 'required|numeric|min:1',
            'view' => 'required|string|max:100',
            'description' => 'required|string',
            'max_adult' => 'required|integer|min:1',
            'max_children' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'features.*' => 'exists:features,id',
            'beds' => 'nullable|array',
            'beds.*' => 'integer|min:0',
        ]);

        // Cập nhật thông tin, bao gồm description
        $roomType->update([
            'name' => $data['name'],
            'base_price' => $data['base_price'],
            'area' => $data['area'],
            'view' => $data['view'],
            'description' => $data['description'],
            'max_adult' => $data['max_adult'],
            'max_children' => $data['max_children'] ?? null,
        ]);

        // Sync tiện nghi
        $roomType->features()->sync($data['features'] ?? []);

        // Sync cấu hình giường
        $bedSync = [];
        if (!empty($data['beds'])) {
            foreach ($data['beds'] as $bedTypeId => $qty) {
                if ($qty > 0) {
                    $bedSync[$bedTypeId] = ['quantity' => $qty];
                }
            }
        }
        $roomType->bedTypes()->sync($bedSync);

        return redirect()
            ->route('room_types.index')
            ->with('success', 'Loại phòng đã được cập nhật thành công.');
    }

    /**
     * Xóa loại phòng
     */
    public function destroy(RoomType $roomType)
    {
        $roomType->delete();

        return redirect()
            ->route('room_types.index')
            ->with('success', 'Loại phòng đã được xóa thành công.');
    }

    /**
     * Phần của Customer: hiển thị danh sách phòng có ảnh active
     */
    public function accommodations()
    {
        $roomTypes = RoomType::with([
            'images' => function ($q) {
                $q->where('is_active', true);
            },
            'features',
            'bedTypes'
        ])->get();
        $features = Feature::all();
        $bedTypes = BedType::all();

        return view('customer.room', compact('roomTypes', 'features', 'bedTypes'));
    }

    public function details($id)
    {
        $roomType = RoomType::with([
            'images' => function ($q) {
                $q->where('is_active', true)->orderBy('image_id');
            },
            'features', // Lấy các tiện nghi liên quan đến phòng
            'bedTypes'
        ])->findOrFail($id);

        // Lấy danh sách các tiện nghi liên quan đến phòng này
        $features = $roomType->features;

        // Các loại giường (bedTypes) có sẵn
        $bedTypes = BedType::all();

        // Lấy danh sách ảnh
        $images = $roomType->images;

        // Ảnh hero và ảnh thứ hai nếu có
        $heroImage = $images->get(0)?->image_url;
        $secondImage = $images->get(2)?->image_url;

        // Lấy các loại phòng khác (ngoại trừ phòng hiện tại)
        $otherRoomTypes = RoomType::where('id', '!=', $roomType->id)
            ->with(['images' => function ($q) {
                $q->where('is_active', true)->orderBy('image_id');
            }])
            ->paginate(3);

        return view('customer.room_details', compact(
            'roomType',
            'features',  // Gửi các tiện nghi liên quan đến phòng
            'bedTypes',
            'heroImage',
            'secondImage',
            'otherRoomTypes'
        ));
    }

    public function getRoomTypes()
    {
        // Lấy tất cả loại phòng từ cơ sở dữ liệu
        $roomTypes = RoomType::all(); // Bạn có thể thêm điều kiện nếu cần lọc phòng

        return response()->json([
            'success' => true,
            'roomTypes' => $roomTypes
        ]);
    }
}
