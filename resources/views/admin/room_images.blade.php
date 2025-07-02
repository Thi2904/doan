<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="{{ asset('css/gallery.css') }}">
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <title>Quản Lý Ảnh Phòng - Admin</title>
</head>
<body>
@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Ảnh Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Ảnh Phòng</a></li>
        </ul>

        <div class="head d-flex justify-content-between align-items-center mb-3" style="margin-top: 20px">
            <button id="openCreateModal" class="btn-action add">
                <i class="ri-add-line"></i> Thêm Ảnh
            </button>
        </div>

        <ul class="gallery">
            @foreach($images as $img)
                <li class="gallery-item">
                    <a href="{{ Storage::url($img->image_url) }}" data-lightbox="room-{{ $img->room_type_id }}">
                        <img loading="lazy"
                             src="{{ Storage::url($img->image_url) }}"
                             alt="Ảnh Phòng {{ $img->roomType->name }}" />
                    </a>
                    <div class="gallery-info">
                        <span>ID#{{ $img->image_id }}</span>
                        <span>{{ $img->roomType->name }}</span>
                    </div>
                    <div class="actions">
                        <button class="btn-action view"
                                data-id="{{ $img->image_id }}"
                                data-roomtype="{{ $img->roomType->name }}"
                                data-url="{{ Storage::url($img->image_url) }}"
                                data-active="{{ $img->is_active }}">
                            <i class="ri-eye-line"></i>
                        </button>
                        <button class="btn-action edit"
                                data-id="{{ $img->image_id }}"
                                data-action="{{ route('room_images.update', $img->image_id) }}"
                                data-roomtype_id="{{ $img->room_type_id }}"
                                data-url="{{ Storage::url($img->image_url) }}"
                                data-active="{{ $img->is_active }}">
                            <i class="ri-edit-box-fill"></i>
                        </button>
                        <form action="{{ route('room_images.destroy', $img->image_id) }}"
                              method="POST"
                              style="display:inline-block; margin-top: 0">
                            @csrf @method('DELETE')
                            <button type="button" class="btn-action delete" onclick="confirmDelete(this)">
                                <i class="ri-delete-bin-2-fill"></i>
                            </button>
                        </form>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-3">{{ $images->links() }}</div>
    </main>
</section>

<!-- View Modal -->
<div id="viewModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Chi tiết ảnh phòng</h3>
        <div class="modal-body">
            <p><strong>ID:</strong> <span id="viewId"></span></p>
            <p><strong>Loại Phòng:</strong> <span id="viewRoomType"></span></p>
            <p><strong>Ảnh:</strong><br><img id="viewImage" src="" style="max-width:100%;"></p>
            <p><strong>Trạng Thái:</strong> <span id="viewActive"></span></p>
        </div>
        <div class="modal-footer">
            <button id="closeViewModal" class="btn-action">Đóng</button>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div id="createModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Thêm ảnh phòng mới</h3>
        <form action="{{ route('room_images.store') }}"
              method="POST"
              enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <label><strong>Loại Phòng:</strong></label>
                <select name="room_type_id" required>
                    <option value="">-- Chọn loại phòng --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>

                <label class="mt-2"><strong>Chọn Ảnh:</strong></label>
                <input type="file" name="image_file" id="image_file" accept="image/*" required>
                <div class="preview-container mt-2">
                    <img id="createPreview" src="#" style="display:none; max-width:100%;">
                </div>

                <div class="form-group mt-2">
                    <input type="checkbox" name="is_active" id="is_active" checked>
                    <label for="is_active">Hoạt động</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelCreate" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add">
                    <i class="ri-save-3-line"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa ảnh phòng</h3>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-body">
                <label><strong>Loại Phòng:</strong></label>
                <select name="room_type_id" id="edit_room_type_id" required>
                    <option value="">-- Chọn loại phòng --</option>
                    @foreach($roomTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>

                <label class="mt-2"><strong>Chọn Ảnh:</strong></label>
                <input type="file" name="image_file" id="edit_image_file" accept="image/*">
                <div class="preview-container mt-2">
                    <img id="editPreview" src="#" style="display:none; max-width:100%;">
                </div>

                <div class="form-group mt-2">
                    <input type="checkbox" name="is_active" id="edit_is_active">
                    <label for="edit_is_active">Hoạt động</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="cancelEdit" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add">
                    <i class="ri-save-3-line"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(function() {
        function toggleModal(id, show) {
            $(id).toggleClass('active', show);
        }

        $('#image_file, #edit_image_file').change(function() {
            const preview = this.id === 'image_file' ? '#createPreview' : '#editPreview';
            const reader = new FileReader();
            reader.onload = e => $(preview).attr('src', e.target.result).show();
            reader.readAsDataURL(this.files[0]);
        });

        $('.view').click(function() {
            $('#viewId').text($(this).data('id'));
            $('#viewRoomType').text($(this).data('roomtype'));
            $('#viewImage').attr('src', $(this).data('url'));
            $('#viewActive').text($(this).data('active') ? 'Đang hoạt động' : 'Không hoạt động');
            toggleModal('#viewModal', true);
        });

        $('#closeViewModal, #viewModal').click(e => {
            if (e.target.id==='viewModal' || e.target.id==='closeViewModal')
                toggleModal('#viewModal', false);
        });

        $('#openCreateModal').click(() => toggleModal('#createModal', true));
        $('#cancelCreate, #createModal').click(e => {
            if (e.target.id==='createModal' || e.target.id==='cancelCreate')
                toggleModal('#createModal', false);
        });

        $('#editForm').submit(function(e){
            console.log('FORM ACTION =', $(this).attr('action'));
            console.log('FILE INPUT =', $('#edit_image_file')[0].files);
        });

        $('.edit').click(function() {
            const actionUrl = $(this).data('action');
            $('#editForm').attr('action', actionUrl);
            $('#edit_room_type_id').val($(this).data('roomtype_id'));
            $('#edit_is_active').prop('checked', $(this).data('active'));
            $('#editPreview').attr('src', $(this).data('url')).show();
            toggleModal('#editModal', true);
        });

        $('#cancelEdit, #editModal').click(e => {
            if (e.target.id==='editModal' || e.target.id==='cancelEdit')
                toggleModal('#editModal', false);
        });

        window.confirmDelete = function(btn){
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then(res=>{
                if(res.isConfirmed) $(btn).closest('form').submit();
            });
        };
    });
</script>
