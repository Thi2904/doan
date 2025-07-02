@php use Illuminate\Support\Carbon; @endphp
    <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Phòng - Admin</title>
    <style>
        .room-block {
            position: relative;
            border: 2px solid transparent;
            width: 110px;
            height: 70px;
            margin: 12px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-weight: 700;
            font-size: 16px;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgb(0 0 0 / 0.1);
            transition: transform 0.25s ease, box-shadow 0.25s ease, border-color 0.3s ease;
            background-color: #e9f5e9;
            color: #2e7d32;
            cursor: pointer;
            user-select: none;
        }

        .room-block:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 8px 15px rgb(0 0 0 / 0.2);
            border-color: #4caf50;
            z-index: 10;
        }

        /* Tooltip styling */
        .room-block[title]:hover::after {
            content: attr(title);
            position: absolute;
            bottom: 110%;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(44, 62, 80, 0.85);
            color: #fff;
            padding: 7px 14px;
            border-radius: 6px;
            white-space: nowrap;
            font-size: 14px;
            pointer-events: none;
            opacity: 1;
            transition: opacity 0.3s ease;
            box-shadow: 0 2px 8px rgb(0 0 0 / 0.3);
            z-index: 100;
        }

        /* State colors */
        .room-block.available {
            background-color: #cfd8dc; /* xám hơi xanh, rất dễ nhìn */
            color: #37474f; /* màu chữ xám đậm cho dễ đọc */
            border-color: #90a4ae; /* đường viền xám xanh đậm hơn */
            box-shadow: 0 4px 8px rgba(55, 71, 79, 0.25); /* bóng đổ tông xám */
        }

        .room-block.booked_pending {
            background-color: #a9c9ff;
            color: #0d47a1;
            border-color: #548aff;
            box-shadow: 0 4px 8px rgb(13 71 161 / 0.25);
        }
        .room-block.checked_in {
            background-color: #66bb6a;
            color: #1b5e20;
            border-color: #388e3c;
            box-shadow: 0 4px 10px rgb(27 94 32 / 0.3);
        }
        .room-block.last_day {
            background-color: #ffeb3b;
            color: #f57f17;
            border-color: #fbc02d;
            box-shadow: 0 4px 10px rgb(245 127 23 / 0.3);
        }
        .room-block.under_maintenance {
            background-color: #ff8a65;
            color: #4e342e;
            border-color: #e64a19;
            box-shadow: 0 4px 10px rgb(230 74 25 / 0.3);
        }

        /* Container */
        .room-group {
            margin-bottom: 50px;
        }

        .room-group-title {
            font-weight: 700;
            font-size: 24px;
            margin-bottom: 16px;
            color: #34495e;
            border-bottom: 2px solid #102348;
            padding-bottom: 6px;
        }

        .room-list {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .date-title {
            font-size: 24px;
            font-weight: 700;
            margin: 30px 0 20px;
            color: #2c3e50;
            text-align: center;
            letter-spacing: 1px;
        }

        .legend {
            display: flex;
            gap: 25px;
            margin-top: 30px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 16px;
            color: #555;
            user-select: none;
        }
        .legend-color {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            border: 2px solid transparent;
            box-shadow: 0 0 5px rgb(0 0 0 / 0.1);
        }
        .legend-available {
            background: #cfd8dc;       /* thay từ #d0f0c0 (xanh lá) */
            border-color: #90a4ae;     /* thay từ #81c784 (xanh lá đậm) */
        }
        .legend-booked_pending { background: #a9c9ff; border-color: #548aff; }
        .legend-checked_in { background: #66bb6a; border-color: #388e3c; }
        .legend-last_day { background: #ffeb3b; border-color: #fbc02d; }
        .legend-maintenance { background: #ff8a65; border-color: #e64a19; }

        /* Date input */
        label[for="date"] {
            font-weight: 600;
            margin-right: 12px;
            font-size: 16px;
        }
        input[type="date"] {
            padding: 7px 12px;
            border-radius: 8px;
            border: 1.8px solid #102348;
            font-size: 16px;
            outline-offset: 2px;
            transition: border-color 0.3s ease;
        }
        input[type="date"]:focus {
            border-color: #27ae60;
            box-shadow: 0 0 8px #27ae60aa;
        }
        form {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')

    <main>
        <h1 class="title">Sơ Đồ Quản Lý Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Sơ Đồ Quản Lý Phòng</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách phòng</h3>
                </div>

                <form method="GET" action="">
                    <div style="display: flex; align-items: center; flex-wrap: wrap; gap: 12px;">

                    {{-- Nhóm các nút bên trái --}}
                        <div style="display: flex; align-items: center; flex-wrap: wrap;">
                            <button type="button" id="prevDay" class="btn-date-nav"
                                    style="padding: 10px 14px; gap: 2px; border-radius: 8px; background-color: #2c3e50; color: white; border: none; font-weight: 600;">
                                <i class="ri-arrow-left-s-line"></i>
                            </button>

                            {{-- Input ngày ở bên phải --}}
                            <div>
                                <input type="date" id="date" name="date"
                                       value="{{ request('date', now()->format('Y-m-d')) }}"
                                       style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 8px;">
                            </div>

                            <button type="button" id="nextDay" class="btn-date-nav"
                                    style="padding: 10px 14px; border-radius: 8px; background-color: #2c3e50; color: white; border: none; font-weight: 600;">
                                <i class="ri-arrow-right-s-line"></i>
                            </button>

                            <button type="button" id="todayBtn" class="btn-date-nav"
                                    style="padding: 10px 14px; margin-left: 10px; border-radius: 8px; background-color: #7f8c8d; color: white; border: none; font-weight: 600;">
                                Hôm nay
                            </button>
                        </div>

                    </div>
                </form>

            @foreach ($roomTypes as $roomType)
                    <div class="room-group">
                        <h2 class="room-group-title">{{ $roomType->name }}</h2>
                        <div class="room-list">
                            @foreach ($roomType->rooms as $room)
                                @php
                                    $selected = Carbon::parse(request('date', now()))->startOfDay();
                                    $statusClass = 'available';
                                    $tooltip = "Phòng {$room->room_number} - Trống";
                                    // under maintenance
                                    if ($room->room_status === 'under_maintenance') {
                                        $statusClass = 'under_maintenance';
                                        $tooltip = "Phòng {$room->room_number} - Đang bảo trì";
                                    } else {
                                        foreach ($room->allocations as $a) {
                                            $start = Carbon::parse($a->start_at)->startOfDay();
                                            $end   = Carbon::parse($a->end_at)->startOfDay();
                                            if ($selected->between($start, $end)) {
                                                if ($selected->isSameDay($end)) {
                                                    $statusClass = 'last_day';
                                                    $tooltip = "Phòng {$room->room_number} - Trả phòng hôm nay";
                                                } else {
                                                    $statusClass = $a->booking->actual_check_in ? 'checked_in' : 'booked_pending';
                                                    $tooltip = $statusClass === 'checked_in'
                                                        ? "Phòng {$room->room_number} - Đang ở"
                                                        : "Phòng {$room->room_number} - Đã đặt, chưa check-in";
                                                }
                                                break;
                                            }
                                        }
                                    }
                                    // clickable only when booked_pending, checked_in, last_day
                                    $clickable = in_array($statusClass, ['booked_pending','checked_in','last_day']);
                                @endphp
                                <div class="room-block {{ $statusClass }} {{ $clickable ? 'clickable' : 'not-clickable' }}"
                                     title="{{ $tooltip }}"
                                     @if($clickable) onclick="viewRoomDetail({{ $room->id }})" @endif>
                                    {{ $room->room_number }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="legend">
                    <div class="legend-item"><div class="legend-color legend-available"></div>Phòng trống</div>
                    <div class="legend-item"><div class="legend-color legend-booked_pending"></div>Đã book, chưa check-in</div>
                    <div class="legend-item"><div class="legend-color legend-checked_in"></div>Đang ở</div>
                    <div class="legend-item"><div class="legend-color legend-last_day"></div>Ngày trả phòng</div>
                    <div class="legend-item"><div class="legend-color legend-maintenance"></div>Đang bảo trì</div>
                </div>
            </div>
        </div>
    </main>
</section>
<script src="{{ asset('js/script.js') }}"></script>
<script>
    document.getElementById('date').addEventListener('change', function () {
        const selectedDate = this.value;
        const baseUrl = "{{ route('admin.room.matrix') }}";
        window.location.href = `${baseUrl}?date=${selectedDate}`;
    });

    const calendarUrl = "{{ route('admin.booking.calendar') }}";

    // Ngày đang hiển thị trong matrix, lấy từ input#date
    function getSelectedDate() {
        return document.getElementById('date').value;
    }

    function viewRoomDetail(roomId) {
        const date = getSelectedDate();
        // Chuyển hướng sang calendar, truyền date + room_id
        window.location.href = `${calendarUrl}?date=${date}&room_id=${roomId}`;
    }

        const baseUrl = "{{ route('admin.room.matrix') }}";

        function goToDate(dateStr) {
        window.location.href = `${baseUrl}?date=${dateStr}`;
    }

        document.getElementById('prevDay').addEventListener('click', () => {
        const date = new Date(document.getElementById('date').value);
        date.setDate(date.getDate() - 1);
        goToDate(date.toISOString().split('T')[0]);
    });

        document.getElementById('nextDay').addEventListener('click', () => {
        const date = new Date(document.getElementById('date').value);
        date.setDate(date.getDate() + 1);
        goToDate(date.toISOString().split('T')[0]);
    });

        document.getElementById('todayBtn').addEventListener('click', () => {
        const today = new Date().toISOString().split('T')[0];
        goToDate(today);
    });
</script>
</body>
</html>
