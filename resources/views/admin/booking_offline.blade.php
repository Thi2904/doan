<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Tạo Đơn Đặt Phòng</title>
    <link rel="icon" href="{{ asset('favicon.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<style>
    /* Container chính */
    #adminBookingForm {
        max-width: 1600px;
        margin: 2rem auto;
        padding: 2rem;
        background: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        font-family: "Segoe UI", Roboto, sans-serif;
        color: #2d3748;
        box-sizing: border-box;
    }

    /* Tiêu đề trang */
    .page-title {
        font-size: 1.75rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        text-align: center;
    }

    /* Lưới chung cho form-row */
    .form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .form-row .form-group {
        flex: 1;
        min-width: 200px;
    }

    /* Nhãn và nhóm nhập */
    .form-group {
        margin-bottom: 1.5rem; /* Increased margin for better spacing */
    }

    .form-group label {
        display: block;
        margin-bottom: 0.25rem; /* Reduced margin for compactness */
        font-size: 0.95rem;
        font-weight: 500;
        color: #4a5568;
    }

    /* Inputs & Selects */
    .form-control,
    select.form-control,
    textarea.form-control {
        width: 100%;
        padding: 0.65rem 0.75rem;
        font-size: 0.95rem;
        border: 1px solid #cbd5e0;
        border-radius: 0.25rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
        min-height: 2.6rem;
        line-height: 1.4;
    }

    .form-control:focus,
    select.form-control:focus,
    textarea.form-control:focus {
        outline: none;
        border-color: #3182ce;
        box-shadow: 0 0 0 2px rgba(49, 130, 206, 0.2);
    }

    textarea.form-control {
        resize: vertical;
        min-height: 100px;
    }

    /* Datepicker Inputs */
    .flatpicker {
        cursor: pointer;
        background-color: #ffffff;
        min-height: 2.6rem;
    }

    /* Grid động cho khách & phòng */
    .guests-grid,
    .rooms-grid {
        display: grid;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    @media (min-width: 700px) {
        .guests-grid,
        .rooms-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    /* Card Styles for Dynamic Sections */
    .card,
    .room-card {
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        transition: box-shadow 0.2s;
    }

    .card:hover,
    .room-card:hover {
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
    }

    .room-card {
        background-color: #ffffff;
        border-color: #cbd5e0;
    }

    .card h5,
    .room-card h5 {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.75rem;
        color: #2d3748;
    }

    /* Room Image Styling */
    .room-card .room-img {
        width: 100%;
        max-height: 150px;
        object-fit: cover;
        border-radius: 0.25rem;
        margin-bottom: 0.75rem;
    }

    .room-card .room-details {
        width: 100%;
    }

    .room-card .room-details p {
        margin: 0.25rem 0;
        font-size: 0.9rem;
        color: #4a5568;
    }

    /* Checkbox tuỳ chỉnh */
    .form-check {
        position: relative;
        display: flex;
        align-items: center;
        padding-left: 2rem;
        margin-bottom: 0.75rem;
        cursor: pointer;
        user-select: none;
    }

    .form-check-input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 1.25rem;
        width: 1.25rem;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
    }

    .form-check-label {
        margin: 0;
        font-size: 0.9rem;
        color: #4a5568;
        cursor: pointer;
        transition: color 0.2s;
    }

    .form-check-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid #cbd5e0;
        border-radius: 0.375rem;
        background: #fff;
        transition: border-color 0.2s, background-color 0.2s;
        z-index: 1;
    }

    .form-check-label::after {
        content: '✔';
        position: absolute;
        left: 0.22rem;
        top: 50%;
        transform: translateY(-50%) scale(0);
        font-size: 0.85rem;
        color: #fff;
        transition: transform 0.1s ease-in-out;
        z-index: 3;
    }

    .form-check:hover .form-check-label::before {
        border-color: #3182ce;
    }

    .form-check-input:checked + .form-check-label::before {
        background: #3182ce;
        border-color: #3182ce;
    }

    .form-check-input:checked + .form-check-label::after {
        transform: translateY(-50%) scale(1);
    }

    .form-check-input:focus + .form-check-label::before {
        box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.3);
    }

    /* Submit Button */
    .btn-primary {
        display: inline-block;
        padding: 0.7rem 1.4rem;
        font-size: 1rem;
        color: #ffffff;
        background-color: #3182ce;
        border: none;
        border-radius: 0.25rem;
        cursor: pointer;
        transition: background-color 0.2s, transform 0.1s;
    }

    .btn-primary:hover {
        background-color: #2563eb;
        transform: translateY(-1px);
    }

    /* Mobile Adjustments */
    @media (max-width: 600px) {
        #adminBookingForm {
            padding: 1.5rem;
        }

        .form-row {
            flex-direction: column;
        }

        .guests-grid,
        .rooms-grid {
            grid-template-columns: 1fr;
        }
    }

    /* Nhãn và nhóm nhập */
    .form-group {
        display: flex; /* Use flexbox for alignment */
        align-items: center; /* Center align items vertically */
        margin-bottom: 1.5rem; /* Increased margin for better spacing */
    }

    .form-group label {
        margin-right: 1rem; /* Space between label and input */
        font-size: 0.95rem;
        font-weight: 500;
        color: #4a5568;
        width: 150px; /* Fixed width for all labels */
    }

    /* Inputs & Selects */
    .form-control,
    select.form-control,
    textarea.form-control {
        flex: 1; /* Allow input to take remaining space */
        padding: 0.65rem 0.75rem;
        font-size: 0.95rem;
        border: 1px solid #cbd5e0;
        border-radius: 0.25rem;
        transition: border-color 0.2s, box-shadow 0.2s;
        box-sizing: border-box;
        min-height: 2.6rem;
        line-height: 1.4;
    }
