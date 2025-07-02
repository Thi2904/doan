<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hanoi Hotel - Đặt Phòng</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/room_type.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="{{ asset('css/user.css') }}">
    <link rel="stylesheet" href="{{ asset('css/booking.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;600;700&family=Cormorant+Garamond:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>
<header class="header">
    @include('customer.navbar')
</header>

<div class="container">
    <h1>Hanoi Hotel – Đặt Phòng</h1>

    <ul class="progressbar">
        <li class="active" data-step="1">Ngày &amp; Khách</li>
        <li data-step="2">Chi tiết phòng</li>
        <li data-step="3">Thanh toán</li>
        <li data-step="4">Xác nhận</li>
    </ul>

    <form id="bookingForm" action="{{ route('bookings.store') }}" method="POST">
        @csrf

        <!-- Step 1 -->
        <!-- Bước 1: chọn ngày & số phòng -->
        <div class="form-step active" data-step="1">
            <div class="card step-card">
                <h2>Bước 1: Chọn ngày &amp; số phòng</h2>
                <div class="step1-grid">
                    <div class="form-group">
                        <label for="checkin">Check‑in</label>
                        <input type="text" id="checkin" name="check_in" class="flatpicker" placeholder="Chọn ngày đến" required>
                    </div>
                    <div class="form-group">
                        <label for="checkout">Check‑out</label>
                        <input type="text" id="checkout" name="check_out" class="flatpicker" placeholder="Chọn ngày rời" required>
                    </div>
                    <div class="form-group">
                        <label for="roomCount">Số phòng</label>
                        <input type="number" id="roomCount" name="room_count" min="1" max="5" value="1" required>
                    </div>
                </div>
                <div id="guests-container" class="guests-grid"></div>
            </div>
            <div class="nav">
                <span></span>
                <button type="button" data-next="2">Tiếp theo →</button>
            </div>
        </div>

        <!-- Step 2 -->
        <div class="form-step" data-step="2">
            <div class="card" id="roomsContainer"></div>
            <div class="nav">
                <button type="button" data-prev="1">Quay lại</button>
                <button type="button" data-next="3">Tiếp theo</button>
            </div>
        </div>

        <!-- Step 3 -->
        @php
            $user = auth()->user();
        @endphp

        <div class="form-step" data-step="3">
            <div class="card">
                <h2>Bước 3: Thanh toán & thông tin</h2>
                <div class="policy" style="margin:1.5rem 0; padding:1rem; background:#fff7e6; border:1px solid #f2d98f; border-radius:6px;">
                    <h3 style="margin-bottom:0.75rem; font-size:1.1rem; color:#6b4f1c;">Chính sách đặt phòng</h3>
                    <ul style="list-style:disc inside; color:#555; line-height:1.5;">
                        <li>Hủy phòng miễn phí nếu báo trước ít nhất 48 giờ trước ngày nhận phòng.</li>
                        <li>Thanh toán cọc 30% giá trị đơn ngay sau khi đặt để giữ chỗ.</li>
                        <li>Phát sinh phí phụ thu nếu trả phòng muộn từ 12:00 trưa & Khách sạn cũng sẽ hoàn phí giá phòng nếu quý khách có việc nếu trả phòng sớm.</li>
                        <li>Nếu không đến nhận phòng trong vòng 6 giờ sau giờ check‑in đã chọn, đặt phòng sẽ tự động bị hủy và không hoàn cọc.</li>
                    </ul>
                </div>

                <label>Phương thức thanh toán</label>
                <select name="payment_method_id" required>
                    <option value="">-- Chọn --</option>
                    @foreach($pmethods as $id => $method)
                        <option value="{{ $id }}">{{ $method }}</option>
                    @endforeach
                </select>

                <label>Tên khách</label>
                <input
                    type="text"
                    name="guest_name"
                    required
                    value="{{ old('guest_name', $user->name ?? '') }}"
                >

                <label>Email</label>
                <input
                    type="email"
                    name="guest_email"
                    required
                    value="{{ old('guest_email', $user->email ?? '') }}"
                >

                <label>Điện thoại</label>
                <input
                    type="text"
                    name="guest_phone"
                    required
                    value="{{ old('guest_phone', $user->phone ?? '') }}"
                >
            </div>
            <div class="nav">
                <button type="button" data-prev="2">Quay lại</button>
                <button type="button" data-next="4">Tiếp theo</button>
            </div>
        </div>


        <!-- Step 4 -->
        <div class="form-step" data-step="4">
            <div class="card summary">
                <h2>Bước 4: Xác nhận</h2>
                <div id="summaryContent" class="summary-grid"></div>
            </div>
            <div class="nav">
                <button type="button" data-prev="3">Quay lại</button>
                <button type="submit">Xác nhận</button>
            </div>
        </div>
    </form>
