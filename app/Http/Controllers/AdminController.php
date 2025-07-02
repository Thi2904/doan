<?php
namespace App\Http\Controllers;

use App\Models\BookingAllocation;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function panel()
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->route('home')->withErrors(['access_denied' => 'Bạn không có quyền truy cập.']);
        }

        $timezone = 'Asia/Ho_Chi_Minh';

        // 1. Biểu đồ 7 ngày
        $days = collect(range(0, 6))->map(function ($i) use ($timezone) {
            return Carbon::now($timezone)->subDays(6 - $i)->toDateString();
        });

        $counts = $days->map(function ($day) use ($timezone) {
            $startOfDayUtc = Carbon::parse($day, $timezone)->startOfDay()->setTimezone('UTC');
            $endOfDayUtc   = Carbon::parse($day, $timezone)->endOfDay()->setTimezone('UTC');
            return Booking::whereBetween('created_at', [$startOfDayUtc, $endOfDayUtc])->count();
        });

        // 2. Tổng số liệu người dùng và phòng
        $activeUserCount  = User::where('is_active', 1)->count();
        $roomTotal        = Room::count();
        $roomAvailable    = Room::where('room_status', 'available')->count();
        $roomBooked       = Room::where('room_status', 'booked')->count();
        $bookingTotal     = Booking::count();
        $bookingCompleted = Booking::where('status', 'completed')->count();

        // 3. Tỷ lệ phần trăm
        $userPercent            = $activeUserCount > 0 ? number_format($activeUserCount / $activeUserCount * 100, 2) : 0;
        $roomAvailablePercent   = $roomTotal > 0 ? round($roomAvailable / $roomTotal * 100) : 0;
        $roomBookedPercent      = $roomTotal > 0 ? number_format(($roomTotal - $roomAvailable) / $roomTotal * 100, 2) : 0;
        $bookingCompletedPercent = $bookingTotal > 0 ? number_format($bookingCompleted / $bookingTotal * 100, 2) : 0;

        // 4. Thống kê hôm nay (GMT+7)
        $todayStart = Carbon::today($timezone)->startOfDay();
        $todayEnd   = Carbon::today($timezone)->endOfDay();

        $roomsBookedToday = BookingAllocation::with('room', 'booking')
            ->where(function ($q) use ($todayStart, $todayEnd) {
                $q->where('start_at', '<', $todayEnd)
                    ->where('end_at',   '>', $todayStart);
            })
            ->get();

        $roomsBookedTodayCount = $roomsBookedToday->pluck('room_id')->unique()->count();
        $roomOccupancyRate     = $roomTotal > 0
            ? round($roomsBookedTodayCount / $roomTotal * 100, 2)
            : 0;

        // 5. Tỷ lệ hủy trong tháng
        $monthStart = Carbon::now($timezone)->startOfMonth()->setTimezone('UTC');
        $monthEnd   = Carbon::now($timezone)->endOfMonth()->setTimezone('UTC');

        $totalThisMonth  = Booking::whereBetween('created_at', [$monthStart, $monthEnd])->count();
        $cancelThisMonth = Booking::where('status', 'cancelled')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->count();

        $cancelPercentThisMonth = $totalThisMonth > 0
            ? round($cancelThisMonth / $totalThisMonth * 100)
            : 0;

        // 6. Doanh thu
        // 6.1. Tổng doanh thu thực nhận (tất cả booking đã hoàn tất)
        $totalRevenue = Booking::where('status', 'completed')->sum('total_price');

        // 6.2. Doanh thu dự kiến tháng này (tất cả booking không bị huỷ trong tháng này)
        $estimatedRevenueThisMonth = Booking::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('status', '!=', 'cancelled')
            ->sum('total_price');

        // 6.3. **Doanh thu thực thu tháng này** (booking hoàn tất trong tháng này)
        $revenueThisMonth = Booking::where('status', 'completed')
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('total_price');

        return view('admin.panel', compact(
            'activeUserCount',
            'roomTotal',
            'days',
            'counts',
            'roomAvailable',
            'roomBooked',
            'bookingTotal',
            'bookingCompleted',
            'userPercent',
            'roomAvailablePercent',
            'roomBookedPercent',
            'bookingCompletedPercent',
            'roomsBookedToday',
            'cancelPercentThisMonth',
            'totalRevenue',
            'estimatedRevenueThisMonth',
            'revenueThisMonth',    // thêm biến này
            'roomOccupancyRate'
        ));
    }
}