</style>
<body>
@include('admin.sidebar')
<section id="content">
    @include('admin.navbar')
    <main>
        <div class="admin-page">
            <h1 class="page-title">Tạo Đơn Đặt Phòng (Offline)</h1>

            <form action="{{ route('admin.bookings.store') }}" method="POST" id="adminBookingForm">
                @csrf
                <div class="form-group">
                    <label for="guest_name">Tên Khách</label>
                    <input type="text" name="guest_name" id="guest_name" class="form-control" value="{{ old('guest_name') }}" required>
                </div>
                <div class="form-group">
                    <label for="guest_email">Email</label>
                    <input type="email" name="guest_email" id="guest_email" class="form-control" value="{{ old('guest_email') }}" required>
                </div>
                <div class="form-group">
                    <label for="guest_phone">Điện Thoại</label>
                    <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="{{ old('guest_phone') }}" required>
                </div>

                {{-- Ngày nhận & trả --}}
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="check_in_date">Check-in</label>
                        <input type="text" name="check_in" id="check_in_date" class="form-control flatpicker" placeholder="YYYY-MM-DD" autocomplete="off" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="check_out_date">Check-out</label>
                        <input type="text" name="check_out" id="check_out_date" class="form-control flatpicker" placeholder="YYYY-MM-DD" autocomplete="off" required>
                    </div>
                </div>

                {{-- Số phòng & khách --}}
                <div class="form-row align-items-end">
                    <div class="form-group col-md-4">
                        <label for="room_count">Số Phòng</label>
                        <input type="number" name="room_count" id="room_count" class="form-control" min="1" max="10" value="{{ old('room_count',1) }}" required>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Người Lớn Tối Đa / Phòng</label>
                        <input type="text" class="form-control" value="{{ $maxAdults }}" disabled>
                    </div>
                    <div class="form-group col-md-4">
                        <label>Trẻ Em Tối Đa / Phòng</label>
                        <input type="text" class="form-control" value="{{ $maxChildren }}" disabled>
                    </div>
                </div>

                {{-- Khách & phòng động --}}
                <div id="guests-container" class="guests-grid mb-4"></div>
                <div id="rooms-container" class="rooms-grid mb-4"></div>

                {{-- Phương thức thanh toán & ghi chú --}}
                <div class="form-group">
                    <label for="payment_method_id">Phương Thức Thanh Toán</label>
                    <select name="payment_method_id" id="payment_method_id" class="form-control" required>
                        <option value="">— Chọn phương thức —</option>
                        @foreach($pmethods as $id => $method)
                            <option value="{{ $id }}">{{ $method }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Lưu Đơn Đặt</button>
            </form>
        </div>
    </main>
</section>

{{-- Scripts --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Flatpickr
        const today = new Date(); today.setHours(0,0,0,0);
        const checkInPicker = flatpickr('#check_in_date', {
            dateFormat: 'Y-m-d',
            minDate: today,
            disableMobile: true,
            onChange(selectedDates) {
                if (selectedDates[0]) {
                    const next = new Date(selectedDates[0]);
                    next.setDate(next.getDate() + 1);
                    checkOutPicker.set('minDate', next);
                }
            }
        });
        const checkOutPicker = flatpickr('#check_out_date', {
            dateFormat: 'Y-m-d',
            minDate: new Date(today.getTime() + 24*60*60*1000),
            disableMobile: true
        });

        // Dynamic guest & room forms
        const maxA = {{ $maxAdults }}, maxC = {{ $maxChildren }};
        const roomCountInput = document.getElementById('room_count');
        const guestsContainer = document.getElementById('guests-container');
        const roomsContainer  = document.getElementById('rooms-container');

        function buildForms() {
            const cnt = parseInt(roomCountInput.value) || 1;
            guestsContainer.innerHTML = '';
            roomsContainer.innerHTML = '';

            for (let i = 1; i <= cnt; i++) {
                // Guest card
                const guestCard = document.createElement('div');
                guestCard.className = 'card mb-3';
                guestCard.innerHTML = `
                <h5>Khách Phòng ${i}</h5>
                <div class="form-row">
                    <div class="form-group col">
                        <label>Người Lớn</label>
                        <select name="rooms[${i}][adults]" class="form-control" required>
                            ${[...Array(maxA)].map((_,j)=>`<option value="${j+1}">${j+1}</option>`).join('')}
                        </select>
                    </div>
                    <div class="form-group col">
                        <label>Trẻ Em</label>
                        <select name="rooms[${i}][children]" class="form-control" required>
                            ${[...Array(maxC+1)].map((_,j)=>`<option value="${j}">${j}</option>`).join('')}
                        </select>
                    </div>
                </div>`;
                guestsContainer.appendChild(guestCard);

                // Room card
                const roomCard = document.createElement('div');
                roomCard.className = 'card room-card mb-3';
                roomCard.innerHTML = `
                <h5>Phòng ${i}</h5>
                <div class="form-group">
                    <label>Loại Phòng</label>
                    <select name="rooms[${i}][room_type_id]" class="form-control room-type" data-index="${i}" required>
                        @foreach($roomTypes as $t)
                <option value="{{ $t->id }}"
                                data-img="{{ Storage::url($t->images->first()->image_url) }}"
                                data-area="{{ $t->area }}"
                                data-base_price="{{ $t->base_price }}"
                                data-beds="{{ $t->bedTypes->pluck('name')->join(', ') }}">
                            {{ $t->name }}
                </option>
@endforeach
                </select>
            </div>
            <div class="room-details">
                <img src="" class="room-img" alt="Hình Phòng ${i}">
                    <p>Diện tích: <span class="area"></span> m²</p>
                    <p>Giá cơ bản: <span class="base_price"></span>₫/đêm</p>
                    <p>Giường: <span class="beds"></span></p>
                </div>
                <p>Phụ Phí:</p>
                <div class="form-group">
                    @foreach($fees as $f)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="rooms[${i}][fees][]" value="{{ $f->id }}">
                        <label class="form-check-label">{{ $f->name }} ({{ number_format($f->default_price) }}₫)</label>
                    </div>
                    @endforeach
                </div>`;
                roomsContainer.appendChild(roomCard);

                // Attach change listener to update details
                const select = roomCard.querySelector('.room-type');
                const img = roomCard.querySelector('.room-img');
                const area = roomCard.querySelector('.area');
                const basePrice = roomCard.querySelector('.base_price');
                const beds = roomCard.querySelector('.beds');

                function updateRoomInfo() {
                    const opt = select.selectedOptions[0];
                    img.src = opt.dataset.img;
                    area.textContent = opt.dataset.area;
                    basePrice.textContent = new Intl.NumberFormat('vi-VN').format(opt.dataset.base_price);
                    beds.textContent = opt.dataset.beds;
                }

                select.addEventListener('change', updateRoomInfo);
                updateRoomInfo();
            }
        }

        roomCountInput.addEventListener('change', buildForms);
        buildForms();
    });
</script>
</body>
</html>
