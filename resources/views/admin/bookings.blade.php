<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/modal-booking.css') }}" />
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Đặt Phòng - Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<style>
    .badge {
        display: inline-block;
        padding: 0.25em 0.5em;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        color: #fff;
        font-weight: 500;
    }
    .badge-pending    { background-color: #ffc107; } /* vàng */
    .badge-confirmed  { background-color: #17a2b8; } /* xanh dương nhạt */
    .badge-checkedin  { background-color: #007bff; } /* xanh dương */
    .badge-checkedout { background-color: #6c757d; } /* xám */
    .badge-completed  { background-color: #28a745; } /* xanh lá */
    .badge-cancelled  { background-color: #dc3545; } /* đỏ */
    .badge-default    { background-color: #adb5bd; } /* xám nhạt */
</style>
<body>
@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Đặt Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Đặt Phòng</a></li>
        </ul>
        <div class="data">
            <div class="content-data">
                <div class="header-container">
                    <h3 class="booking-title">Danh sách Đặt phòng</h3>
                    <div class="booking-date" id="currentDate"></div>
                </div>
                @php
                    $statusCounts = [
                        'pending' => $statusCounts['pending'] ?? 0,
                        'confirmed' => $statusCounts['confirmed'] ?? 0,
                        'checked_in' => $statusCounts['checked_in'] ?? 0,
                        'completed' => $statusCounts['completed'] ?? 0,
                        'cancelled' => $statusCounts['cancelled'] ?? 0,
                    ];
                @endphp

                <ul class="nav nav-tabs mb-3" id="statusTabs">
                    <li class="nav-item">
                        <a class="nav-link active" data-status="" href="#">
                            Tất cả <span class="count-badge">{{ $totalCount }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="pending" href="#">
                            Chờ duyệt <span class="count-badge">{{ $statusCounts['pending'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="confirmed" href="#">
                            Đã xác nhận <span class="count-badge">{{ $statusCounts['confirmed'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="checked_in" href="#">
                            Đã nhận phòng <span class="count-badge">{{ $statusCounts['checked_in'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="completed" href="#">
                            Đã trả phòng <span class="count-badge">{{ $statusCounts['completed'] }}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-status="cancelled" href="#">
                            Đã hủy <span class="count-badge">{{ $statusCounts['cancelled'] }}</span>
                        </a>
                    </li>
                </ul>

                <table id="bookingsTable" class="display" style="width:100%">
                    <thead>
                    <tr>
                        <th></th>
                        <th>Mã</th>
                        <th>Khách hàng</th>
                        <th>Số điện thoại</th>
                        <th>Thời gian</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bookings as $b)
                        <tr data-status="{{ $b->status }}" data-payments='@json($b->payments)'>
                            <td class="details-control"><i class="ri-arrow-down-s-line"></i></td>
                            <td>{{ $b->booking_code }}</td>
                            <td>{{ $b->guest_name }}<br><small>{{ $b->guest_email }}</small></td>
                            <td>{{ preg_replace('/(\d{4})(\d{3})(\d+)/', '$1 $2 $3', $b->guest_phone) }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($b->check_in)->format('d/m/Y') }}
                                &rarr;
                                {{ \Carbon\Carbon::parse($b->check_out)->format('d/m/Y') }}
                            </td>
                            <td>{{ number_format($b->total_price,0,',','.') }} VNĐ</td>
                            <td>{!! $b->status_badge_html !!}</td>
                            <td>{{ \Carbon\Carbon::parse($b->created_at)->format('d/m/Y H:i') }}</td>
                            <td>
                                <button class="btn-action view" data-id="{{ $b->id }}">
                                    <i class="ri-eye-line"></i>
                                </button>

                                @if(in_array($b->status, ['pending', 'confirmed']))
                                    <button class="btn-action room-type-btn"
                                            data-rooms='@json($b->bookingRooms)'>
                                        <i class="ri-hotel-bed-line"></i>
                                    </button>
                                @endif

                                @if ($b->status === 'checked_in')
                                    <button class="btn-action surcharge-btn"
                                            data-booking-id="{{ $b->id }}"
                                            data-booking='@json($b)'
                                            data-additional-fees='@json($additionalFees)'>
                                        <i class="ri-money-dollar-circle-line"></i>
                                    </button>
                                @endif

                                @if ($b->status === 'completed')
                                    <a href="{{ route('bookings.invoice.page', $b->id) }}" class="btn-action" title="Xem hóa đơn" target="_blank">
                                        <i class="ri-file-text-line"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</section>

<div id="bookingModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <header class="modal-header">
            <div>
                <h3>Chi tiết Đặt phòng</h3>
                <small class="booking-code">Mã: <span id="bBookingCode"></span></small>
            </div>
            <button id="bookingModalCloseBtn" class="modal-close" aria-label="Đóng">&times;</button>
        </header>

        <div class="modal-body">
            <!-- Thời gian đặt phòng -->
            <section class="schedule-info-group">
                <h4>Lịch trình</h4>
                <div class="schedule-info-grid">
                    <div>
                        <strong style="margin-right: 2px;">Dự kiến:</strong>
                        <span id="bCheckIn"></span>
                        <span class="arrow">→</span>
                        <span id="bCheckOut"></span>
                    </div>
                    <div>
                        <strong style="margin-right: 2px;">Thực tế:</strong>
                        <span id="bActualCheckIn">—</span>
                        <span class="arrow">→</span>
                        <span id="bActualCheckOut">—</span>
                    </div>
                </div>
            </section>

            <!-- Thông tin khách -->
            <section class="info-group">
                <h4>Thông tin khách</h4>
                <div class="info-grid">
                    <div><strong>Tên:</strong> <span id="bGuestName"></span></div>
                    <div><strong>Email:</strong> <span id="bGuestEmail"></span></div>
                    <div><strong>Điện thoại:</strong> <span id="bGuestPhone"></span></div>
                    <div><strong>CCCD:</strong> <span id="bGuestIDNumber"></span></div>
                </div>
            </section>

            <!-- Trạng thái & Tổng tiền -->
            <section class="info-group">
                <h4>Trạng thái & Thanh toán</h4>
                <div class="info-grid">
                    <div><strong>Trạng thái:</strong> <span id="bStatus"></span></div>
                    <div class="cancel-reason"><strong>Lý do hủy:</strong> <span id="bCancelReason"></span></div>
                    <div><strong>Tổng tiền:</strong> <span id="bTotal"></span></div>
                    <div class="status-update">
                        <label for="modalStatusSelect"><strong>Cập nhật trạng thái:</strong></label>
                        <select id="modalStatusSelect" style="margin-left: 10px; padding: 4px;">
                            <option value="pending">Chờ xử lý</option>
                            <option value="confirmed">Đã duyệt</option>
                            <option value="checked_in">Đã nhận phòng</option>
                            <option value="completed">Đã trả phòng</option>
                            <option value="cancelled">Đã hủy</option>
                        </select>
                    </div>
                </div>
            </section>

            <!-- Phòng đã đặt & Cấp phát -->
            <section class="info-group">
                <h4>Phòng đã đặt</h4>
                <div id="bRooms" class="cards-grid"></div>
            </section>

            <!-- Thanh toán chi tiết -->
            <section class="info-group">
                <h4>Chi tiết thanh toán</h4>
                <div class="cards-grid payments">
                    <div>
                        <h5>Đã thanh toán</h5>
                        <div id="bPaidPayments"></div>
                    </div>
                    <div>
                        <h5>Hoàn tiền</h5>
                        <div id="bRefundPayments"></div>
                    </div>
                    <div>
                        <h5>Chưa thanh toán</h5>
                        <div id="bPendingPayments"></div>
                    </div>
                </div>
            </section>

            <section class="info-group">
                <h4>Phòng đã cấp phát</h4>
                <div id="bAllocations" class="cards-grid" style="margin-top: 10px;"></div>
            </section>
        </div>

        <footer class="modal-footer">
            <button id="bookingEditBtn" class="btn btn-warning">Chỉnh sửa</button>
            <button id="bookingSaveBtn" class="btn btn-success" style="display:none;">Lưu</button>
            <button id="bookingCancelBtn" class="btn btn-danger" style="display:none;">Hủy</button>
            <button id="bookingModalCloseBtnFooter" class="btn btn-danger">Đóng</button>
        </footer>
    </div>
</div>

<div id="surchargeModal" class="modal">
    <div class="modal-content">
        <h2>Chỉnh sửa phụ phí từng phòng</h2>

        <div class="modal-body" id="surchargeModalBody">
            {{-- Nội dung sẽ được inject bằng JS dựa vào booking ID --}}
        </div>

        <div class="modal-footer">
            <button type="button" id="saveSurchargeBtn">Lưu</button>
            <button type="button" id="closeSurchargeModal">Đóng</button>
        </div>
    </div>
</div>

<div id="roomTypeModal" class="modal-overlay">
    <div class="modal-box">
        <header class="modal-header">
            <div>
                <h3>Loại Phòng Đã Đặt</h3>
            </div>
            <button id="roomTypeModalCloseBtn" class="modal-close" aria-label="Đóng">&times;</button>
        </header>

        <div class="modal-body" id="roomTypeModalBody">
            <!-- Nội dung loại phòng sẽ được hiển thị ở đây -->
        </div>

        <footer class="modal-footer">
            <button id="roomTypeModalCloseBtnFooter" class="btn btn-danger">Đóng</button>
        </footer>
    </div>
</div>

<div id="changeRoomTypeModal" class="modal-overlay">
    <div class="modal-box">
        <header class="modal-header">
            <div>
                <h3>Thay Đổi Loại Phòng</h3>
            </div>
            <button id="changeRoomTypeModalCloseBtn" class="modal-close" aria-label="Đóng">&times;</button>
        </header>

        <div class="modal-body" id="changeRoomTypeModalBody">
            <!-- Nội dung các loại phòng sẽ được hiển thị ở đây -->
        </div>

        <footer class="modal-footer">
            <button id="saveNewRoomTypeBtn" class="btn btn-success">Lưu thay đổi</button>
            <button id="changeRoomTypeModalCloseBtnFooter" class="btn btn-danger">Đóng</button>
        </footer>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const dateEl = document.getElementById('currentDate');
        const today = new Date();
        const formatted = today.toLocaleDateString('vi-VN', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });

        dateEl.innerHTML = `Ngày hôm nay: <span class="date-box">${formatted.charAt(0).toUpperCase() + formatted.slice(1)}</span>`;
    });
</script>
<script>
    $(function() {
        // Khởi tạo DataTable
        const table = $('#bookingsTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json'
            }
        });

        $('#statusTabs .nav-link').on('click', function(e) {
            e.preventDefault();
            // active tab
            $('#statusTabs .nav-link').removeClass('active');
            $(this).addClass('active');

            const status = $(this).data('status') || '';

            // thêm hàm lọc
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                const row = table.row(dataIndex).node();
                const rowStatus = $(row).data('status') || '';
                return !status || rowStatus === status;
            });

            table.draw();
            // xóa filter sau khi vẽ để tránh chồng lấn
            $.fn.dataTable.ext.search.pop();
        });

        const statusOptions = ['pending', 'paid', 'failed'];
        const statusTextMap = {
            pending: 'Chờ xử lý',
            paid: 'Đã thanh toán',
            failed: 'Thất bại',
        };

// Thêm mapping cho entry_type
        const entryTypeTextMap = {
            deposit: 'Cọc',
            payment: 'Thanh toán',
            adjustment: 'Điều chỉnh',
            charge: 'Phụ phí',
            refund: 'Hoàn tiền'
        };

        const makeStatusSelect = (paymentId, currentStatus) => {
            const isDisabled = currentStatus === 'paid' ? 'disabled' : '';
            let html = `<select class="payment-status-select" data-id="${paymentId}" ${isDisabled}>`;
            statusOptions.forEach(st => {
                const selected = st === currentStatus ? 'selected' : '';
                html += `<option value="${st}" ${selected}>${statusTextMap[st]}</option>`;
            });
            html += `</select>`;
            return html;
        };

        $('#bookingsTable tbody').on('click', 'td.details-control', function () {
            const tr = $(this).closest('tr');
            const row = table.row(tr);
            const icon = tr.find('td.details-control i');

            if (row.child.isShown()) {
                row.child.hide();
                tr.removeClass('shown');
                icon.removeClass('ri-arrow-up-s-line').addClass('ri-arrow-down-s-line');
            } else {
                const pays = JSON.parse(tr.attr('data-payments'));
                let html = `
        <table class="child-table">
            <thead>
                <tr>
                    <th>Loại</th>
                    <th>Số tiền</th>
                    <th>Mô tả</th>
                    <th>Trạng thái</th>
                </tr>
            </thead>
            <tbody>
        `;

                pays.forEach(p => {
                    html += `
            <tr>
                <td>${entryTypeTextMap[p.entry_type] ?? p.entry_type}</td>
                <td>${new Intl.NumberFormat('vi-VN').format(p.amount)} VNĐ</td>
                <td>${p.description.charAt(0).toUpperCase() + p.description.slice(1)}</td>
                <td>${makeStatusSelect(p.id, p.status)}</td>
            </tr>
            `;
                });

                html += `</tbody></table>`;
                row.child(html).show();
                tr.addClass('shown');
                icon.removeClass('ri-arrow-down-s-line').addClass('ri-arrow-up-s-line');
            }
        });

        // Sự kiện thay đổi trạng thái cho payments (giữ nguyên)
        $(document).on('change', '.payment-status-select', function () {
            const select = $(this);
            const paymentId = select.data('id');
            const newStatus = select.val();

            $.ajax({
                url: `/admin/payments/${paymentId}/status`,
                method: 'PUT',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: newStatus
                },
                success: function () {
                    Swal.fire('Thành công', 'Trạng thái đã được cập nhật', 'success');
                },
                error: function () {
                    Swal.fire('Lỗi', 'Không thể cập nhật trạng thái', 'error');
                    select.val(select.data('original')); // quay lại trạng thái cũ nếu lỗi
                }
            });
        });

        let currentBookingId = null;
        let currentBookingStatus = null;
        let originalBookingData = {};

        // Utility
        const formatNumber   = n => new Intl.NumberFormat('vi-VN').format(n) + ' VNĐ';
        const formatDate = d => new Date(d).toLocaleDateString('vi-VN');
        const formatDateTime = dt => dt ? new Date(dt).toLocaleString('vi-VN',{hour12:false}) : '—';
        const statusText     = s => ({
            pending:    'Chờ xử lý',
            confirmed:  'Đã duyệt',
            checked_in: 'Đã nhận phòng',
            completed:  'Đã trả phòng',
            cancelled:  'Đã hủy'
        }[s]||s);

        // Render helpers (giữ nguyên)
        function renderRoomDetails(rooms) {
            const c = $('#bRooms').empty();
            rooms.forEach(r=>{
                const fees = r.surcharges?.length
                    ? r.surcharges.map(f=>`<li>${f.name} - ${formatNumber(f.pivot.price)}</li>`).join('')
                    : '<li>Không có phụ phí</li>';
                c.append(`
              <div class="card">
                <div class="room-header"><span class="badge">${r.room_type?.name||'?'}</span></div>
                <div class="room-content two-columns">
                  <ul>
                    <li>Người lớn: ${r.adults}</li>
                    <li>Trẻ em: ${r.children}</li>
                    <li>Giá/đêm: ${formatNumber(r.price_per_night)}</li>
                    <li>Số đêm: ${r.nights}</li>
                    <li>Tạm tính: ${formatNumber(r.sub_total)}</li>
                  </ul>
                  <ul>${fees}</ul>
                </div>
              </div>
            `);
            });
        }

        function renderRoomAllocations(allocs) {
            const c = $('#bAllocations').empty();
            if (!allocs.length) {
                c.append('<div class="card"><p>Chưa có phòng cấp phát.</p></div>');
                return;
            }
            for (const a of allocs) {
                if (a.room) {
                    const dateOnly = new Date(a.start_at).toLocaleDateString('vi-VN');
                    c.append(`
              <div class="card">
                <div class="room-header">
                  <span class="badge">${a.room.room_type?.name || '?'}</span>
                </div>
                <div class="room-content">
                  <p><strong>Phòng số:</strong> ${a.room.room_number}</p>
                  <p><strong>Ngày cấp phát:</strong> ${dateOnly}</p>
                </div>
              </div>
            `);
                }
            }
        }

        function renderPaymentDetails(payments) {
            $('#bPaidPayments, #bPendingPayments, #bRefundPayments').empty();
            payments.forEach(p=>{
                const container = p.entry_type==='refund'
                    ? '#bRefundPayments'
                    : (p.status==='paid' ? '#bPaidPayments' : '#bPendingPayments');
                const label = p.entry_type==='refund'
                    ? 'Hoàn tiền:' : (p.status==='paid'?'Đã thanh toán:':'Chưa trả:');
                $(container).append(`
              <div class="card">
                <p><strong>${label}</strong> ${formatNumber(p.amount)}</p>
                <p><small>${p.description||''}</small></p>
                <p><small>${formatDateTime(p.paid_at||p.created_at)}</small></p>
              </div>
            `);
            });
        }

        // View Booking detail (giữ nguyên)
        $(document).on('click','.btn-action.view',function(){
            currentBookingId = $(this).data('id');
            $.getJSON(`/admin/bookings/${currentBookingId}`, booking => {
                currentBookingStatus = booking.status;
                originalBookingData = booking;
                $('#bBookingCode').text(booking.booking_code);
                $('#bCheckIn').text(formatDate(booking.check_in));
                $('#bCheckOut').text(formatDate(booking.check_out));
                $('#bActualCheckIn').text(formatDateTime(booking.actual_check_in));
                $('#bActualCheckOut').text(formatDateTime(booking.actual_check_out));
                $('#bGuestName').text(booking.guest_name);
                $('#bGuestEmail').text(booking.guest_email);
                $('#bGuestPhone').text(booking.guest_phone);
                $('#bStatus').text(statusText(booking.status));
                $('#bTotal').text(formatNumber(booking.total_price));
                $('#bGuestIDNumber').text(booking.guest_id_number || '—');

                $('#modalStatusSelect').prop('disabled', false).val(booking.status);
                if (booking.status === 'cancelled') {
                    $('#bCancelReason').show().text(booking.cancel_reason || '—');
                } else {
                    $('#bCancelReason').hide();
                }

                if (['pending','confirmed'].includes(booking.status)) {
                    $('#bookingEditBtn').show();
                } else {
                    $('#bookingEditBtn').hide();
                }

                renderRoomDetails(booking.booking_rooms);
                renderRoomAllocations(booking.allocations);
                renderPaymentDetails(booking.payments);

                $('#bookingModalOverlay').addClass('active');
            }).fail(() => Swal.fire('Lỗi','Không tải được dữ liệu','error'));
        });

        // Close modal (giữ nguyên)
        $('#bookingModalOverlay, #bookingModalCloseBtn, #bookingModalCloseBtnFooter')
            .on('click', e => {
                if (['bookingModalOverlay','bookingModalCloseBtn','bookingModalCloseBtnFooter']
                    .includes(e.target.id)) {
                    $('#bookingModalOverlay').removeClass('active');
                }
            });

        // Edit mode (giữ nguyên)
        $('#bookingEditBtn').on('click', function () {
            if (!['pending', 'confirmed'].includes(currentBookingStatus)) {
                Swal.fire('Không thể chỉnh sửa', 'Chỉ khi Chờ xử lý hoặc Đã duyệt', 'warning');
                return;
            }

            $(this).hide();
            $('#bookingSaveBtn, #bookingCancelBtn').show();
            $('#modalStatusSelect').prop('disabled', true);

            // Lấy ngày hôm nay theo local timezone
            const today = new Date();
            const yyyy = today.getFullYear();
            const mm = String(today.getMonth() + 1).padStart(2, '0');
            const dd = String(today.getDate()).padStart(2, '0');
            const todayStr = `${yyyy}-${mm}-${dd}`;

            [
                { sel: '#bGuestName',  type: 'text',  v: originalBookingData.guest_name },
                { sel: '#bGuestEmail', type: 'email', v: originalBookingData.guest_email },
                { sel: '#bGuestPhone', type: 'text',  v: originalBookingData.guest_phone },
                { sel: '#bCheckIn',    type: 'date',  v: formatDate(originalBookingData.check_in), min: todayStr },
                { sel: '#bCheckOut',   type: 'date',  v: formatDate(originalBookingData.check_out), min: todayStr }
            ].forEach(f => {
                const id = f.sel.slice(1);
                const minAttr = f.min ? `min="${f.min}"` : '';
                $(f.sel).replaceWith(`
            <input type="${f.type}" id="${id}" value="${f.v}" ${minAttr} required />
        `);
            });
        });


        // Save changes (giữ nguyên)
        $('#bookingSaveBtn').on('click', () => {
            const checkIn = $('#bCheckIn').val();
            const checkOut = $('#bCheckOut').val();

            if (checkIn > checkOut) {
                Swal.fire('Lỗi', 'Ngày nhận phòng không được sau ngày trả phòng.', 'error');
                return;
            }

            const diffDays = (checkOut - checkIn) / (1000 * 60 * 60 * 24);
            if (diffDays < 1) {
                return Swal.fire('Ngày trả không hợp lệ', 'Ngày trả phòng phải sau ngày nhận phòng ít nhất 1 ngày.', 'error');
            }

            const payload = {
                _token: '{{ csrf_token() }}',
                guest_name: $('#bGuestName').val(),
                guest_email: $('#bGuestEmail').val(),
                guest_phone: $('#bGuestPhone').val(),
                check_in: checkIn,
                check_out: checkOut
            };

            $.ajax({
                url: `/admin/bookings/${currentBookingId}`,
                method: 'PUT',
                data: payload,
                success: () => Swal.fire('Thành công', 'Lưu thành công', 'success').then(() => location.reload()),
                error: function (xhr) {
                    const message = xhr.responseJSON?.message || 'Không lưu được';
                    Swal.fire('Lỗi', message, 'error');
                }
            });
        });

        // Cancel edit (giữ nguyên)
        $('#bookingCancelBtn').on('click', () => {
            $('#bookingSaveBtn,#bookingCancelBtn').hide();
            $('#bookingEditBtn').show();
            [
                {sel:'#bGuestName',  v: originalBookingData.guest_name},
                {sel:'#bGuestEmail', v: originalBookingData.guest_email},
                {sel:'#bGuestPhone', v: originalBookingData.guest_phone},
                {sel:'#bCheckIn',    v: formatDate(originalBookingData.check_in)},
                {sel:'#bCheckOut',   v: formatDate(originalBookingData.check_out)}
            ].forEach(f => $(f.sel).replaceWith(`<span id="${f.sel.slice(1)}">${f.v}</span>`));
            $('#modalStatusSelect').prop('disabled', false);
        });

        // Cập nhật trạng thái booking qua select
        $('#modalStatusSelect').on('change', function () {
            if ($(this).prop('disabled')) return;
            const ns = $(this).val();
            if (ns === currentBookingStatus) return;

            const sendStatus = (extra = {}) => {
                const payload = Object.assign({
                    _token: '{{ csrf_token() }}',
                    status: ns
                }, extra);

                $.ajax({
                    url: `/admin/bookings/${currentBookingId}/status`,
                    method: 'PUT',
                    data: payload,
                    success: () => {
                        Swal.fire('Thành công', 'Cập nhật thành công', 'success')
                            .then(() => location.reload());
                    },
                    error: (xhr) => {
                        console.error(xhr.responseText);
                        Swal.fire('Lỗi', 'Không cập nhật được', 'error');
                        $('#modalStatusSelect').val(currentBookingStatus);
                    }
                });
            };

            const toDatetimeLocalString = (utcStr) => {
                if (!utcStr) return '';
                const utcDate = new Date(utcStr);
                const offsetDate = new Date(utcDate.getTime() + 7 * 60 * 60 * 1000);
                return offsetDate.toISOString().slice(0, 16);
            };

            if (ns === 'cancelled') {
                Swal.fire({
                    title: 'Xác nhận hủy',
                    input: 'text',
                    inputLabel: 'Lý do hủy',
                    inputPlaceholder: 'Nhập lý do...',
                    showCancelButton: true,
                    confirmButtonText: 'Xác nhận',
                    cancelButtonText: 'Hủy',
                    inputValidator: v => !v && 'Vui lòng nhập lý do!'
                }).then(r => {
                    if (r.isConfirmed) sendStatus({ cancel_reason: r.value });
                    else $('#modalStatusSelect').val(currentBookingStatus);
                });
            } else if (ns === 'checked_in') {
                Swal.fire({
                    title: 'Nhập thông tin nhận phòng',
                    html: `
                <input type="datetime-local" id="swal-act-in" class="swal2-input"
                    value="${toDatetimeLocalString(originalBookingData.actual_check_in || originalBookingData.check_in)}"
                    placeholder="Thời gian nhận phòng thực tế">
                <input type="text" id="swal-id-number" class="swal2-input"
                    placeholder="Số CCCD"
                    value="${originalBookingData.guest_id_number || ''}">
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Lưu',
                    cancelButtonText: 'Hủy',
                    preConfirm: () => {
                        const dt = $('#swal-act-in').val();
                        const idNum = $('#swal-id-number').val().trim();

                        if (!dt) {
                            Swal.showValidationMessage('Vui lòng chọn ngày và giờ nhận phòng');
                            return false;
                        }
                        if (!idNum) {
                            Swal.showValidationMessage('Vui lòng nhập số CCCD');
                            return false;
                        }

                        return {
                            actual_check_in: dt + ':00',
                            guest_id_number: idNum
                        };
                    }
                }).then(r => {
                    if (r.isConfirmed && r.value) {
                        sendStatus(r.value);
                    } else {
                        $('#modalStatusSelect').val(currentBookingStatus);
                    }
                });
            } else if (ns === 'completed') {
                Swal.fire({
                    title: 'Nhập thời gian trả phòng thực tế',
                    html: `
                <input type="datetime-local" id="swal-act-out" class="swal2-input"
                    value="${toDatetimeLocalString(originalBookingData.actual_check_out || originalBookingData.check_out)}">
            `,
                    showCancelButton: true,
                    confirmButtonText: 'Lưu',
                    cancelButtonText: 'Hủy',
                    preConfirm: () => {
                        const dt = $('#swal-act-out').val();
                        if (!dt) {
                            Swal.showValidationMessage('Vui lòng chọn ngày và giờ');
                            return false;
                        }
                        return { actual_check_out: dt + ':00' };
                    }
                }).then(r => {
                    if (r.isConfirmed && r.value) sendStatus(r.value);
                    else $('#modalStatusSelect').val(currentBookingStatus);
                });
            } else {
                sendStatus();
            }
        });

    });

    $(document).ready(function () {
        // Khi click nút surcharge, mở modal và tạo html phụ phí
        $('.surcharge-btn').on('click', function () {
            const bookingStr = $(this).attr('data-booking');
            const additionalFeesStr = $(this).attr('data-additional-fees');

            const booking = JSON.parse(bookingStr);
            const additionalFees = JSON.parse(additionalFeesStr);

            if (!booking.booking_rooms || !Array.isArray(booking.booking_rooms)) {
                console.error('booking.booking_rooms không tồn tại hoặc không phải mảng');
                return;
            }

            let html = '';

            booking.booking_rooms.forEach(room => {
                html += `
<div class="room-fees">
    <h4>Phòng: ${room.room_type.name}</h4>
    <input type="text"
           class="fee-search"
           placeholder="Tìm phụ phí..."
           data-room-id="${room.id}" />`;

                ['pre_fee', 'post_fee'].forEach(type => {
                    const label = type === 'pre_fee' ? 'Phí trước' : 'Phí sau';
                    const feesOfType = additionalFees[type] || [];
                    html += `
    <div class="fee-section">
        <h5>${label}</h5>
        <div class="fee-list" data-room-id="${room.id}">`;

                    feesOfType.forEach(fee => {
                        let isChecked = false;
                        if (room.surcharges && Array.isArray(room.surcharges)) {
                            isChecked = room.surcharges.some(surcharge => Number(surcharge.id) === Number(fee.id));
                        }
                        html += `
            <label>
                <input type="checkbox"
                       name="fees[${room.id}][]"
                       value="${fee.id}"
                       ${isChecked ? 'checked' : ''} />
                ${fee.name} (${parseFloat(fee.default_price).toLocaleString()} đ)
            </label>`;
                    });

                    html += `
        </div>
    </div>`;
                });

                html += `
</div>`;
            });

            $('#surchargeModalBody').html(html);
            $('#surchargeModal').data('booking-id', booking.id);
            $('#surchargeModal').addClass('active');

            // Sự kiện tìm kiếm phụ phí theo phòng
            $('.fee-search').on('input', function () {
                const kw = $(this).val().toLowerCase();
                const rid = $(this).data('room-id');
                $(`.fee-list[data-room-id="${rid}"] label`).each(function () {
                    $(this).toggle($(this).text().toLowerCase().includes(kw));
                });
            });
        });

        // Đóng modal
        $(document).on('click', '#closeSurchargeModal', function () {
            $('#surchargeModal').removeClass('active');
        });

        // Lưu dữ liệu qua AJAX
        $(document).on('click', '#saveSurchargeBtn', function () {
            const bookingId = $('#surchargeModal').data('booking-id');
            const fees = {};

            // Duyệt từng phòng để thu thập checkbox đã check (hoặc mảng rỗng nếu không có)
            $('#surchargeModal .room-fees').each(function () {
                const roomId = $(this).find('.fee-search').data('room-id');
                fees[roomId] = [];

                // Lấy tất cả checkbox được tick trong phòng đó
                $(this).find('input[type="checkbox"]:checked').each(function () {
                    fees[roomId].push(parseInt($(this).val()));
                });
            });

            $.ajax({
                url: `/admin/bookings/${bookingId}/fees`,
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: { fees: fees },
                success: function (res) {
                    Swal.fire('Thành công', res.message, 'success');
                    $('#surchargeModal').removeClass('active');
                },
                error: function (xhr) {
                    let errMsg = 'Không thể lưu phụ phí';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Lỗi', errMsg, 'error');
                }
            });
        });
    });

    // Hiển thị modal loại phòng
    $(document).on('click', '.room-type-btn', function () {
        const rooms = $(this).data('rooms');
        const container = $('#roomTypeModalBody').empty();

        if (!rooms.length) {
            container.html('<p>Không có phòng được đặt.</p>');
        } else {
            rooms.forEach(room => {
                container.append(`
                <div class="card" style="margin-bottom: 10px;">
                    <div class="room-header">
                        <span class="badge">${room.room_type?.name || '—'}</span>
                    </div>
                    <div class="room-content">
                        <p><strong>Người lớn:</strong> ${room.adults}</p>
                        <p><strong>Trẻ em:</strong> ${room.children}</p>
                        <p><strong>Giá/đêm:</strong> ${new Intl.NumberFormat('vi-VN').format(room.price_per_night)} VNĐ</p>
                        <p><strong>Số đêm:</strong> ${room.nights}</p>
                        <p><strong>Tạm tính:</strong> ${new Intl.NumberFormat('vi-VN').format(room.sub_total)} VNĐ</p>
                        <button class="btn btn-warning change-room-type"
                            data-booking-room-id="${room.id}"
                            data-room-type-id="${room.room_type_id}"
                            data-check-in="${room.check_in}"
                            data-check-out="${room.check_out}">
                            Thay đổi loại phòng
                        </button>
                    </div>
                </div>
            `);
            });
        }

        $('#roomTypeModal').fadeIn();
    });

    // Đóng modal loại phòng
    $('#roomTypeModalCloseBtn, #roomTypeModalCloseBtnFooter').on('click', function () {
        $('#roomTypeModal').fadeOut();
    });

    // Hiển thị form đổi loại phòng (toàn thời gian)
    $(document).on('click', '.change-room-type', function () {
        const bookingRoomId = $(this).data('booking-room-id');
        const currentRoomTypeId = $(this).data('room-type-id');
        const checkInDate = $(this).data('check-in');
        const checkOutDate = $(this).data('check-out');

        $('#changeRoomTypeModal').data('booking-room-id', bookingRoomId);
        const container = $('#changeRoomTypeModalBody').empty();

        $.ajax({
            url: '/get-room-types',
            method: 'GET',
            success: function (response) {
                if (response.success) {
                    let roomTypeOptions = '';

                    response.roomTypes.forEach(roomType => {
                        roomTypeOptions += `
                        <div class="form-check">
                            <input type="radio" class="form-check-input" name="newRoomType" value="${roomType.id}" ${roomType.id === currentRoomTypeId ? 'checked' : ''}>
                            <label class="form-check-label">${roomType.name}</label>
                        </div>
                    `;
                    });

                    container.append(`
                    <form id="changeRoomTypeForm">
                        ${roomTypeOptions}
                        <input type="hidden" id="changeStartDate" value="${checkInDate}">
                        <input type="hidden" id="changeEndDate" value="${checkOutDate}">
                    </form>
                `);
                } else {
                    container.html('<p>Không tìm thấy loại phòng nào.</p>');
                }
            },
            error: function () {
                container.html('<p>Không thể tải danh sách loại phòng.</p>');
            }
        });

        $('#changeRoomTypeModal').show();
    });

    // Đóng modal đổi loại phòng
    $('#changeRoomTypeModalCloseBtn, #changeRoomTypeModalCloseBtnFooter').on('click', function () {
        $('#changeRoomTypeModal').hide();
    });

    // Lưu thay đổi loại phòng
    $('#saveNewRoomTypeBtn').on('click', function () {
        const bookingRoomId = $('#changeRoomTypeModal').data('booking-room-id');
        const newRoomTypeId = $('input[name="newRoomType"]:checked').val();
        const changeStartDate = $('#changeStartDate').val();
        const changeEndDate = $('#changeEndDate').val();

        if (!newRoomTypeId) {
            alert('Vui lòng chọn loại phòng mới.');
            return;
        }

        console.log({
            booking_room_id: bookingRoomId,
            new_room_type_id: newRoomTypeId,
            change_start_date: changeStartDate,
            change_end_date: changeEndDate
        });

        $.ajax({
            url: '/booking/change-room-type',
            method: 'PATCH',
            data: {
                booking_room_id: bookingRoomId,
                new_room_type_id: newRoomTypeId,
                change_start_date: changeStartDate,
                change_end_date: changeEndDate,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công!',
                    text: 'Đổi phòng thành công!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    location.reload();
                });
            },
            error: function (xhr) {
                console.error(xhr.responseJSON);

                if (xhr.status === 422 && xhr.responseJSON.errors) {
                    let errorList = '';
                    Object.entries(xhr.responseJSON.errors).forEach(([field, msgs]) => {
                        errorList += `- ${msgs.join(', ')}\n`;
                    });

                    Swal.fire({
                        icon: 'error',
                        title: 'Dữ liệu không hợp lệ!',
                        text: 'Vui lòng kiểm tra lại:',
                        footer: `<pre style="text-align: left">${errorList}</pre>`,
                        customClass: {
                            footer: 'swal2-footer-pre'
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Có lỗi xảy ra!',
                        text: 'Xem console để biết thêm chi tiết.'
                    });
                }
            }
        });
    });
</script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const params = new URLSearchParams(window.location.search);
        const popupId = params.get('popup');

        if (popupId) {
            // Đợi DOM hiển thị đầy đủ, tối đa 2 giây
            const maxAttempts = 20;
            let attempts = 0;

            const tryOpenModal = setInterval(() => {
                const button = document.querySelector(`.btn-action.view[data-id="${popupId}"]`);
                if (button) {
                    button.click();
                    clearInterval(tryOpenModal);

                    // Đợi thêm một chút cho modal mở xong rồi mới xóa param
                    setTimeout(() => {
                        params.delete('popup');
                        const newUrl = window.location.pathname + (params.toString() ? '?' + params.toString() : '');
                        window.history.replaceState({}, '', newUrl);
                    }, 500);
                }

                if (++attempts >= maxAttempts) {
                    clearInterval(tryOpenModal); // Dừng sau 2 giây nếu không thấy nút
                }
            }, 100);
        }
    });

</script>
</body>
</html>
