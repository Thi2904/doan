<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông báo đặt phòng mới - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background-color: #f6f6f6;
            margin: 0;
            padding: 0;
            color: #000000;
        }

        .email-wrapper {
            width: 100%;
            padding: 20px 0;
            background-color: #f6f6f6;
        }

        .email-content {
            max-width: 640px;
            margin: auto;
            background: #ffffff;
            padding: 32px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .email-header {
            text-align: center;
            margin-bottom: 24px;
        }

        .email-header img {
            height: 60px;
            margin-bottom: 16px;
        }

        .email-header h1 {
            margin: 0;
            color: #e3342f;
        }

        .email-body h2 {
            margin-top: 0;
            color: #000000;
        }

        .email-body p {
            font-size: 15px;
            color: #000000;
        }

        .summary-table, .room-table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
        }

        .summary-table td {
            padding: 6px 0;
            font-size: 14px;
            color: #000000;
        }

        .summary-table td:first-child {
            font-weight: bold;
            width: 40%;
        }

        .room-table th, .room-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-size: 14px;
            color: #000000;
        }

        .room-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .button {
            display: inline-block;
            background-color: #e3342f;
            color: #ffffff !important;
            font-weight: bold;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            margin: 20px 0;
            text-align: center;
            font-size: 15px;
        }

        .email-footer {
            font-size: 12px;
            text-align: center;
            color: #999999;
            margin-top: 40px;
        }

        @media only screen and (max-width: 600px) {
            .email-content {
                padding: 16px;
            }

            .button {
                display: block;
                width: 100%;
                box-sizing: border-box;
            }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-content">
        <div class="email-header">
            <img src="https://hanoihotel.com.vn/wp-content/uploads/2019/06/Logo-Hanoi-Hotel-gold-1.png" alt="Logo khách sạn">
            <h1>Đơn đặt phòng mới: {{ $booking->booking_code }}</h1>
        </div>

        <div class="email-body">
            <h2>Thông tin khách hàng:</h2>
            <table class="summary-table">
                <tr><td>Họ tên:</td><td>{{ $booking->guest_name }}</td></tr>
                <tr><td>Email:</td><td>{{ $booking->guest_email }}</td></tr>
                <tr><td>Số điện thoại:</td><td>{{ $booking->guest_phone }}</td></tr>
                <tr><td>Ngày nhận phòng:</td><td>{{ $booking->check_in->format('d/m/Y') }}</td></tr>
                <tr><td>Ngày trả phòng:</td><td>{{ $booking->check_out->format('d/m/Y') }}</td></tr>
                <tr><td>Tổng giá:</td><td>{{ number_format($booking->total_price, 0, ',', '.') }}₫</td></tr>
            </table>

            <h3 style="color: #000000;">Chi tiết phòng:</h3>
            <table class="room-table">
                <thead>
                <tr>
                    <th>Loại phòng</th>
                    <th>Người lớn</th>
                    <th>Trẻ em</th>
                    <th>Đơn giá/đêm</th>
                    <th>Phụ phí</th>
                    <th>Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($booking->bookingRooms as $room)
                    <tr>
                        <td>{{ $room->roomType->name }}</td>
                        <td>{{ $room->adults }}</td>
                        <td>{{ $room->children }}</td>
                        <td>{{ number_format($room->price_per_night, 0, ',', '.') }}₫</td>
                        <td>
                            @if ($room->surcharges->isNotEmpty())
                                <ul style="margin: 0; padding-left: 16px;">
                                    @foreach ($room->surcharges as $fee)
                                        <li>{{ $fee->name }} ({{ number_format($fee->pivot->price, 0, ',', '.') }}₫)</li>
                                    @endforeach
                                </ul>
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ number_format($room->sub_total, 0, ',', '.') }}₫</td>
                    </tr>
                @endforeach
                </tbody>
            </table>

            <p style="text-align: center;">
                <a href="{{ route('admin.bookings.index', ['popup' => $booking->id]) }}" class="button" target="_blank">
                    Xem chi tiết đơn đặt phòng
                </a>
            </p>

            <p style="color: #000000; text-align: center;">
                * Hệ thống sẽ mở popup tự động khi truy cập admin.
            </p>
        </div>

        <div class="email-footer">
            &copy; {{ now()->year }} {{ config('app.name') }}. Mọi quyền được bảo lưu.
        </div>
    </div>
</div>
</body>
</html>
