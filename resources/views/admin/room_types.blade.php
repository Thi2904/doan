<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/modal-room_type.css') }}" />
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png" />
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>Quản Lý Loại Phòng - Admin</title>
</head>
<body>
@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')
    <main>
        <h1 class="title">Loại Phòng</h1>
        <ul class="breadcrumbs mb-4">
            <li><a href="{{ route('admin.panel') }}">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Loại Phòng</a></li>
        </ul>
        <div class="data">
            <div class="content-data">
                <div class="head d-flex justify-content-between align-items-center mb-3">
                    <h3>Danh sách loại phòng</h3>
                    <button id="openCreateModal" class="btn-action add" style="background-color: #0C5FCD">
                        <i class="ri-add-line"></i> Thêm Loại Phòng
                    </button>
                </div>
                <table id="roomTypesTable" class="display">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên Loại Phòng</th>
                        <th>Diện Tích (m²)</th>
                        <th>View</th>
                        <th>Giường</th>
                        <th>Người lớn tối đa</th>
                        <th>Trẻ em tối đa</th>
                        <th>Giá Cơ Bản</th>
                        <th>Hành Động</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roomTypes as $roomType)
                        <tr>
                            <td>{{ $roomType->id }}</td>
                            <td>{{ $roomType->name }}</td>
                            <td>{{ $roomType->area }} m²</td>
                            <td>{{ $roomType->view }}</td>
                            <td>
                                @foreach($roomType->bedTypes as $bt)
                                    {{ $bt->name }} x{{ $bt->pivot->quantity }}<br>
                                @endforeach
                            </td>
                            <td>{{ $roomType->max_adult }}</td>
                            <td>{{ $roomType->max_children ?? '-' }}</td>
                            <td>{{ number_format($roomType->base_price, 0, ',', '.') }} VNĐ</td>
                            <td class="actions">
                                <a href="#" class="btn-action view"
                                   data-id="{{ $roomType->id }}"
                                   data-name="{{ $roomType->name }}"
                                   data-area="{{ $roomType->area }}"
                                   data-view="{{ $roomType->view }}"
                                   data-description="{{ $roomType->description }}"
                                data-beds='@json($roomType->bedTypes->map(fn($b)=>["name"=>$b->name,"qty"=>$b->pivot->quantity]))'
                                data-max_adult="{{ $roomType->max_adult }}"
                                data-max_children="{{ $roomType->max_children }}"
                                data-base_price="{{ $roomType->base_price }}"
                                data-features='@json($roomType->features->pluck("name"))'>
                                <i class="ri-eye-line"></i>
                                </a>
                                <a href="#" class="btn-action edit"
                                   data-id="{{ $roomType->id }}"
                                   data-name="{{ $roomType->name }}"
                                   data-area="{{ $roomType->area }}"
                                   data-view="{{ $roomType->view }}"
                                   data-description="{{ $roomType->description }}"
                                data-beds='@json($roomType->bedTypes->pluck("pivot.quantity","id"))'
                                data-max_adult="{{ $roomType->max_adult }}"
                                data-max_children="{{ $roomType->max_children }}"
                                data-base_price="{{ $roomType->base_price }}"
                                data-features='@json($roomType->features->pluck("id"))'>
                                <i class="ri-edit-box-fill"></i>
                                </a>
                                <form action="{{ route('room_types.destroy', $roomType->id) }}" method="POST" style="display:inline-block; margin:0;">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn-action delete"><i class="ri-delete-bin-2-fill"></i></button>
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

<!-- Modal Xem Chi Tiết -->
<div id="modalOverlay" class="modal-overlay-new">
    <div class="modal-box-new">
        <h3>Chi tiết loại phòng</h3>
        <div class="modal-body-new">
            <p><strong>ID:</strong> <span id="roomTypeId"></span></p>
            <p><strong>Tên:</strong> <span id="roomTypeName"></span></p>
            <p><strong>Mô Tả:</strong> <span id="roomTypeDescription"></span></p>
            <p><strong>Diện Tích:</strong> <span id="roomTypeArea"></span> m²</p>
            <p><strong>View:</strong> <span id="roomTypeView"></span></p>
            <p><strong>Giường:</strong></p>
            <ul id="roomTypeBeds" class="modal-list-new"></ul>
            <p><strong>Người lớn tối đa:</strong> <span id="roomTypeMaxAdult"></span></p>
            <p><strong>Trẻ em tối đa:</strong> <span id="roomTypeMaxChildren"></span></p>
            <p><strong>Giá Cơ Bản:</strong> <span id="roomTypeBasePrice"></span></p>
            <p><strong>Tiện nghi:</strong></p>
            <ul id="roomTypeFeatures" class="modal-list-new"></ul>
        </div>
        <div class="modal-footer-new">
            <button id="modalCloseBtn" class="btn-action-new">Đóng</button>
        </div>
    </div>
</div>

