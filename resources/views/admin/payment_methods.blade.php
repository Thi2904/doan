<!-- resources/views/admin/payment_methods/index.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Phương Thức Thanh Toán - Admin</title>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Phương Thức Thanh Toán</h1>
        <ul class="breadcrumbs">
            <li><a href="{{ route('admin.panel') }}">Trang chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Phương thức thanh toán</a></li>
        </ul>
        <div class="data">
            <div class="content-data">
                <div class="head">
                    <h3>Danh sách phương thức thanh toán</h3>
                    <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                        <i class="ri-add-line"></i>
                        Thêm phương thức
                    </button>
                </div>
                <table id="paymentMethodsTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên phương thức</th>>
                        <th>Kích hoạt</th>
                        <th>Hành động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($paymentMethods as $method)
                        <tr>
                            <td>{{ $method->id }}</td>
                            <td>{{ $method->name }}</td>
                            <td>{{ $method->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}</td>
                            <td class="actions">
                                <a href="#" class="btn-action view"
                                   data-id="{{ $method->id }}"
                                   data-name="{{ $method->name }}"
                                   data-active="{{ $method->is_active }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="#" class="btn-action edit"
                                   data-id="{{ $method->id }}"
                                   data-name="{{ $method->name }}"
                                   data-active="{{ $method->is_active }}">
                                    <i class="ri-edit-box-fill"></i>
                                </a>
                                <form action="{{ route('payment_methods.destroy', $method->id) }}" method="POST" style="display: inline-block; margin-top: 0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete" onclick="confirmDelete(this)">
                                        <i class="ri-delete-bin-2-fill"></i>
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

<!-- Detail Modal -->
<div id="detailModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Chi tiết phương thức</h3>
        <p><strong>ID:</strong> <span id="detailId"></span></p>
        <p><strong>Tên:</strong> <span id="detailName"></span></p>
        <p><strong>Kích hoạt:</strong> <span id="detailActive"></span></p>
        <div class="modal-footer">
            <button id="closeDetail" class="btn-action">Đóng</button>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Thêm phương thức mới</h3>
        <form action="{{ route('payment_methods.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <label>Tên</label>
                <input type="text" name="name" required>
                <label>
                    <input type="checkbox" name="is_active" checked>
                    Kích hoạt
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelCreate" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add"><i class="ri-save-3-line"></i>Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa phương thức</h3>
        <form method="POST" id="editForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="id" id="editId">
            <div class="modal-body">
                <label>Tên</label>
                <input type="text" name="name" id="editName" required>
                <label>
                    <input type="checkbox" name="is_active" id="editActive">
                    Kích hoạt
                </label>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEdit" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add"><i class="ri-save-3-line"></i>Lưu</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#paymentMethodsTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json"
            }
        });

        $('.btn-action.view').click(function () {
            const method = $(this).data();
            $('#detailId').text(method.id);
            $('#detailName').text(method.name);
            $('#detailActive').text(method.active ? 'Có' : 'Không');
            $('#detailModal').addClass('active');
        });

        $('#closeDetail').click(function () {
            $('#detailModal').removeClass('active');
        });

        $('#openCreateModal').click(function () {
            $('#createModal').addClass('active');
        });

        $('#cancelCreate').click(function () {
            $('#createModal').removeClass('active');
        });

        $('.btn-action.edit').click(function () {
            const method = $(this).data();
            $('#editId').val(method.id);
            $('#editName').val(method.name);
            $('#editActive').prop('checked', method.active);
            const formAction = '{{ route("payment_methods.update", ":id") }}'.replace(':id', method.id);
            $('#editForm').attr('action', formAction);
            $('#editModal').addClass('active');
        });

        $('#cancelEdit').click(function () {
            $('#editModal').removeClass('active');
        });
    });

    function confirmDelete(button) {
        Swal.fire({
            title: 'Bạn có chắc chắn muốn xóa?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Xóa',
            cancelButtonText: 'Hủy'
        }).then((res) => {
            if (res.isConfirmed) {
                $(button).closest('form').submit();
            }
        });
    }
</script>
</body>
</html>
