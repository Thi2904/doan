<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class PaymentMethodController extends Controller
{
    public function index()
    {
        $paymentMethods  = PaymentMethod::all();
        return view('admin.payment_methods', compact('paymentMethods'));
    }

    // Lưu phương thức thanh toán mới
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        PaymentMethod::create([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('payment_methods.index')->with('success', 'Phương thức thanh toán đã được thêm thành công.');
    }

    // Hiển thị form chỉnh sửa
    public function edit(PaymentMethod $paymentMethod)
    {
        return view('admin.payment_methods.edit', compact('paymentMethod'));
    }

    // Cập nhật phương thức thanh toán
    public function update(Request $request, PaymentMethod $paymentMethod)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $paymentMethod->update([
            'name' => $request->name,
            'is_active' => $request->has('is_active') ? 1 : 0,
        ]);

        return redirect()->route('payment_methods.index')->with('success', 'Phương thức thanh toán đã được cập nhật thành công.');
    }


    public function destroy(PaymentMethod $paymentMethod)
    {
        $paymentMethod->delete();

        return redirect()->route('payment_methods.index')
            ->with('success', 'Payment method deleted.');
    }
}
