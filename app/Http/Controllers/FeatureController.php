<?php

namespace App\Http\Controllers;

use App\Models\Feature;
use Illuminate\Http\Request;

class FeatureController extends Controller
{
    // Hiển thị danh sách tính năng
    public function index()
    {
        $features = Feature::all(); // Lấy tất cả tính năng từ cơ sở dữ liệu
        return view('admin.feature', compact('features')); // Trả về view với dữ liệu tính năng
    }

    // Lưu tính năng mới
    public function store(Request $request)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Tạo tính năng mới
        Feature::create([
            'name' => $request->name,
            'description' => $request->description ?? '', // Nếu không có mô tả thì gán rỗng
        ]);

        return redirect()->route('features.index')->with('success', 'Tính năng đã được thêm thành công.');
    }

    // Cập nhật tính năng
    public function update(Request $request, Feature $feature)
    {
        // Xác thực dữ liệu đầu vào
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Cập nhật thông tin tính năng
        $feature->update([
            'name' => $request->name,
            'description' => $request->description ?? '', // Nếu không có mô tả thì gán rỗng
        ]);

        return redirect()->route('features.index')->with('success', 'Tính năng đã được cập nhật thành công.');
    }

    // Xóa tính năng
    public function destroy(Feature $feature)
    {
        $feature->delete(); // Xóa tính năng khỏi cơ sở dữ liệu

        return redirect()->route('features.index')->with('success', 'Tính năng đã được xóa thành công.');
    }
}
