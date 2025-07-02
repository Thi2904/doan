<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function loginForm()
    {
        return view('auth.login'); // Trả về view đăng nhập
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Kiểm tra nếu tài khoản không active
            if (!$user->is_active) {
                Auth::logout(); // Đăng xuất nếu tài khoản không active

                // Thêm thông báo lỗi với PHP Flasher
                flash()->addError('Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.');

                return back()->withInput();
            }

            // Chuyển hướng đến trang tương ứng với vai trò của người dùng
            switch ($user->role) {
                case 'admin':
                    flash()->addSuccess('Đăng nhập thành công với quyền Admin.');
                    return redirect()->route('admin.panel');
                case 'reception':
                    flash()->addSuccess('Đăng nhập thành công với quyền Reception.');
                    return redirect()->route('reception.dashboard');
                case 'customer':
                default:
                    flash()->addSuccess('Đăng nhập thành công.');
                    return redirect()->route('customer.homepage');
            }
        }

        // Đăng nhập thất bại
        flash()->addError('Email hoặc mật khẩu không đúng.');
        return back()->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        flash()->addInfo('Đăng xuất thành công.');
        return redirect()->route('home');
    }
}