<!-- Modal Thêm Mới -->
<div id="createModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Thêm loại phòng mới</h3>
        <form action="{{ route('room_types.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <label for="name"><strong>Tên loại phòng:</strong></label>
                <textarea id="name" name="name" required rows="2" placeholder="Nhập tên loại phòng..">{{ old('name') }}</textarea>
            </div>
            <div class="modal-body">
                <label for="description"><strong>Mô Tả:</strong></label>
                <textarea id="description" name="description" required rows="4" placeholder="Nhập mô tả loại phòng..">{{ old('description') }}</textarea>
            </div>
            <div class="modal-body">
                <label for="area"><strong>Diện Tích (m²):</strong></label>
                <input type="number" id="area" name="area" required step="0.01" value="{{ old('area') }}" placeholder="Nhập diện tích.." />
            </div>
            <div class="modal-body">
                <label for="view"><strong>View:</strong></label>
                <select id="view" name="view" required>
                    <option value="">Chọn view</option>
                    <option value="Hồ" {{ old('view')=='Hồ'?'selected':'' }}>Hồ</option>
                    <option value="Biển" {{ old('view')=='Biển'?'selected':'' }}>Biển</option>
                    <option value="Vườn" {{ old('view')=='Vườn'?'selected':'' }}>Vườn</option>
                </select>
            </div>
            <div class="modal-body">
                <label><strong>Giường:</strong></label>
                <div class="bed-config-row">
                    @foreach($bedTypes as $bt)
                        <div class="bed-config-item">
                            <label>{{ $bt->name }}</label>
                            <input type="number" name="beds[{{ $bt->id }}]" min="0" value="{{ old('beds.'.$bt->id, 0) }}" />
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-body">
                <label for="max_adult"><strong>Người lớn tối đa:</strong></label>
                <input type="number" id="max_adult" name="max_adult" required min="1" value="{{ old('max_adult') }}" />
            </div>
            <div class="modal-body">
                <label for="max_children"><strong>Trẻ em tối đa:</strong></label>
                <input type="number" id="max_children" name="max_children" min="0" value="{{ old('max_children') }}" />
            </div>
            <div class="modal-body">
                <label for="base_price"><strong>Giá Cơ Bản:</strong></label>
                <input type="text" id="base_price" name="base_price" required step="0.01" value="{{ old('base_price') }}" placeholder="Nhập giá cơ bản" />
            </div>
            <div class="modal-body">
                <label><strong>Tiện nghi:</strong></label>
                <div class="checkbox-container">
                    <button type="button" class="toggle-checkbox-btn" id="toggleCreateFeaturesBtn">Hiện tiện nghi</button>
                    <div class="checkbox-group hidden">
                        @foreach($features as $feature)
                            <label class="checkbox-inline">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" {{ in_array($feature->id, old('features', [])) ? 'checked' : '' }} />
                                {{ $feature->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="createCancelBtn" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add"><i class="ri-save-3-line"></i> Lưu</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Chỉnh Sửa -->
<div id="editModalOverlay" class="modal-overlay">
    <div class="modal-box">
        <h3>Chỉnh sửa loại phòng</h3>
        <form action="#" method="POST" id="editRoomTypeForm">
            @csrf
            @method('PUT')

            <div class="modal-body">
                <label for="editName"><strong>Tên loại phòng:</strong></label>
                <textarea id="editName" name="name" required rows="2"></textarea>
            </div>
            <div class="modal-body">
                <label for="editDescription"><strong>Mô Tả:</strong></label>
                <textarea id="editDescription" name="description" required rows="4"></textarea>
            </div>
            <div class="modal-body">
                <label for="editArea"><strong>Diện Tích (m²):</strong></label>
                <input type="number" id="editArea" name="area" required step="0.01" />
            </div>
            <div class="modal-body">
                <label for="editView"><strong>View:</strong></label>
                <select id="editView" name="view" required>
                    <option value="">Chọn view</option>
                    <option value="Hồ">Hồ</option>
                    <option value="Biển">Biển</option>
                    <option value="Vườn">Vườn</option>
                </select>
            </div>
            <div class="modal-body">
                <label><strong>Giường:</strong></label>
                <div class="bed-config-row" id="editBedConfigGroup">
                    @foreach($bedTypes as $bt)
                        <div class="bed-config-item">
                            <label>{{ $bt->name }}</label>
                            <input type="number" name="beds[{{ $bt->id }}]" min="0" />
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-body">
                <label for="editMaxAdult"><strong>Người lớn tối đa:</strong></label>
                <input type="number" id="editMaxAdult" name="max_adult" required min="1" />
            </div>
            <div class="modal-body">
                <label for="editMaxChildren"><strong>Trẻ em tối đa:</strong></label>
                <input type="number" id="editMaxChildren" name="max_children" min="0" />
            </div>
            <div class="modal-body">
                <label for="editBasePrice"><strong>Giá Cơ Bản:</strong></label>
                <input type="text" id="editBasePrice" name="base_price" required step="0.01" />
            </div>
            <div class="modal-body">
                <label><strong>Tiện nghi:</strong></label>
                <div class="checkbox-container">
                    <button type="button" id="toggleEditFeaturesBtn" class="toggle-checkbox-btn">Hiện tiện nghi</button>
                    <div class="checkbox-group hidden" id="editFeaturesGroup">
                        @foreach($features as $feature)
                            <label class="checkbox-inline mr-3">
                                <input type="checkbox" name="features[]" value="{{ $feature->id }}" />
                                {{ $feature->name }}
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="editCancelBtn" class="btn-action">Hủy</button>
                <button type="submit" class="btn-action add"><i class="ri-save-3-line"></i> Lưu</button>
            </div>
        </form>
    </div>
</div>

<script src="{{ asset('js/script.js') }}"></script>
<script>
    $(document).ready(function () {
        $('#roomTypesTable').DataTable({
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.5/i18n/vi.json"
            }
        });

        // === View details ===
        $('#roomTypesTable').on('click', '.btn-action.view', function (e) {
            e.preventDefault();
            const btn = $(this);

            $('#roomTypeId').text(btn.data('id'));
            $('#roomTypeName').text(btn.data('name'));
            $('#roomTypeDescription').text(btn.data('description'));
            $('#roomTypeArea').text(btn.data('area'));
            $('#roomTypeView').text(btn.data('view'));
            $('#roomTypeMaxAdult').text(btn.data('max_adult'));
            $('#roomTypeMaxChildren').text(btn.data('max_children') !== undefined ? btn.data('max_children') : '-');
            $('#roomTypeBasePrice').text(btn.data('base_price'));

            // Giường
            const beds = btn.data('beds') || [];
            const $bedsUl = $('#roomTypeBeds').empty();
            beds.forEach(b => $bedsUl.append($('<li>').text(`${b.name} x${b.qty}`)));

            // Tiện nghi
            const features = btn.data('features') || [];
            const $featUl = $('#roomTypeFeatures').empty();
            features.forEach(f => $featUl.append($('<li>').text(f)));

            $('#modalOverlay').addClass('active');
        });

        $('#modalCloseBtn, #modalOverlay').on('click', function (e) {
            if (e.target.id === 'modalCloseBtn' || e.target.id === 'modalOverlay') {
                $('#modalOverlay').removeClass('active');
            }
        });

        // === Create modal ===
        $('#openCreateModal').click(() => $('#createModalOverlay').addClass('active'));
        $('#createCancelBtn, #createModalOverlay').on('click', function (e) {
            if (e.target.id === 'createCancelBtn' || e.target.id === 'createModalOverlay') {
                $('#createModalOverlay').removeClass('active');
                $('#createModalOverlay form')[0].reset();
            }
        });

        // Toggle tiện nghi modal Create
        $('#toggleCreateFeaturesBtn').on('click', function () {
            const group = $(this).siblings('.checkbox-group');
            group.toggleClass('hidden show');
            $(this).text(group.hasClass('show') ? 'Ẩn tiện nghi' : 'Hiện tiện nghi');
        });

        // === Toggle tiện nghi Edit ===
        $('#toggleEditFeaturesBtn').on('click', function () {
            const group = $('#editFeaturesGroup');
            group.toggleClass('hidden show');
            $(this).text(group.hasClass('show') ? 'Ẩn tiện nghi' : 'Hiện tiện nghi');
        });

        // === Edit modal ===
        $('#roomTypesTable').on('click', '.btn-action.edit', function (e) {
            e.preventDefault();
            const btn = $(this);
            const form = $('#editRoomTypeForm');

            form.attr('action', '/admin/room_types/' + btn.data('id'));
            $('#editName').val(btn.data('name'));
            $('#editArea').val(btn.data('area'));
            $('#editView').val(btn.data('view'));
            $('#editDescription').val(btn.data('description'));
            $('#editMaxAdult').val(btn.data('max_adult'));
            $('#editMaxChildren').val(btn.data('max_children') !== undefined ? btn.data('max_children') : 0);
            $('#editBasePrice').val(btn.data('base_price'));

            let feats;
            try { feats = JSON.parse(btn.attr('data-features')); }
            catch { feats = []; }
            $('#editFeaturesGroup input[type=checkbox]').each(function () {
                $(this).prop('checked', feats.includes(parseInt(this.value)));
            });

            const bedsMap = btn.data('beds') || {};
            $('#editBedConfigGroup input[type=number]').each(function () {
                const id = parseInt($(this).attr('name').match(/\d+/)[0]);
                $(this).val(bedsMap[id] || 0);
            });

            $('#editModalOverlay').addClass('active');
        });

        $('#editCancelBtn, #editModalOverlay').on('click', function (e) {
            if (e.target.id === 'editCancelBtn' || e.target.id === 'editModalOverlay') {
                $('#editModalOverlay').removeClass('active');
                $('#editRoomTypeForm')[0].reset();
            }
        });

        // === Delete confirmation ===
        $('#roomTypesTable').on('click', '.btn-action.delete', function () {
            const btn = this;
            Swal.fire({
                title: 'Bạn có chắc chắn muốn xóa?',
                text: 'Dữ liệu sẽ không thể khôi phục!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then(result => {
                if (result.isConfirmed) {
                    $(btn).closest('form').submit();
                }
            });
        });
    });
</script>
</body>
</html>
