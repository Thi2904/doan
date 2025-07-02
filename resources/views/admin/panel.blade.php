<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">

    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <title>Trang Admin - Hanoi Hotel</title>
</head>
<body>

@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')

    <main>
        <h1 class="title">Trang Chủ</h1>
        <ul class="breadcrumbs">
            <li><a href="#">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Trang Chủ</a></li>
        </ul>
        <div class="info-data">
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $activeUserCount }}</h2>
                        <p>Người Dùng Đang Họat Động</p>
                    </div>
                    <i class='bx bx-user-check icon'></i>
                </div>
                <span class="progress" data-value="{{ $userPercent }}%"></span>
                <span class="label">{{ $userPercent }}%</span>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $bookingCompleted }}</h2>
                        <p>Đơn Đặt Hoàn Thành</p>
                    </div>
                    <i class='bx bx-check-circle icon'></i>
                </div>
                <span class="progress" data-value="{{ $bookingCompletedPercent }}%"></span>
                <span class="label">{{ $bookingCompletedPercent }}%</span>
            </div>

            <!-- Bổ sung các card mới -->
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $cancelPercentThisMonth }}%</h2>
                        <p>Tỷ lệ hủy đơn tháng này</p>
                    </div>
                    <i class='bx bx-x-circle icon'></i>
                </div>
                <span class="progress" data-value="{{ $cancelPercentThisMonth }}%"></span>
                <span class="label">{{ $cancelPercentThisMonth }}%</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ $roomOccupancyRate }}%</h2>
                        <p>Tỷ lệ lấp phòng hôm nay</p>
                    </div>
                    <i class='bx bx-bed icon'></i>
                </div>
                <span class="progress" data-value="{{ $roomOccupancyRate }}%"></span>
                <span class="label">{{ $roomOccupancyRate }}%</span>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ number_format($totalRevenue, 0, ',', '.') }}đ</h2>
                        <p>Doanh thu tổng</p>
                    </div>
                    <i class='bx bx-wallet icon'></i>
                </div>
            </div>

            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ number_format($estimatedRevenueThisMonth, 0, ',', '.') }}đ</h2>
                        <p>Doanh thu dự kiến tháng này</p>
                    </div>
                    <i class='bx bx-bar-chart-alt-2 icon'></i>
                </div>
            </div>
            <div class="card">
                <div class="head">
                    <div>
                        <h2>{{ number_format($revenueThisMonth, 0, ',', '.') }}đ</h2>
                        <p>Doanh thu tổng tháng {{ \Carbon\Carbon::now('Asia/Ho_Chi_Minh')->format('m') }} này</p>
                    </div>
                    <i class='bx bx-bar-chart-alt-2 icon'></i>
                </div>
            </div>

        </div>




        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Số đơn đặt trong 7 ngày gần đây</h3>
                    <div class="menu">
                        <i class='bx bx-dots-horizontal-rounded icon'></i>
                        <ul class="menu-link">
                        </ul>
                    </div>
                </div>
                <div class="chart">
                    <div id="chart"></div>
                </div>
            </div>
            <div class="content-data">
                <div class="head">
                    <h3>Phòng có lịch đặt hôm nay</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>STT</th>
                            <th>Phòng</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Khách</th>
                            <th>Trạng thái</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($roomsBookedToday as $index => $allocation)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $allocation->room->room_number ?? 'N/A' }}</td>
                                <td>{{ \Carbon\Carbon::parse($allocation->start_at)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($allocation->end_at)->setTimezone('Asia/Ho_Chi_Minh')->format('d/m/Y') }}</td>
                                <td>{{ $allocation->booking->guest_name ?? '---' }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'checked_in'  => '#66BB6A', // xanh lá đậm
                                            'pending'     => '#FFA726', // cam nhạt
                                            'cancelled'   => '#EF5350', // đỏ
                                            'completed'   => '#66BB6A', // xanh lá đậm
                                            'confirmed'   => '#BDBDBD', // xám nhạt
                                        ];

                                        $statusLabels = [
                                            'checked_in'  => 'Đang ở',
                                            'pending'     => 'Đã đặt',
                                            'cancelled'   => 'Đã hủy',
                                            'completed'   => 'Hoàn thành',
                                            'confirmed'   => 'Đã xác nhận',
                                        ];

                                        $status = $allocation->booking->status ?? 'confirmed';
                                        $color = $statusColors[$status] ?? '#BDBDBD';  // mặc định xám nhạt
                                        $label = $statusLabels[$status] ?? ucfirst($status);
                                    @endphp

                                    <span style="padding: 3px 6px; border-radius: 4px; background-color: {{ $color }}; color: #fff;">
    {{ $label }}
</span>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Không có phòng nào được đặt hôm nay.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Dòng "Xem tất cả" -->
                <div style="margin-top: 16px; text-align: right;">
                    <a href="{{ route('admin.booking.calendar') }}" style="
            padding: 6px 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            display: inline-block;
        ">
                        Xem tất cả →
                    </a>
                </div>
            </div>

        </div>
    </main>
</section>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script>
    // Render chart
    var options = {
        chart: { type: 'area', height: 250 },
        series: [{ name: 'Đơn đặt', data: @json($counts) }],
        xaxis: { categories: @json($days) }
    };
    new ApexCharts(document.querySelector("#chart"), options).render();

    // Progress bars
    document.querySelectorAll('.progress').forEach(el => {
        el.style.width = el.getAttribute('data-value');
    });
</script>
</body>
</html>
