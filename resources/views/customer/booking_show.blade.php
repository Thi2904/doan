<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hạng Phòng - Hanoi Hotel Luxurious accommodations</title>
    <link rel="stylesheet" id="google-fonts-1-css" href="https://fonts.googleapis.com/css?family=Raleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CCormorant+Garamond%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CGothic+A1%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;subset=vietnamese&amp;ver=6.7.1" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/room_type.css') }}">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
        .booking-detail-container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        h2 {
            margin-bottom: 40px;
            color: #2c3e50;
        }

        .highlight {
            color: #e74c3c;
        }

        .main-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 30px;
        }

        .section {
            margin-bottom: 30px;
        }

        .section h3 {
            margin-bottom: 15px;
            color: #34495e;
            border-left: 5px solid #3498db;
            padding-left: 10px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }

        .info-column,
        .room-card,
        .payment-entry {
            background: #F7F4ED;
            border: 1px solid #C9BDAF;
            padding: 15px;
            border-radius: 5px;
        }

        .info-column p,
        .room-card p,
        .payment-entry p {
            margin: 8px 0;
        }

        .surcharge h4 {
            margin-top: 10px;
            color: #e67e22;
        }

        .surcharge ul {
            margin-left: 20px;
        }

        .surcharge li {
            color: #555;
            margin-top: 0.25rem;
        }

        @media (max-width: 768px) {
            .main-grid {
                grid-template-columns: 1fr;
            }
        }

        .sub-section h4 {
            margin-bottom: 10px;
        }

        .payment-entry {
            border-left: 4px solid #ccc;
        }

        .sub-section .payment-entry:nth-child(even) {
            background-color: #f2f2f2;
        }

        .header {
            position: relative;  /* hoặc fixed nếu bạn cần header luôn cố định */
        }

        .header, body {
            overflow: visible !important;
        }
</style>
<body>
<header class="header">
    @include('customer.navbar')
</header>
<div class="booking-detail-container">
    <h2>Chi tiết Đặt phòng: <span class="highlight">{{ $booking->booking_code }}</span></h2>
    @php
        $paymentStatusLabels = [
            'pending' => 'Đang chờ',
            'paid'    => 'Đã thanh toán',
            'failed'  => 'Thất bại',
            'cancel'  => 'Đã huỷ',
        ];

        $bookingStatusLabels = [
            'pending'   => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'cancelled' => 'Đã huỷ',
            'completed' => 'Hoàn thành',
        ];
    @endphp
    <div class="main-grid">
        <!-- Cột trái -->
        <div>
            <!-- Thông tin khách và đặt phòng -->
            <div class="section">
                <h3>Thông tin khách & Đặt phòng</h3>
                <div class="info-grid">
                    <div class="info-column">
                        <p><strong>Tên khách:</strong> {{ $booking->guest_name }}</p>
                        <p><strong>Email:</strong> {{ $booking->guest_email }}</p>
                        <p><strong>Điện thoại:</strong> {{ $booking->guest_phone }}</p>
                    </div>
                    <div class="info-column">
                        <p><strong>Ngày vào ở:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</p>
                        <p><strong>Ngày trả phòng:</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</p>

                        <p><strong>Trạng thái đơn:</strong>
                            {{ $bookingStatusLabels[$booking->status] ?? ucfirst($booking->status) }}
                        </p>
                        <p><strong>Tổng tiền:</strong> {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</p>
                    </div>
                </div>
            </div>


            <div class="section">
                <h3>Thông tin thanh toán</h3>

                <!-- Đã thanh toán -->
                <div class="sub-section">
                    <h4 style="color: #27ae60;">Đã thanh toán</h4>
                    <div class="info-grid">
                        @forelse ($booking->payments->where('status', 'paid') as $payment)
                            <div class="payment-entry">
                                <p><strong>Phương thức:</strong> {{ $payment->paymentMethod->name ?? 'Không rõ' }}</p>
                                <p><strong>Số tiền:</strong> {{ number_format($payment->amount, 0, ',', '.') }} VNĐ</p>
                                <p><strong>Ngày thanh toán:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                        @empty
                            <p>Không có khoản nào đã thanh toán.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Chưa thanh toán -->
                <div class="sub-section" style="margin-top: 20px;">
                    <h4 style="color: #e74c3c;">Chưa thanh toán</h4>
                    <div class="info-grid">
                        @forelse ($booking->payments->where('status', '!=', 'paid') as $payment)
                            <div class="payment-entry">
                                <p><strong>Số tiền:</strong> {{ number_format($payment->amount, 0, ',', '.') }} VNĐ</p>
                                <p><strong>Ngày thanh toán:</strong> {{ $payment->created_at->format('d/m/Y H:i') }}</p>
                                <p><strong>Trạng thái thanh toán:</strong>
                                    {{ $paymentStatusLabels[$payment->status] ?? ucfirst($payment->status) }}
                                </p>
                            </div>
                        @empty
                            <p>Không có khoản nào đang chờ thanh toán.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>

        <!-- Cột phải -->
        <div>
            <!-- Phòng đã đặt -->
            <div class="section">
                <h3>Phòng đã đặt</h3>
                <div class="info-grid">
                    @foreach ($booking->bookingRooms as $room)
                        <div class="room-card">
                            <p><strong>Loại phòng:</strong> {{ $room->roomType->name }}</p>
                            <p><strong>Người lớn:</strong> {{ $room->adults }}</p>
                            <p><strong>Trẻ em:</strong> {{ $room->children }}</p>
                            <p><strong>Giá mỗi đêm:</strong> {{ number_format($room->price_per_night, 0, ',', '.') }} VNĐ</p>
                            <p><strong>Số đêm:</strong> {{ $room->nights }}</p>
                            <p><strong>Tạm tính:</strong> {{ number_format($room->sub_total, 0, ',', '.') }} VNĐ</p>

                            @if($room->surcharges->count())
                                <div class="surcharge">
                                    <h4>Phụ phí:</h4>
                                    <ul>
                                        @foreach ($room->surcharges as $fee)
                                            <li>{{ $fee->name }} - {{ number_format($fee->pivot->price, 0, ',', '.') }} VNĐ</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Phòng đã cấp phát -->
            <div class="section">
                <h3>Phòng đã cấp phát</h3>
                <div class="info-grid">
                    @foreach ($booking->allocations as $allocation)
                        <div class="info-column">
                            <p>Phòng số: <b>{{ $allocation->room->room_number }} </b> (Loại: <i> <b> {{ $allocation->room->roomType->name ?? 'Không rõ' }}</b> </i>)</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@include('customer.footer')
<script>
    document.querySelector('.user-info').addEventListener('click', function(){
        this.closest('.user-dropdown').classList.toggle('open');
    });
    // Optionally: click ngoài đóng dropdown
    document.addEventListener('click', function(e){
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
</script>
</body>
</html>
