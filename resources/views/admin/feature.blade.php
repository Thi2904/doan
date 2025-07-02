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
    <title>Quản Lý Tính Năng - Admin</title>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Tiện Nghi</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Tiện Nghi</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách tiện nghi</h3>
                    <!-- Nút mở modal tạo mới -->
                    <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                        <i class="ri-add-line"></i> Thêm Tính Năng
                    </button>
                </div>
                <table id="featuresTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Tính Năng</th>
                        <th>Hành Động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($features as $feature)
                        <tr>
                            <td>{{ $feature->id }}</td>
                            <td>{{ $feature->name }}</td>
                            <td class="actions">
                                <a href="#" class="btn-action view"
                                   data-id="{{ $feature->id }}"
                                   data-name="{{ $feature->name }}"
                                   data-description="{{ $feature->description }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <a href="#" class="btn-action edit"
                                   data-id="{{ $feature->id }}"
                                   data-name="{{ $feature->name }}">
                                    <i class="ri-edit-box-fill"></i>
                                </a>
                                <form action="{{ route('features.destroy', $feature->id) }}" method="POST" style="display:inline-block; margin:0;">
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
        <h3>Chi tiết tính năng</h3>
        <div class="modal-body">
            <p><strong>ID:</strong> <span id="featureId"></span></p>
            <p><strong>Tên:</strong> <span id="featureName"></span></p>
        </div>
        <div class="modal-footer">
            <button id="modalCloseBtn" class="btn-action">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal tạo mới tính năng -->
<div id="createModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-content">
            <div class="form-section">
                <h3>Thêm tính năng mới</h3>
                <form action="{{ route('features.store') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="name"><strong>Tên tính năng:</strong></label>
                        <textarea id="name" name="name" required placeholder="Nhập tên tính năng.." rows="4"></textarea>
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

<!-- Modal chỉnh sửa tính năng -->
<div id="editModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa tính năng</h3>
        <form action="{{ route('features.update', ['feature' => 0]) }}" method="POST" id="editFeatureForm">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <label for="editName"><strong>Tên tính năng:</strong></label>
                <textarea id="editName" name="name" required placeholder="Nhập tên tính năng.." rows="4"></textarea>
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
        $('#featuresTable').DataTable({
            language: { url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json" }
        });

        // Xem chi tiết
        $('#featuresTable').on('click', '.btn-action.view', function (e) {
            e.preventDefault();
            const btn = $(this);
            $('#featureId').text(btn.data('id'));
            $('#featureName').text(btn.data('name'));
            $('#featureDescription').text(btn.data('description'));
            $('#modalOverlay').addClass('active');
        });

        // Đóng modal chi tiết
        $('#modalClose, #modalCloseBtn').on('click', function () {
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
        $('#createModalClose, #createCancelBtn').on('click', function(){
            $('#createModalOverlay').removeClass('active');
        });
        $('#createModalOverlay').on('click', function(e){
            if(e.target.id === 'createModalOverlay') $(this).removeClass('active');
        });

        // Chỉnh sửa tính năng
        $('#featuresTable').on('click', '.btn-action.edit', function (e) {
            e.preventDefault();
            const btn = $(this);
            const featureId = btn.data('id');
            const featureName = btn.data('name');

            // Điền dữ liệu vào form chỉnh sửa
            $('#editFeatureId').val(featureId);
            $('#editName').val(featureName);

            // Cập nhật URL action của form
            $('#editFeatureForm').attr('action', '{{ route('features.update', ['feature' => '__id__']) }}'.replace('__id__', featureId));

            // Mở modal chỉnh sửa
            $('#editModalOverlay').addClass('active');
        });

        // Đóng modal chỉnh sửa
        $('#editModalClose, #editCancelBtn').on('click', function () {
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
        }).then((res)=>{
            if(res.isConfirmed){
                $(btn).closest('form').submit();
            }
        });
    }
</script>
</body>
</html>
