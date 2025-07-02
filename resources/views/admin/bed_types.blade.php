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
        <h1 class="title">Loại Giường</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Loại Giường</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách loại giường</h3>
                    <!-- Nút mở modal tạo mới -->
                    <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                        <i class="ri-add-line"></i> Thêm Loại Giường
                    </button>
                </div>
                <table id="bedTypesTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Loại Giường</th>
                        <th>Hành Động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($bedTypes as $bedType)
                        <tr>
                            <td>{{ $bedType->id }}</td>
                            <td>{{ $bedType->name }}</td>
                            <td class="actions">
                                <a href="#" class="btn-action view"
                                   data-id="{{ $bedType->id }}"
                                   data-name="{{ $bedType->name }}"
                                >
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="#" class="btn-action edit"
                                   data-id="{{ $bedType->id }}"
                                   data-name="{{ $bedType->name }}">
                                    <i class="ri-edit-box-fill"></i>
                                </a>
                                <form action="{{ route('bed_types.destroy', $bedType->id) }}" method="POST" style="display:inline-block; margin:0;">
                                    @csrf @method('DELETE')
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

<!-- Modal chi tiết -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chi tiết loại giường</h3>
        <div class="modal-body">
            <p><strong>ID:</strong> <span id="bedTypeId"></span></p>
            <p><strong>Tên:</strong> <span id="bedTypeName"></span></p>
        </div>
        <div class="modal-footer">
            <button id="modalCloseBtn" class="btn-action">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal tạo mới loại giường -->
<div id="createModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-content">
            <div class="form-section">
                <h3>Thêm loại giường mới</h3>
                <form action="{{ route('bed_types.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="name"><strong>Tên loại giường:</strong></label>
                        <textarea id="name" name="name" required placeholder="Nhập tên loại giường.." rows="4"></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="createCancelBtn" class="btn-action">Hủy</button>
                        <button type="submit" class="btn-action add">
                            <i class="ri-save-3-line"></i> Lưu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal chỉnh sửa loại giường -->
<div id="editModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa loại giường</h3>
        <form action="{{ route('bed_types.update', ['bed_type' => 0]) }}" method="POST" id="editBedTypeForm">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <label for="editName"><strong>Tên loại giường:</strong></label>
                <textarea id="editName" name="name" required placeholder="Nhập tên loại giường.." rows="4"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" id="editCancelBtn" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add">
                    <i class="ri-save-3-line"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(document).ready(function(){
        $('#bedTypesTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json" }
        });

        // Xem chi tiết
        $('#bedTypesTable').on('click', '.btn-action.view', function (e) {
            e.preventDefault();
            const btn = $(this);
            $('#bedTypeId').text(btn.data('id'));
            $('#bedTypeName').text(btn.data('name'));
            $('#modalOverlay').addClass('active');
        });

        // Đóng modal chi tiết
        $('#modalCloseBtn').on('click', function () {
            $('#modalOverlay').removeClass('active');
        });
        $('#modalOverlay').on('click', function (e) {
            if (e.target.id === 'modalOverlay') $(this).removeClass('active');
        });

        // Mở modal tạo mới
        $('#openCreateModal').on('click', function(){
            $('#createModalOverlay').addClass('active');
        });

        // Đóng modal tạo mới
        $('#createCancelBtn').on('click', function(){
            $('#createModalOverlay').removeClass('active');
        });
        $('#createModalOverlay').on('click', function(e){
            if(e.target.id === 'createModalOverlay') $(this).removeClass('active');
        });

        // Chỉnh sửa loại giường
        $('#bedTypesTable').on('click', '.btn-action.edit', function (e) {
            e.preventDefault();
            const btn = $(this);
            const bedTypeId = btn.data('id');
            const bedTypeName = btn.data('name');

            // Điền dữ liệu vào form chỉnh sửa
            $('#editName').val(bedTypeName);

            // Cập nhật URL action của form
            $('#editBedTypeForm').attr('action', '{{ route('bed_types.update', ['bed_type' => '__id__']) }}'.replace('__id__', bedTypeId));

            // Mở modal chỉnh sửa
            $('#editModalOverlay').addClass('active');
        });

        // Đóng modal chỉnh sửa
        $('#editCancelBtn').on('click', function () {
            $('#editModalOverlay').removeClass('active');
        });
        $('#editModalOverlay').on('click', function (e) {
            if (e.target.id === 'editModalOverlay') $(this).removeClass('active');
        });
    });

    function confirmDelete(btn){
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
