<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Quản Lý Đổi Phòng - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Đổi Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Đổi Phòng</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách đổi phòng</h3>
                </div>
                <table id="changesTable" class="display">
                    <thead>
                    <tr>
                        <th>Mã Đặt Phòng</th>
                        <th>Khách (Tên & Email)</th>
                        <th>Phòng (Cũ → Mới)</th>
                        <th>Thời Gian Ở (Check‐in → Check‐out)</th>
                        <th>Thời Điểm Đổi</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($changes as $change)
                        <tr>
                            <td>{{ $change->booking->booking_code ?? '—' }}</td>
                            <td>
                                {{ $change->booking->guest_name ?? '—' }}<br>
                                <small>{{ $change->booking->guest_email ?? '—' }}</small>
                            </td>
                            <td>
                                {{ $change->fromRoomType->name ?? '—' }} →
                                {{ $change->toRoomType->name ?? '—' }}
                            </td>
                            <td>
                                {{ \Carbon\Carbon::parse($change->change_start_date)->format('d/m/Y') }}
                                → {{ \Carbon\Carbon::parse($change->change_end_date)->format('d/m/Y') }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($change->changed_at)->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Chưa có yêu cầu đổi phòng nào.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</section>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#changesTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json" }
        });
    });
</script>
</body>
</html>
