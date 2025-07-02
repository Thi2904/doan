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
    <title>Quản Lý Loại Giường - Admin</title>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
<main>
    <h1 class="title">Phí phụ thu</h1>
    <ul class="breadcrumbs">
        <li><a href="{{ route('admin.panel') }}">Trang chủ</a></li>
        <li class="divider">/</li>
        <li><a href="#" class="active">Phí phụ thu</a></li>
    </ul>
    <div class="data">
        <div class="content-data">
            <div class="head">
                <h3>Danh sách phí phụ thu</h3>
                <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                    <i class="ri-add-line"></i>
                    Thêm loại phí
                </button>
            </div>
            <table id="additionalFeesTable" class="display">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên</th>
                    <th>Loại</th>
                    <th>Giá mặc định</th>
                    <th>Kích hoạt</th>
                    <th>Hành động</th>
                </tr>
                </thead>
                <tbody>
                @foreach($additionalFees as $fee)
                    <tr>
                        <td>{{ $fee->id }}</td>
                        <td>{{ $fee->name }}</td>
                        <td>{{ $fee->type === 'pre_fee' ? 'Phí trước' : 'Phí sau' }}</td>
                        <td>{{ number_format($fee->default_price, 2) }} đ</td>
                        <td>{{ $fee->is_active ? 'Đang hoạt động' : 'Không hoạt động' }}</td>
                        <td class="actions">
                            <a href="#" class="btn-action view"
                               data-id="{{ $fee->id }}"
                               data-name="{{ $fee->name }}"
                               data-type="{{ $fee->type }}"
                               data-price="{{ $fee->default_price }}"
                               data-active="{{ $fee->is_active }}">
                                <i class="ri-eye-line"></i>
                            </a>
                            <a href="#" class="btn-action edit"
                               data-id="{{ $fee->id }}"
                               data-name="{{ $fee->name }}"
                               data-type="{{ $fee->type }}"
                               data-price="{{ $fee->default_price }}"
                               data-active="{{ $fee->is_active }}">
                                <i class="ri-edit-box-fill"></i>
                            </a>
                            <form action="{{ route('additional_fees.destroy', $fee->id) }}" method="POST" style="display: inline-block; margin-top: 0;">
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
    <!-- Modal Detail -->
    <div id="detailModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Chi tiết phí</h3>
            <p><strong>ID:</strong> <span id="detailId"></span></p>
            <p><strong>Tên:</strong> <span id="detailName"></span></p>
            <p><strong>Loại:</strong> <span id="detailType"></span></p>
            <p><strong>Giá mặc định:</strong> <span id="detailPrice"></span></p>
            <p><strong>Kích hoạt:</strong> <span id="detailActive"></span></p>
            <div class="modal-footer">
                <button id="closeDetail" class="btn-action">Đóng</button>
            </div>
        </div>
    </div>

    <!-- Modal Create -->
    <div id="createModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Thêm phí mới</h3>
            <form action="{{ route('additional_fees.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <label>Tên</label>
                    <input type="text" name="name" required>
                    <label>Loại</label>
                    <select name="type" required>
                        <option value="pre_fee">Phí trước</option>
                        <option value="post_fee">Phí sau</option>
                    </select>
                    <label>Giá mặc định</label>
                    <input type="number" step="0.01" name="default_price" required>
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

    <!-- Modal Edit -->
    <div id="editModal" class="modal-overlay">
        <div class="modal-box">
            <h3>Chỉnh sửa phí</h3>
            <form method="POST" id="editForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <label>Tên</label>
                    <input type="text" name="name" id="editName" required>
                    <label>Loại</label>
                    <select name="type" id="editType" required>
                        <option value="pre_fee">Phí trước</option>
                        <option value="post_fee">Phí sau</option>
                    </select>
                    <label>Giá mặc định</label>
                    <input type="number" step="0.01" name="default_price" id="editPrice" required>
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
            $('#additionalFeesTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json"
                }
            });

            $('.btn-action.view').click(function () {
                const fee = $(this).data();
                $('#detailId').text(fee.id);
                $('#detailName').text(fee.name);
                $('#detailType').text(fee.type === 'pre_fee' ? 'Phí trước' : 'Phí sau');
                $('#detailPrice').text(parseFloat(fee.price).toFixed(2) + ' đ');
                $('#detailActive').text(fee.active ? 'Có' : 'Không');
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
                const fee = $(this).data();
                $('#editId').val(fee.id);
                $('#editName').val(fee.name);
                $('#editType').val(fee.type);
                $('#editPrice').val(fee.price);
                $('#editActive').prop('checked', fee.active);
                const formAction = '{{ route("additional_fees.update", ":id") }}'.replace(':id', fee.id);
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
            }).then((res)=> {
                if(res.isConfirmed){
                    $(btn).closest('form').submit();
                }
            });
        }
    </script>
</body>
</html>
