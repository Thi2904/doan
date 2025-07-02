<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AdditionalFee;
use Illuminate\Http\Request;

class AdditionalFeeController extends Controller
{
    // Hiển thị danh sách phụ phí
    public function index()
    {
        $additionalFees = AdditionalFee::all();
        return view('admin.additional_fees', compact('additionalFees'));
    }

    // Lưu phụ phí mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pre_fee,post_fee',
            'default_price' => 'required|numeric|min:0',
        ]);

        AdditionalFee::create([
            'name' => $request->name,
            'type' => $request->type,
            'default_price' => $request->default_price,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('additional_fees.index')->with('success', 'Phụ phí đã được thêm thành công.');
    }

    // Chỉnh sửa phụ phí
    public function edit(AdditionalFee $additionalFee)
    {
        return view('admin.additional_fees.edit', compact('additionalFee'));
    }

    // Cập nhật phụ phí
    public function update(Request $request, AdditionalFee $additionalFee)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:pre_fee,post_fee',
            'default_price' => 'required|numeric|min:0',
        ]);

        $additionalFee->update([
            'name' => $request->name,
            'type' => $request->type,
            'default_price' => $request->default_price,
            'is_active'    => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('additional_fees.index')->with('success', 'Phụ phí đã được cập nhật thành công.');
    }

    // Xóa phụ phí
    public function destroy(AdditionalFee $additionalFee)
    {
        $additionalFee->delete();
        return redirect()->route('additional_fees.index')->with('success', 'Phụ phí đã được xóa thành công.');
    }
}
