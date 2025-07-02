<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hồ Sơ Người Dùng - Hanoi Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/room_type.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,600&display=swap&subset=vietnamese" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <style>
        .status-tabs {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 1rem;
            gap: 8px;
        }
        .status-tabs .nav-link {
            padding: 6px 12px;
            border-radius: 4px;
            background-color: #eaeaea;
            color: #333;
            text-decoration: none;
        }
        .status-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: #fff;
        }
        .status-tabs li {
            list-style: none;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .profile-nav button.active { background-color: var(--primary-color); color: white; }
    </style>
</head>
<body>
@include('customer.navbar')

<main class="profile-main">
    <div class="profile-wrapper">
        <aside class="profile-sidebar">
            <div class="profile-card">
                <img src="{{ Auth::user()->avatar_url ?? asset('assets/avatar1.jpg') }}" alt="Avatar" class="profile-avatar" style="margin: 0 auto">
                <h2 class="profile-name">{{ Auth::user()->name }}</h2>
            </div>
            <nav class="profile-nav">
                <ul>
                    <li><button class="tab-btn active" data-tab="info">Thông Tin Cá Nhân</button></li>
                    <li><button class="tab-btn" data-tab="bookings">Đơn Đặt Phòng</button></li>
                    <li><button class="tab-btn" data-tab="password">Đổi Mật Khẩu</button></li>
                </ul>
            </nav>
        </aside>

        <section class="profile-content">
            <div id="info" class="tab-content active">
                <h3>Thông Tin Cá Nhân</h3>
                <form action="{{ route('profile.update') }}" method="POST" class="profile-form">
                    @csrf @method('PUT')
                    <div class="form-row">
                        <label for="name">Họ và tên</label>
                        <input type="text" id="name" name="name" value="{{ Auth::user()->name }}" required>
                    </div>
                    <div class="form-row">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="{{ Auth::user()->email }}" required>
                    </div>
                    <div class="form-row">
                        <label for="phone">Số điện thoại</label>
                        <input type="text" id="phone" name="phone" value="{{ Auth::user()->phone ?? '' }}">
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
                    </div>
                </form>
            </div>

            <div id="bookings" class="tab-content">
                <h3>Đơn Đặt Phòng của bạn</h3>
                <ul class="nav nav-tabs status-tabs" id="statusTabs">
                    <li><a class="nav-link active" data-status="all" href="#">Tất cả</a></li>
                    <li><a class="nav-link" data-status="pending" href="#">Chờ duyệt</a></li>
                    <li><a class="nav-link" data-status="confirmed" href="#">Đã xác nhận</a></li>
                    <li><a class="nav-link" data-status="checked_in" href="#">Đã nhận phòng</a></li>
                    <li><a class="nav-link" data-status="completed" href="#">Đã trả phòng</a></li>
                    <li><a class="nav-link" data-status="cancelled" href="#">Đã hủy</a></li>
                </ul>

                <table id="bookingsTable" class="bookings-table display">
                    <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Loại phòng</th>
                        <th>Ngày đến</th>
                        <th>Ngày đi</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bookings as $b)
                        <tr data-status="{{ $b->status }}">
                            <td>{{ $b->booking_code }}</td>
                            <td>{{ $b->bookingRooms->pluck('roomType.name')->join(', ') }}</td>
                            <td>{{ \Carbon\Carbon::parse($b->check_in)->format('d/m/Y') }}</td>
                            <td>{{ \Carbon\Carbon::parse($b->check_out)->format('d/m/Y') }}</td>
                            <td>
                                <span class="status status-{{ $b->status }}">
                                    {{ $b->status_label }}
                                </span>
                            </td>
                            <td>
                                <button class="btn-action view" onclick="window.location.href='{{ route('bookings.show', $b->id) }}'" title="Xem chi tiết">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div id="password" class="tab-content">
                <h3>Đổi Mật Khẩu</h3>
                <form action="{{ route('profile.change-password') }}" method="POST" class="profile-form">
                    @csrf
                    <div class="form-row">
                        <label for="current_password">Mật khẩu hiện tại</label>
                        <input type="password" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-row">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-row">
                        <label for="new_password_confirmation">Xác nhận mật khẩu mới</label>
                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                    </div>
                </form>
            </div>
        </section>
    </div>
</main>

@include('customer.footer')

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        const table = $('#bookingsTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json'
            },
            responsive: true,
        });

        $('.tab-btn').on('click', function () {
            const tab = $(this).data('tab');
            $('.tab-btn').removeClass('active');
            $('.tab-content').removeClass('active');
            $(this).addClass('active');
            $('#' + tab).addClass('active');

            if (tab === 'bookings') initStatusTabs();
        });

        $('.user-info').on('click', function () {
            $(this).closest('.user-dropdown').toggleClass('open');
        });

        $(document).on('click', function (e) {
            const dropdown = $('.user-dropdown');
            if (!dropdown.is(e.target) && dropdown.has(e.target).length === 0) {
                dropdown.removeClass('open');
            }
        });

        function initStatusTabs() {
            $('#statusTabs .nav-link').off('click').on('click', function (e) {
                e.preventDefault();
                $('#statusTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                const status = $(this).data('status');
                $('#bookingsTable tbody tr').each(function () {
                    const rowStatus = $(this).data('status');
                    if (status === 'all' || rowStatus === status) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        }

        if ($('#bookings').hasClass('active')) {
            initStatusTabs();
        }
    });
</script>
</body>
</html>
