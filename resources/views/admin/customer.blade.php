<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal-profile.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Khách Hàng - Admin</title>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Khách Hàng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Khách Hàng</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách khách hàng</h3>
                </div>
                <table id="customersTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($customers as $customer)
                        <tr>
                            <td>{{ $customer->id }}</td>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->email }}</td>
                            <td>{{ $customer->phone }}</td>
                            <td>{{ $customer->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}</td>
                            <td>{{ $customer->created_at->format('d/m/Y') }}</td>
                            <td class="actions">
                                <!-- Nút View -->
                                <a href="#" class="btn-action view"
                                   data-id="{{ $customer->id }}"
                                   data-name="{{ $customer->name }}"
                                   data-email="{{ $customer->email }}"
                                   data-phone="{{ $customer->phone }}"
                                   data-address="{{ $customer->address }}"
                                   data-created="{{ $customer->created_at->format('d/m/Y') }}"
                                   data-active="{{ $customer->is_active }}"
                                   data-photo="{{ $customer->profile_photo_path }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <!-- Nút Toggle trạng thái -->
                                <form action="{{ route('customers.toggleStatus', $customer->id) }}" method="POST" style="display:inline-block; margin-top:0">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" class="btn-action toggle-status" data-status="{{ $customer->is_active ? 1 : 0 }}">
                                        <i class="ri-shuffle-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</section>

<!-- Custom Modal Overlay -->
<!-- ========== HTML ========== -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal-box">
        <!-- Main text content -->
        <div class="modal-content">
            <div class="modal-body">
                <h3>Chi tiết khách hàng</h3>
                <div class="card-divider"></div>
                <p><strong>ID:</strong> <span id="customerId"></span></p>
                <p><strong>Tên:</strong> <span id="customerName"></span></p>
                <p><strong>Email:</strong> <span id="customerEmail"></span></p>
                <p><strong>SĐT:</strong> <span id="customerPhone"></span></p>
                <p><strong>Địa chỉ:</strong> <span id="customerAddress"></span></p>
                <p><strong>Ngày tạo:</strong> <span id="customerCreatedAt"></span></p>
                <p><strong>Trạng thái:</strong> <span id="customerActive"></span></p>
            </div>
        </div>
        <!-- Side panel: image + close button -->
        <div class="side-panel">
            <div class="modal-image">
                <img id="customerPhoto" src="" alt="Ảnh đại diện">
            </div>
            <div class="modal-footer">
                <button id="modalCloseBtn" class="btn-action">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#customersTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json" }
        });

        // Xem chi tiết
        $('#customersTable').on('click', '.btn-action.view', function (e) {
            e.preventDefault();
            const btn = $(this);
            $('#customerId').text(btn.data('id'));
            $('#customerName').text(btn.data('name'));
            $('#customerEmail').text(btn.data('email'));
            $('#customerPhone').text(btn.data('phone'));
            $('#customerAddress').text(btn.data('address'));
            $('#customerCreatedAt').text(btn.data('created'));

            // Xử lý ảnh đại diện
            const photoPath = btn.data('photo');
            const imageUrl = photoPath ? `/storage/${photoPath}` : '{{ asset('assets/Default.jpg') }}';
            $('#customerPhoto').attr('src', imageUrl);

            // Trạng thái Active/Inactive
            const isActive = btn.data('active');
            const statusText = isActive ? 'Đang hoạt động' : 'Không hoạt động';
            $('#customerActive').text(statusText);

            if (isActive) {
                $('#customerActive').addClass('active-status').removeClass('inactive-status');
            } else {
                $('#customerActive').addClass('inactive-status').removeClass('active-status');
            }

            $('#modalOverlay').addClass('active');
        });
// 1. Close on “Đóng” button
        $('#modalCloseBtn').on('click', function(e) {
            e.preventDefault();
            $('#modalOverlay').removeClass('active');
        });

// 2. Close when clicking on the overlay (but not when click vào modal-box)
        $('#modalOverlay').on('click', function(e) {
            // nếu target chính là overlay thì đóng
            if (e.target === this) {
                $(this).removeClass('active');
            }
        });

// 3. (Optional) Close on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('#modalOverlay').removeClass('active');
            }
        });

        // Toggle trạng thái
        $('#customersTable').on('click', '.btn-action.toggle-status', function (e) {
            e.preventDefault();
            const btn = $(this);
            const action = btn.data('status') ? 'hủy kích hoạt' : 'kích hoạt';
            Swal.fire({
                title: `Bạn có chắc muốn ${action} khách hàng này?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#aaa',
                confirmButtonText: 'Có',
                cancelButtonText: 'Không'
            }).then((res) => {
                if (res.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        });
    });
</script>
</body>
</html>
