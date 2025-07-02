<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        // Logic để lấy dữ liệu khách hàng (ví dụ: từ database)
        return view('admin.setting'); // Trả về view
    }
}
