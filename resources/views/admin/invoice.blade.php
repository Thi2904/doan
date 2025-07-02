<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa Đơn Đặt Phòng - {{ $booking->booking_code }}</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway:400,700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            font-style: normal;
            font-weight: normal;
            src: url('{{ public_path('fonts/DejaVuSans.ttf') }}') format('truetype');
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
        }

        body {
            background: #f2f2f2;
            margin: 0;
            padding: 20px;
        }
        .invoice-container {
            max-width: 1000px;
            margin: auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .invoice-header h2 {
            color: #2c3e50;
        }
        .btn-download {
            background: #3498db;
            color: #fff;
            padding: 8px 14px;
            border-radius: 5px;
            text-decoration: none;
        }
        @media print {
            .no-print {
                display: none !important;
            }
        }
        .section {
            margin-bottom: 25px;
        }
        .section h3 {
            color: #34495e;
            border-left: 5px solid #3498db;
            padding-left: 10px;
            margin-bottom: 15px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 15px;
        }
        .card {
            background: #f9f9f9;
            border: 1px solid #ccc;
            padding: 15px;
            border-radius: 5px;
        }
        .card p {
            margin: 8px 0;
        }
        .surcharge ul {
            padding-left: 20px;
        }
    </style>
</head>
<body>
<div class="invoice-container">
    <div class="invoice-header">
        <h2>Hóa đơn đặt phòng: <span style="color:#e74c3c">{{ $booking->booking_code }}</span></h2>
    </div>

    <div class="section">
        <h3>Thông tin khách hàng</h3>
        <div class="info-grid">
            <div class="card">
                <p><strong>Họ tên:</strong> {{ $booking->guest_name }}</p>
                <p><strong>Email:</strong> {{ $booking->guest_email }}</p>
                <p><strong>Số điện thoại:</strong>
                    {{ preg_replace('/(\d{3})(\d{3})(\d{3,})/', '$1 $2 $3', $booking->guest_phone) }}
                </p>
            </div>
            <div class="card">
                <p><strong>Ngày nhận phòng:</strong> {{ \Carbon\Carbon::parse($booking->check_in)->format('d/m/Y') }}</p>
                <p><strong>Ngày trả phòng:</strong> {{ \Carbon\Carbon::parse($booking->check_out)->format('d/m/Y') }}</p>
                <p><strong>Tổng tiền đơn:</strong> {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</p>
            </div>
        </div>
    </div>

    <div class="section">
        <h3>Thông tin phòng</h3>
        <div class="info-grid">
            @foreach($booking->bookingRooms as $room)
                <div class="card">
                    <p><strong>Loại phòng:</strong> {{ $room->roomType->name }}</p>
                    <p><strong>Người lớn:</strong> {{ $room->adults }}</p>
                    <p><strong>Trẻ em:</strong> {{ $room->children }}</p>
                    <p><strong>Giá mỗi đêm:</strong> {{ number_format($room->price_per_night, 0, ',', '.') }} VNĐ</p>
                    <p><strong>Số đêm:</strong> {{ $room->nights }}</p>
                    <p><strong>Tạm tính:</strong> {{ number_format($room->sub_total, 0, ',', '.') }} VNĐ</p>
                    @if($room->surcharges->count())
                        <div class="surcharge">
                            <strong>Phụ phí:</strong>
                            <ul>
                                @foreach($room->surcharges as $fee)
                                    <li>{{ $fee->name }} - {{ number_format($fee->pivot->price, 0, ',', '.') }} VNĐ</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <h3>Phòng đã cấp phát</h3>
        <div class="info-grid">
            @foreach ($booking->allocations as $allocation)
                <div class="card">
                    <p><strong>Phòng số:</strong> {{ $allocation->room->room_number }}</p>
                    <p><strong>Loại:</strong> {{ $allocation->room->roomType->name ?? 'Không rõ' }}</p>
                </div>
            @endforeach
        </div>
    </div>

    <div class="section">
        <h3>Chi tiết các khoản thanh toán</h3>
        <div class="info-grid">
            @forelse ($booking->payments as $payment)
                <div class="card">
                    <p><strong>Loại khoản:</strong>
                        @if ($payment->entry_type === 'charge')
                            Phụ thu
                        @elseif ($payment->entry_type === 'refund')
                            Hoàn tiền
                        @elseif ($payment->entry_type === 'deposit')
                            Đặt cọc
                        @elseif ($payment->entry_type === 'adjustment')
                            Điều chỉnh
                        @elseif ($payment->entry_type === 'payment')
                            Thanh toán
                        @else
                            Khác
                        @endif
                    </p>
                    <p><strong>Mô tả:</strong> {{ $payment->description ?? 'Không rõ' }}</p>
                    <p><strong>Số tiền:</strong>
                        {{ number_format($payment->amount, 0, ',', '.') }} VNĐ
                        @if ($payment->entry_type === 'refund')
                            <span style="color:green">(Trả lại)</span>
                        @elseif ($payment->entry_type === 'charge')
                            <span style="color:red">(Thu thêm)</span>
                        @endif
                    </p>
                    <p><strong>Ngày tạo:</strong> {{ $payment->created_at->format('d/m/Y') }}</p>
                </div>
            @empty
                <p>Không có khoản thanh toán hoặc hoàn tiền nào được ghi nhận.</p>
            @endforelse
        </div>
    </div>

    @php
        $totalCharges = $booking->payments->where('entry_type', 'charge')->sum('amount');
        $totalRefunds = $booking->payments->where('entry_type', 'refund')->sum('amount');
        $totalPaid = $booking->payments
            ->where('entry_type', 'charge')
            ->where('status', 'paid')
            ->sum('amount');
        $totalRefunded = $booking->payments
            ->where('entry_type', 'refund')
            ->where('status', 'paid')
            ->sum('amount');
        $netPaid = $totalPaid - $totalRefunded;
        $totalDue = max(0, $booking->total_price + $totalCharges - $netPaid);
    @endphp

    <div class="section">
        <h3>Tổng kết thanh toán</h3>
        <div class="info-grid">
            <div class="card">
                <p><strong>Tổng phụ thu:</strong> {{ number_format($totalCharges, 0, ',', '.') }} VNĐ</p>
                <p><strong>Tổng hoàn tiền:</strong> {{ number_format($totalRefunds, 0, ',', '.') }} VNĐ</p>
                <p><strong>Tổng tiền đơn:</strong> {{ number_format($booking->total_price, 0, ',', '.') }} VNĐ</p>

            </div>
        </div>
    </div>

</div>
</body>
</html>