</div>

<template class="room-template">
    <div>
        <h3>Phòng <span class="room-index"></span></h3>
        <label>Loại phòng</label>
        <select name="rooms[{index}][room_type_id]" class="room-type" required>
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
        <div class="room-details">
            <p>Diện tích: <span class="area"></span> m²</p>
            <p>Giá tiền: <span class="base_price"></span></p>
            <p>Giường: <span class="beds"></span></p>
            <img src="" class="room-img" alt="Hình phòng">
            <p>Phụ phí:</p>
            <div class="fees-group">
                @foreach($fees as $f)
                    <div class="fee-item">
                        <label>
                            <input type="checkbox"
                                   name="rooms[{index}][fees][]"
                                   value="{{ $f->id }}"
                                   data-price="{{ $f->default_price }}">
                            {{ $f->name }} ({{ number_format($f->default_price) }})
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</template>

@include('customer.footer')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Khởi tạo giá trị ngày hôm nay (bỏ giờ)
        const today = new Date();
        today.setHours(0,0,0,0);

        let hasCheckin = false;

        // Flatpickr cho checkout
        const checkoutPicker = flatpickr("#checkout", {
            locale: "vn",
            dateFormat: "Y-m-d",
            disableMobile: true,
            monthSelectorType: "dropdown",
            clickOpens: false,
            minDate: today,
            onReady(selectedDates, dateStr, instance) {
                instance.input.value = ""; // để trống khi chưa chọn
            },
            onOpen(selectedDates, dateStr, instance) {
                // Nếu chưa chọn check-in thì đóng ngay
                if (!hasCheckin) {
                    instance.close();
                }
            }
        });

        // Click vào checkout mới cho mở calendar
        document.querySelector("#checkout").addEventListener("click", () => {
            if (hasCheckin) {
                checkoutPicker.open();
            }
        });

        // Flatpickr cho checkin
        flatpickr("#checkin", {
            locale: "vn",
            dateFormat: "Y-m-d",
            disableMobile: true,
            monthSelectorType: "dropdown",
            defaultDate: today,
            minDate: today,
            onReady(selectedDates, dateStr, instance) {
                instance.input.value = ""; // để trống khi chưa chọn
            },
            onChange(selectedDates) {
                const selected = selectedDates[0];
                if (!selected) return;

                hasCheckin = true;
                // Tính ngày kế tiếp để làm minDate cho checkout
                const nextDay = new Date(selected);
                nextDay.setDate(nextDay.getDate() + 1);

                checkoutPicker.set("minDate", nextDay);
                checkoutPicker.clear(); // xóa giá trị trước đó
            }
        });
    });

    const steps = document.querySelectorAll('.form-step');
    const progressItems = document.querySelectorAll('.progressbar li');
    let curStep = 1;
    const maxA = {{ $maxAdults }};
    const maxC = {{ $maxChildren }};

    function showStep(step) {
        steps.forEach(s => s.classList.toggle('active', +s.dataset.step === step));
        progressItems.forEach((item, i) => item.classList.toggle('active', i < step));
        curStep = step;
        if (step === 1) buildGuests();
        if (step === 2) buildRooms();
        if (step === 4) buildSummary();
    }

    document.querySelectorAll('[data-next]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (validateStep(curStep)) showStep(+btn.dataset.next);
        });
    });
    document.querySelectorAll('[data-prev]').forEach(btn => {
        btn.addEventListener('click', () => showStep(+btn.dataset.prev));
    });

    function buildGuests() {
        const cnt = +document.getElementById('roomCount').value;
        const container = document.getElementById('guests-container');
        container.innerHTML = '';
        for (let i = 0; i < cnt; i++) {
            const div = document.createElement('div');
            div.className = 'card';
            div.innerHTML = `
          <h3>Khách phòng ${i+1}</h3>
          <label>Người lớn</label>
          <select name="rooms[${i}][adults]" required>${[...Array(maxA)].map((_,j)=>`<option value="${j+1}">${j+1}</option>`).join('')}</select>
          <label>Trẻ em</label>
          <select name="rooms[${i}][children]" required>${[...Array(maxC+1)].map((_,j)=>`<option value="${j}">${j}</option>`).join('')}</select>
        `;
            container.appendChild(div);
        }
    }
    document.getElementById('roomCount').addEventListener('change', buildGuests);
    buildGuests();

    function buildRooms() {
        const cnt = +document.getElementById('roomCount').value;
        const container = document.getElementById('roomsContainer');
        container.innerHTML = '';
        const tpl = document.querySelector('.room-template').innerHTML;
        for (let i = 0; i < cnt; i++) {
            let html = tpl.replace(/\{index\}/g, i);
            container.insertAdjacentHTML('beforeend', html);
            const card = container.lastElementChild;
            const type = card.querySelector('.room-type');
            const area = card.querySelector('.area');
            const base_price = card.querySelector('.base_price')
            const beds = card.querySelector('.beds');
            const img = card.querySelector('.room-img');
            function updateInfo() {
                const o = type.selectedOptions[0];
                area.textContent = o.dataset.area;
                // Format giá tại chỗ
                base_price.textContent = new Intl.NumberFormat('vi-VN').format(o.dataset.base_price) + '₫';
                beds.textContent = o.dataset.beds;
                img.src = o.dataset.img;
            }
            type.addEventListener('change', updateInfo);
            updateInfo();
        }
    }

    function buildSummary() {
        const form = document.getElementById('bookingForm');
        const data = new FormData(form);

        // Tính số đêm
        const ci = new Date(data.get('check_in'));
        const co = new Date(data.get('check_out'));
        const nights = (co - ci) / (1000*60*60*24);

        let html = '';

        // Ngày & Số phòng
        html += `
      <div class="summary-block">
        <div class="summary-label">Ngày:</div>
        <div class="summary-value">${data.get('check_in')} → ${data.get('check_out')} (${nights} đêm)</div>
      </div>
      <div class="summary-block">
        <div class="summary-label">Số phòng:</div>
        <div class="summary-value">${data.get('room_count')}</div>
      </div>
    `;

        let grandTotal = 0;

        // Chi tiết từng phòng
        for (let i = 0; i < +data.get('room_count'); i++) {
            const sel = document.querySelector(`select[name="rooms[${i}][room_type_id]"]`).selectedOptions[0];
            const priceNight = parseFloat(sel.dataset.base_price);

            // Tính tổng phòng này
            const roomTotal = priceNight * nights;
            grandTotal += roomTotal;

            // Phòng chính
            html += `
          <div class="summary-room">
            <div class="summary-block">
              <div class="summary-label">Phòng ${i+1}:</div>
              <div class="summary-value">${sel.text} — ${data.get(`rooms[${i}][adults]`)} người lớn, ${data.get(`rooms[${i}][children]`)} trẻ em</div>
            </div>
            <div class="summary-block">
              <div class="summary-label">Giá phòng:</div>
              <div class="summary-value">${new Intl.NumberFormat('vi-VN').format(priceNight)}₫ x ${nights} đêm = ${new Intl.NumberFormat('vi-VN').format(roomTotal)}₫</div>
            </div>
        `;

            // Phụ phí
            const fees = data.getAll(`rooms[${i}][fees][]`);
            if (fees.length) {
                html += `<div class="summary-fees summary-block">
                <div class="summary-label">Phụ phí:</div>
                <div class="summary-value"><ul>`;
                fees.forEach(fid => {
                    const cb = document.querySelector(`#roomsContainer input[name="rooms[${i}][fees][]"][value="${fid}"]`);
                    const feePrice = parseFloat(cb.dataset.price);
                    grandTotal += feePrice;
                    const label = cb.closest('label').textContent.trim();
                    html += `<li>${label} — ${new Intl.NumberFormat('vi-VN').format(feePrice)}₫</li>`;
                });
                html += `</ul></div></div>`;
            }

            html += `</div>`; // close summary-room
        }

        // Tổng kết
        const deposit   = Math.round(grandTotal * 0.3);
        const remaining = grandTotal - deposit;
        html += `
      <div class="summary-total">
        <div class="summary-block">
          <div class="summary-label">Tổng tiền:</div>
          <div class="summary-value">${new Intl.NumberFormat('vi-VN').format(grandTotal)}₫</div>
        </div>
        <div class="summary-block">
          <div class="summary-label">Tiền cọc (30%):</div>
          <div class="summary-value">${new Intl.NumberFormat('vi-VN').format(deposit)}₫</div>
        </div>
        <div class="summary-block">
          <div class="summary-label">Còn lại:</div>
          <div class="summary-value">${new Intl.NumberFormat('vi-VN').format(remaining)}₫</div>
        </div>
        <div class="summary-block">
          <div class="summary-label">Thanh toán:</div>
          <div class="summary-value">${document.querySelector('select[name="payment_method_id"]').selectedOptions[0].text}</div>
        </div>
        <div class="summary-block">
          <div class="summary-label">Khách:</div>
          <div class="summary-value">${data.get('guest_name')} (${data.get('guest_email')})</div>
        </div>
        <div class="summary-block">
          <div class="summary-label">Điện thoại:</div>
          <div class="summary-value">${data.get('guest_phone')}</div>
        </div>
      </div>
    `;

        document.getElementById('summaryContent').innerHTML = html;
    }

    function validateStep(step) {
        const current = document.querySelector(`.form-step[data-step="${step}"]`);
        for (let field of current.querySelectorAll('[required]')) {
            if (!field.value) {
                alert('Vui lòng nhập đầy đủ thông tin trước khi tiếp tục.');
                field.focus();
                return false;
            }
        }
        if (step === 2) {
            const roomCount = +document.getElementById('roomCount').value;
            const countMap = {};
            for (let i = 0; i < roomCount; i++) {
                const sel = document.querySelector(`select[name="rooms[${i}][room_type_id]"]`);
                const id = sel.value, avail = +sel.selectedOptions[0].dataset.available;
                countMap[id] = (countMap[id] || 0) + 1;
                if (countMap[id] > avail) {
                    alert(`Loại phòng "${sel.selectedOptions[0].text}" chỉ còn lại ${avail} phòng.`);
                    return false;
                }
            }
        }
        return true;
    }

    showStep(1);

    document.querySelector('.user-info').addEventListener('click', function(){
        this.closest('.user-dropdown').classList.toggle('open');
    });
    // Optionally: click ngoài đóng dropdown
    document.addEventListener('click', function(e){
        const dropdown = document.querySelector('.user-dropdown');
        if (dropdown && !dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });
</script>
</body>
</html>
