<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lịch Đặt Phòng - Hanoi Hotel</title>
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <style>
        /* FULLCALENDAR OVERRIDES */
        #calendar {
            background: #fff !important;
            padding: 16px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important;
            max-width: 1400px !important;
            margin: 0 auto !important;
            font-family: 'Raleway', sans-serif !important;
        }

        .fc-toolbar-title {
            font-size: 20px !important;
            font-weight: 700 !important;
            color: #333 !important;
        }

        .fc-button {
            border: none !important;
            padding: 6px 12px !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            border-radius: 6px !important;
            color: #fff !important;
            background-color: #0C5FCD !important;
        }

        .fc-button:hover {
            background-color: #0949A5 !important;
        }

        .fc-daygrid-day-frame {
            min-height: 100px !important;
            padding: 8px !important;
        }

        .fc-event {
            font-size: 12px !important;
            font-weight: 600 !important;
            padding: 2px 6px !important;
            line-height: 1.4 !important;
            color: #fff !important;
        }

        .fc-daygrid-body {
            padding-right: 8px !important;
        }

        /* MODAL */
        /* Modal Overlay & Animation */
        .modal-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(2px);
            z-index: 999;
            justify-content: center;
            align-items: center;
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to   { opacity: 1; }
        }

        /* Modal Box */
        .modal-content {
            background: #fff;
            border-radius: 16px;
            max-width: 450px;
            width: 90%;
            overflow: hidden;
            box-shadow: 0 8px 24px rgba(0,0,0,0.2);
            animation: slideUp 0.4s ease-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }

        /* Header */
        .modal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #102348;
            color: #fff;
            padding: 16px 20px;
        }
        .modal-header .modal-icon {
            font-size: 24px;
            margin-right: 8px;
        }
        .modal-close-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
        }

        /* Body */
        .modal-body {
            padding: 20px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            row-gap: 12px;
            column-gap: 16px;
        }
        .info-grid .full-row {
            grid-column: 1 / -1;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 13px;
            font-weight: 600;
            text-transform: uppercase;
            background: #0C5FCD;
            color: #fff;
        }

        /* Footer Buttons */
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            padding: 16px 20px;
            background: #f9f9f9;
        }
        .btn-secondary, .btn-primary {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }
        .btn-secondary {
            background: #dc3545;
            color: #fff;
            padding: 6px 12px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-secondary:hover {
            background: #b02a37;
        }
    </style>
</head>
<body>

@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')

    <main>
        <h1 class="title">Lịch Đặt Phòng</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Lịch Booking</a></li>
        </ul>

        <div class="data">
            <div class="content-data" style="width: 100%;">
                <div class="head">
                    <h3>Lịch đặt phòng toàn hệ thống</h3>

                    <div>
                        <label for="roomFilter"><strong>Chọn phòng:</strong></label>
                        <select id="roomFilter" onchange="filterEventsByRoom()" style="padding: 6px 12px; border-radius: 6px;">
                            <option value="all">Tất cả phòng</option>
                            @foreach ($roomNumbers as $room)
                                <option value="{{ $room }}">{{ $room }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div id="calendar"></div>
            </div>
        </div>
    </main>
</section>

<!-- Modal Booking -->
<div class="modal-overlay" id="bookingModal">
    <div class="modal-content">
        <div class="modal-header">
            <i class="ri-information-line modal-icon"></i>
            <span>Thông Tin Booking</span>
            <button class="modal-close-btn" aria-label="Close" onclick="closeModal()">
                <i class="ri-close-line"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <!-- Grid 2 cột + 1 hàng full-row -->
            <div class="info-grid">
                <div><strong>Khách:</strong> <span id="guestName">---</span></div>
                <div><strong>SĐT:</strong> <span id="guestPhone">---</span></div>
                <div><strong>Email:</strong> <span id="guestEmail">---</span></div>
                <div><strong>CCCD:</strong> <span id="guestID">---</span></div>  <!-- <== Thêm CCCD -->
                <div><strong>Phòng:</strong> <span id="roomNumber">---</span></div>
                <div><strong>Check-in:</strong> <span id="startDate">---</span></div>
                <div><strong>Check-out:</strong> <span id="endDate">---</span></div>
                <div class="full-row"><strong>Trạng thái:</strong>
                    <span class="status-badge" id="statusBadge">---</span>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-secondary" onclick="closeModal()">Đóng</button>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/vi.js"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script>
    // Helper
    function closeModal() {
        document.getElementById('bookingModal').style.display = 'none';
    }
    function getUrlParam(key) {
        return new URLSearchParams(window.location.search).get(key);
    }

    function showModalForEvent(ev) {
        const d       = ev.extendedProps;
        const start   = ev.start.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        let   end     = '---';

        if (d.originalEnd) {
            // originalEnd chứa chuỗi "YYYY-MM-DD HH:MM:SS".
            // Muốn hiển thị check-out thực là ngày trước originalEnd
            const tmpDate = new Date(d.originalEnd.split(' ')[0] + 'T00:00:00');
            tmpDate.setDate(tmpDate.getDate());
            end = tmpDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit', year: 'numeric' });
        }

        document.getElementById('guestName').textContent   = d.guest_name   || '---';
        document.getElementById('guestPhone').textContent  = d.guest_phone  || '---';
        document.getElementById('guestEmail').textContent  = d.guest_email  || '---';
        document.getElementById('guestID').textContent     = d.guest_id_number || '---'; // Hiển thị CCCD
        document.getElementById('roomNumber').textContent  = d.room_number || '---';
        document.getElementById('startDate').textContent   = start;
        document.getElementById('endDate').textContent     = end;
        document.getElementById('statusBadge').textContent = (d.status || '').toUpperCase();
        document.getElementById('bookingModal').style.display = 'flex';
    }

    document.addEventListener('DOMContentLoaded', () => {
        const selectedDate = getUrlParam('date') || '{{ $selectedDate }}';
        const rawEvents    = @json($calendarEvents);

        // Thêm originalEnd vào extendedProps để tính ngày check-out chính xác
        const events = rawEvents.map(ev => ({
            ...ev,
            extendedProps: {
                ...ev.extendedProps,
                originalEnd: ev.end
            }
        }));

        // Khởi tạo FullCalendar
        const calendar = new FullCalendar.Calendar(
            document.getElementById('calendar'), {
                locale: 'vi',
                initialView: 'dayGridMonth',
                initialDate: selectedDate,
                height: 'auto',
                dayMaxEventRows: 6,
                headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek,listWeek' },
                events,
                // eventContent: hiển thị số phòng + ngày check-in→check-out
                eventContent: info => {
                    const room = info.event.extendedProps.room_number;

                    const startDate = new Date(info.event.start);
                    const startStr  = startDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });

                    // Ngày kết thúc dựa vào originalEnd, trừ 1 ngày để lấy ngày check-out
                    let endStr = '';
                    if (info.event.extendedProps.originalEnd) {
                        const tmp = new Date(info.event.extendedProps.originalEnd.split(' ')[0] + 'T00:00:00');
                        tmp.setDate(tmp.getDate()); // không -1 vì originalEnd là ngày kết thúc thực tế
                        endStr = tmp.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                    }

                    // Lấy giờ check-in/out thực tế nếu có
                    const actualIn  = info.event.extendedProps.actual_check_in;
                    const actualOut = info.event.extendedProps.actual_check_out;

                    const getTimeFromDateTime = dt => {
                        if (!dt) return null;
                        const parts = dt.split(' ')[1] || '00:00:00';
                        return parts.slice(0, 5); // Lấy HH:mm
                    };

                    const timeIn  = getTimeFromDateTime(actualIn)  || '14:00';
                    const timeOut = getTimeFromDateTime(actualOut) || '12:00';

                    const html = `
      <div style="font-weight:600; font-size:13px; color:#fff; line-height:1.2;">
        ${room}<br>
        <span style="font-size:11px;">
          ${startStr} ${timeIn} → ${endStr} ${timeOut}
        </span>
      </div>
    `;
                    return { html };
                },
                eventClick: info => showModalForEvent(info.event),
                eventTextColor: '#fff'
            }
        );
        calendar.render();

        // Filter phòng
        const roomFilter = document.getElementById('roomFilter');
        const roomParam  = getUrlParam('room') || 'all';
        roomFilter.value = roomParam;

        const applyFilter = r => {
            const evs = r === 'all'
                ? events
                : events.filter(e => e.extendedProps.room_number == r);
            calendar.removeAllEvents();
            calendar.addEventSource(evs);
        };
        applyFilter(roomParam);

        roomFilter.addEventListener('change', () => {
            const r = roomFilter.value;
            const u = new URL(window.location);
            u.searchParams.set('room', r);
            u.searchParams.set('date', selectedDate);
            window.history.replaceState({}, '', u);
            applyFilter(r);
        });

        // Auto-open modal khi truyền room_id + date trên URL
        const roomId    = getUrlParam('room_id');
        const dateParam = getUrlParam('date');
        if (roomId && dateParam) {
            const matchEv = calendar.getEvents().find(ev => {
                if (ev.extendedProps.room_id.toString() !== roomId) return false;
                const start = new Date(ev.start.toISOString().split('T')[0] + 'T00:00:00');
                const rawE  = ev.extendedProps.originalEnd.split(' ')[0];
                const lastDay = new Date(rawE + 'T00:00:00');
                lastDay.setDate(lastDay.getDate() - 1);
                const target = new Date(dateParam + 'T00:00:00');
                return target >= start && target <= lastDay;
            });
            if (matchEv) {
                showModalForEvent(matchEv);
            } else {
                console.warn(`Không tìm thấy booking cho phòng ${roomId} vào ${dateParam}`);
            }
        }

        // ESC để đóng modal
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') closeModal();
        });
    });
</script>
</body>
</html>
