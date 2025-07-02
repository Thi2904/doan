@php use Illuminate\Support\Carbon; @endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <title>Danh Sách Phòng - Admin</title>
    <style>
        .badge { padding: 5px 10px; border-radius: 6px; font-size: 13px; font-weight: 600; color: white; display: inline-block; }
        .badge-success { background-color: #4caf50; }
        .badge-info    { background-color: #2196f3; }
        .badge-warning { background-color: #ffc107; color: #000; }
        .badge-danger  { background-color: #e53935; }
        .table-container { overflow-x: auto; margin-top: 25px; }
        .btn-date-nav { cursor: pointer; padding: 10px 14px; border-radius: 8px; background-color: #2c3e50; color: white; border: none; font-weight: 600; }
        #todayBtn { background-color: #7f8c8d; }
    </style>
</head>
<body>
@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')

    <main>
        <h1 class="title">Danh Sách Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a class="active">Danh sách phòng</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh Sách Phòng</h3>
                    <div style="display: flex; align-items: center;">
                        <button id="prevDay" class="btn-date-nav"><i class="ri-arrow-left-s-line"></i></button>
                        <button id="nextDay" class="btn-date-nav"><i class="ri-arrow-right-s-line"></i></button>
                        <button id="todayBtn" class="btn-date-nav" style="margin-left:16px; ">Hôm nay</button>
                        <label for="date" style="margin-left:16px; margin-right: 4px; font-weight:600;">Chọn ngày:</label>
                        <input type="date" id="date" name="date"
                               value="{{ request('date', now()->format('Y-m-d')) }}"
                               style="padding:6px 12px; border:1px solid #ccc; border-radius:8px;">
                    </div>
                </div>

                <div class="table-container">
                    <table id="allocation-table" class="display">
                        <thead>
                        <tr>
                            <th>Phòng</th>
                            <th>Loại</th>
                            <th>Trạng thái</th>
                            <th>Khách hàng</th>
                            <th>Ngày đến</th>
                            <th>Ngày đi</th>
                            <th>Ghi chú</th>
                        </tr>
                        </thead>
                        <tbody>
                        {{-- Vòng lặp hiển thị từng phòng --}}
                        @foreach ($roomTypes as $roomType)
                            @foreach ($roomType->rooms as $room)
                                @php
                                    $selected     = Carbon::parse(request('date', now()))->startOfDay();
                                    $statusClass  = 'available';
                                    $tooltip      = "Phòng {$room->room_number} - Trống";
                                    $guest        = '-';
                                    $startDisplay = '-';
                                    $endDisplay   = '-';
                                    $note         = '-';
                                    $guest_cccd   = null;
                                    $guest_phone  = null;
                                    $booking      = null; // Khởi tạo booking để check tồn tại sau

                                    if ($room->room_status === 'under_maintenance') {
                                        $statusClass = 'under_maintenance';
                                        $tooltip     = "Phòng {$room->room_number} - Đang bảo trì";
                                    } else {
                                        foreach ($room->allocations as $a) {
                                            $start   = Carbon::parse($a->start_at)->startOfDay();
                                            $end     = Carbon::parse($a->end_at)->startOfDay();
                                            $booking = $a->booking;

                                            // Nếu selected nằm trong [start, end] (inclusive cả hai đầu)
                                            if ($selected->between($start, $end)) {
                                                if ($selected->isSameDay($end)) {
                                                    // Ngày trả phòng
                                                    $statusClass = 'last_day';
                                                    $tooltip     = "Phòng {$room->room_number} - Trả phòng hôm nay";
                                                } else {
                                                    // Ngày check-in hoặc các ngày giữa
                                                    if ($booking->actual_check_in) {
                                                        $statusClass = 'checked_in';
                                                        $tooltip     = "Phòng {$room->room_number} - Đang ở";
                                                    } else {
                                                        $statusClass = 'booked_pending';
                                                        $tooltip     = "Phòng {$room->room_number} - Đã đặt, chưa check-in";
                                                    }
                                                }

                                                // Lưu thông tin để hiển thị thêm
                                                $guest        = $booking->guest_name ?? '-';
                                                $startDisplay = $start->format('d/m/Y');
                                                $endDisplay   = $end->format('d/m/Y');
                                                $note         = $booking->note ?? '-';
                                                $guest_cccd   = $booking->guest_id_number ?? null;
                                                $guest_phone  = $booking->guest_phone ?? null;
                                                break;
                                            }
                                        }
                                    }

                                    $clickable = in_array($statusClass, ['booked_pending','checked_in','last_day']);
                                @endphp

                                <tr>
                                    <td>{{ $room->room_number }}</td>
                                    <td>{{ $roomType->name }}</td>
                                    <td>
                <span class="badge badge-{{
                    $statusClass === 'available'           ? 'success'
                    : ($statusClass === 'under_maintenance' ? 'info'
                    : ($statusClass === 'last_day'          ? 'warning'
                    : ($statusClass === 'checked_in'        ? 'info'
                    : ($statusClass === 'booked_pending'    ? 'danger'
                    : 'dark'))))
                }}">
                    {{
                        $statusClass === 'available'           ? 'Trống'
                        : ($statusClass === 'under_maintenance' ? 'Đang bảo trì'
                        : ($statusClass === 'last_day'          ? 'Trả phòng hôm nay'
                        : ($statusClass === 'checked_in'        ? 'Đang ở'
                        : ($statusClass === 'booked_pending'    ? 'Đã đặt, chưa check-in'
                        : 'Không xác định'))))
                    }}
                </span>
                                    </td>
                                    <td>
                                        {{ $guest }}
                                        @if($guest_cccd || $guest_phone)
                                            <div style="font-size: 12px; color: #666; line-height: 1.3;">
                                                @if($guest_cccd)
                                                    CCCD: {{ $guest_cccd }}<br>
                                                @endif
                                                @if($guest_phone)
                                                    SĐT: {{ $guest_phone }}
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                    <td>{{ $startDisplay }}</td>
                                    <td>{{ $endDisplay }}</td>
                                    <td>{{ $note }}</td>
                                </tr>
                            @endforeach
                        @endforeach

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script>
    $(function() {
        // Khởi tạo DataTable
        $('#allocation-table').DataTable({
            language: {
                search: "Tìm kiếm:",
                lengthMenu: "Hiển thị _MENU_ dòng",
                info: "Hiển thị _START_ đến _END_ của _TOTAL_ phòng",
                paginate: { previous: "Trước", next: "Sau" }
            }
        });

        // Date navigation
        const dateInput = $('#date'),
            baseUrl   = "{{ route('admin.room.table_view') }}";

        function goTo(date) {
            window.location.href = `${baseUrl}?date=${date}`;
        }

        $('#prevDay').click(() => {
            let d = new Date(dateInput.val());
            d.setDate(d.getDate() - 1);
            goTo(d.toISOString().split('T')[0]);
        });
        $('#nextDay').click(() => {
            let d = new Date(dateInput.val());
            d.setDate(d.getDate() + 1);
            goTo(d.toISOString().split('T')[0]);
        });
        $('#todayBtn').click(() => {
            goTo(new Date().toISOString().split('T')[0]);
        });
        dateInput.change(() => goTo(dateInput.val()));
    });
</script>
</body>
</html>
