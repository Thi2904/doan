<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle($request, Closure $next, ...$roles)
    {
        // Kiểm tra nếu người dùng chưa đăng nhập
        if (!Auth::check()) {
            return redirect()->route('login');  // Chuyển hướng về trang đăng nhập
        }

        // Kiểm tra vai trò của người dùng
        if (!in_array(Auth::user()->role, $roles)) {
            // Nếu vai trò không khớp, trả về lỗi 403
            abort(403, 'Bạn không có quyền truy cập trang này.');
        }

        // Nếu vai trò hợp lệ, tiếp tục với request
        return $next($request);
    }
}

