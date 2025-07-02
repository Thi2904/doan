<?php

namespace App\Http\Controllers;

use App\Models\AdditionalFee;
use App\Models\Booking;
use App\Models\RoomType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerController extends Controller
{
    // Danh sách khách hàng
    public function index()
    {
        $customers = User::where('role', 'customer')->paginate(10);
        return view('admin.customer', compact('customers'));
    }

    // Form thêm khách hàng (nếu dùng modal, không cần route này)
    public function create()
    {
        return view('admin.customer-create');
    }

    // Xử lý thêm khách hàng
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'customer',
        ]);

        return redirect()->route('admin.customers')->with('success', 'Khách hàng đã được thêm.');
    }

    // Form chỉnh sửa khách hàng
    public function edit($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customer-edit', compact('customer'));
    }

    // Xử lý cập nhật khách hàng
    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate dữ liệu
        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|unique:users,email,' . $user->id,
            'phone'   => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        // Cập nhật dữ liệu người dùng
        $user->update($validated);

        return back()->with('success', 'Thông tin cá nhân đã được cập nhật thành công.');
    }

    // Xem chi tiết
    public function show($id)
    {
        $customer = User::findOrFail($id);
        return view('admin.customer-show', compact('customer'));
    }

    // Xóa khách hàng
    public function destroy($id)
    {
        $customer = User::findOrFail($id);
        $customer->delete();

        return redirect()->route('admin.customers')->with('success', 'Xóa khách hàng thành công.');
    }

    public function toggleStatus($id)
    {
        $user = User::where('role', 'customer')->findOrFail($id);
        $user->is_active = !$user->is_active;
        $user->save();

        return redirect()->back()->with('success', 'Trạng thái khách hàng đã được cập nhật.');
    }

    public function profile()
    {
        $user = Auth::user();

        // Lấy booking theo user_id
        $bookings = Booking::with([
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room.roomType'
        ])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Gán nhãn trạng thái và đồng bộ check-in/out xuống bookingRooms
        foreach ($bookings as $booking) {
            $booking->status_label = match ($booking->status) {
                'pending'     => 'Đang chờ',
                'confirmed'   => 'Đã xác nhận',
                'checked_in'  => 'Đã nhận phòng',
                'completed'   => 'Hoàn thành',
                'cancelled'   => 'Đã hủy',
                default       => 'Không xác định',
            };

            foreach ($booking->bookingRooms as $room) {
                $room->check_in  = $booking->check_in;
                $room->check_out = $booking->check_out;
            }
        }

        // Phụ phí còn hiệu lực, nhóm theo type
        $additionalFees = AdditionalFee::where('is_active', true)
            ->get()
            ->groupBy('type');

        // Danh sách loại phòng để hiển thị lựa chọn
        $roomTypes = RoomType::all(['id','name','base_price']);

        return view('customer.profile', compact('bookings', 'additionalFees', 'roomTypes'));
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required'],
            'new_password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không đúng.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return back()->with('success', 'Mật khẩu đã được cập nhật thành công.');
    }
}
