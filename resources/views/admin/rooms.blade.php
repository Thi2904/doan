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
    <title>Quản Lý Phòng - Admin</title>
</head>
<body>
@include('admin.sidebar')

<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Quản Lý Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Phòng</a></li>
        </ul>

        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách phòng</h3>
                    <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                        <i class="ri-add-line"></i> Thêm Phòng
                    </button>
                </div>
                <table id="roomsTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phòng Số</th>
                        <th>Loại Phòng</th>
                        <th>Hành Động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($rooms as $room)
                        <tr>
                            <td>{{ $room->id }}</td>
                            <td>{{ $room->room_number }}</td>
                            <td>{{ $room->roomType->name }}</td>
                            <td class="actions">
                                <!-- View -->
                                <a href="#" class="btn-action view"
                                   data-id="{{ $room->id }}"
                                   data-room_number="{{ $room->room_number }}"
                                   data-room_type_name="{{ $room->roomType->name }}"
                                   data-room_type_id="{{ $room->room_type_id }}">
                                    <i class="ri-eye-line"></i>
                                </a>
                                <!-- Edit -->
                                <a href="#" class="btn-action edit"
                                   data-id="{{ $room->id }}"
                                   data-room_number="{{ $room->room_number }}"
                                   data-room_type_id="{{ $room->room_type_id }}"
                                   data-room_status="{{ $room->room_status }}">
                                    <i class="ri-edit-box-fill"></i>
                                </a>
                                <!-- Delete -->
                                <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" style="display:inline-block; margin:0;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="btn-action delete">
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

<!-- Modal chi tiết phòng -->
<div id="modalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chi tiết phòng</h3>
        <div class="modal-body">
            <p><strong>ID:</strong> <span id="roomId"></span></p>
            <p><strong>Phòng Số:</strong> <span id="roomNumber"></span></p>
            <p><strong>Loại phòng:</strong> <span id="roomTypeName"></span></p>
        </div>
        <div class="modal-footer">
            <button id="modalCloseBtn" class="btn-action">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal tạo phòng mới -->
<div id="createModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Thêm phòng mới</h3>
        <form action="{{ route('rooms.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <label for="room_number"><strong>Phòng Số:</strong></label>
                <input type="text" id="room_number" name="room_number" required placeholder="Nhập số phòng">
            </div>
            <div class="modal-body">
                <label for="room_type_id"><strong>Loại phòng:</strong></label>
                <select id="room_type_id" name="room_type_id" required>
                    <option value="">Chọn loại phòng</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-body">
                <label for="room_status"><strong>Trạng thái phòng:</strong></label>
                <select id="room_status" name="room_status" required>
                    <option value="available">Sẵn sàng</option>
                    <option value="under_maintenance">Đang dọn & sửa</option>
                </select>
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

<!-- Modal chỉnh sửa phòng -->
<div id="editModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa phòng</h3>
        <form action="#" method="POST" id="editRoomForm">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <label for="editRoomNumber"><strong>Phòng Số:</strong></label>
                <input type="text" id="editRoomNumber" name="room_number" required>
            </div>
            <div class="modal-body">
                <label for="editRoomTypeId"><strong>Loại phòng:</strong></label>
                <select id="editRoomTypeId" name="room_type_id" required>
                    <option value="">Chọn loại phòng</option>
                    @foreach($roomTypes as $rt)
                        <option value="{{ $rt->id }}">{{ $rt->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-body">
                <label for="editRoomStatus"><strong>Trạng thái phòng:</strong></label>
                <select id="editRoomStatus" name="room_status" required>
                    <option value="available">Sẵn sàng</option>
                    <option value="under_maintenance">Đang bảo trì</option>
                </select>
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
        // Khởi tạo DataTable
        $('#roomsTable').DataTable({
            language: {
                url: 'https://cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json'
            }
        });

        // === Xem chi tiết phòng ===
        $('#roomsTable').on('click', '.btn-action.view', function (e) {
            e.preventDefault();
            const btn = $(this);
            $('#roomId').text(btn.data('id'));
            $('#roomNumber').text(btn.data('room_number'));
            $('#roomTypeName').text(btn.data('room_type_name'));
            const statusMap = {
                'available': 'Sẵn sàng',
                'booked': 'Đã đặt',
                'under_maintenance': 'Đang bảo trì'
            };
            const statusKey = btn.data('room_status');
            const statusText = statusMap[statusKey] || 'Không rõ';
            $('#roomStatus').text(statusText);
            $('#modalOverlay').addClass('active');
        });

        $('#modalCloseBtn, #modalOverlay').on('click', function(e){
            if (e.target.id === 'modalCloseBtn' || e.target.id === 'modalOverlay') {
                $('#modalOverlay').removeClass('active');
            }
        });

        // === Thêm phòng mới ===
        $('#openCreateModal').on('click', () => $('#createModalOverlay').addClass('active'));
        $('#createCancelBtn, #createModalOverlay').on('click', function(e){
            if (e.target.id === 'createCancelBtn' || e.target.id === 'createModalOverlay') {
                $('#createModalOverlay').removeClass('active');
            }
        });

        // === Chỉnh sửa phòng ===
        $('#roomsTable').on('click', '.btn-action.edit', function (e) {
            e.preventDefault();
            const btn = $(this);
            const roomStatus = btn.data('room_status');

            if (roomStatus === 'booked') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Không thể chỉnh sửa',
                    text: 'Phòng đang được đặt và không thể chỉnh sửa!',
                    confirmButtonText: 'OK'
                });
                return;
            }

            const form = $('#editRoomForm');
            form.attr('action', '/admin/rooms/' + btn.data('id'));
            $('#editRoomNumber').val(btn.data('room_number'));
            $('#editRoomTypeId').val(btn.data('room_type_id'));
            $('#editRoomStatus').val(roomStatus);
            $('#editModalOverlay').addClass('active');
        });

        $('#editCancelBtn, #editModalOverlay').on('click', function(e){
            if (e.target.id === 'editCancelBtn' || e.target.id === 'editModalOverlay') {
                $('#editModalOverlay').removeClass('active');
                $('#editRoomForm')[0].reset();
            }
        });

        // === Xóa phòng ===
        $('#roomsTable').on('click', '.btn-action.delete', function () {
            const btn = this;
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa phòng này?',
                text: "Hành động này không thể hoàn tác!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Có, xóa!',
                cancelButtonText: 'Không, hủy!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $(btn).closest('form').submit();
                }
            });
        });
    });
</script>
</body>
</html>
