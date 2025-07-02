<section id="sidebar">
    <a href="{{ route('admin.panel') }}" class="brand">
        <img src="{{ asset('Logo-1.webp') }}" alt="">
        Hanoi Hotel
    </a>
    <ul class="side-menu">
        <li>
            <a href="{{ route('admin.panel') }}" class="{{ request()->routeIs('admin.panel') ? 'active' : '' }}">
                <i class="ri-dashboard-fill icon"></i> Bảng Điều Khiển
            </a>
        </li>

        <li class="divider" data-text="mục chính">Mục Chính</li>

        <li>
            <a href="#" class="{{ request()->routeIs('rooms.*', 'room_images.*', 'room_types.*', 'bed_types.*', 'features.*') ? 'active' : '' }}">
                <i class="ri-hotel-fill icon"></i> Phòng
                <i class="ri-arrow-right-s-line icon-right"></i>
            </a>
            <ul class="side-dropdown">
                <li><a href="{{ route('rooms.index') }}" class="{{ request()->routeIs('rooms.index') ? 'active' : '' }}">Phòng</a></li>
                <li><a href="{{ route('room_images.index') }}" class="{{ request()->routeIs('room_images.index') ? 'active' : '' }}">Ảnh Phòng</a></li>
                <li><a href="{{ route('room_types.index') }}" class="{{ request()->routeIs('room_types.index') ? 'active' : '' }}">Loại Phòng</a></li>
                <li><a href="{{ route('bed_types.index') }}" class="{{ request()->routeIs('bed_types.index') ? 'active' : '' }}">Loại Giường</a></li>
                <li><a href="{{ route('features.index') }}" class="{{ request()->routeIs('features.index') ? 'active' : '' }}">Tiện Nghi</a></li>
            </ul>
        </li>

        <li>
            <a href="{{ route('admin.customers') }}" class="{{ request()->routeIs('admin.customers') ? 'active' : '' }}">
                <i class="ri-group-fill icon"></i> Khách Hàng
            </a>
        </li>

        <li>
            <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.index') ? 'active' : '' }}">
                <i class="ri-file-list-3-fill icon"></i> Đơn Đặt Phòng
            </a>
        </li>

        <li>
            <a href="{{ route('admin.bookings.create') }}"
               class="{{ request()->routeIs('admin.bookings.create') ? 'active' : '' }}">
                <i class="ri-archive-stack-fill icon"></i>  Tạo Đơn Đặt Mới
            </a>
        </li>

        <li class="divider" data-text="trạng thái & quản lý phòng">Trạng Thái & Quản Lý Phòng</li>

        <li>
            <a href="{{ route('admin.room.matrix') }}" class="{{ request()->routeIs('admin.room.matrix') ? 'active' : '' }}">
                <i class="ri-layout-grid-fill icon"></i> Sơ Đồ Phòng
            </a>
        </li>

        <li>
            <a href="{{ route('admin.room.table_view') }}" class="{{ request()->routeIs('admin.room.table_view') ? 'active' : '' }}">
                <i class="ri-table-fill icon"></i> Bảng Phòng
            </a>
        </li>

        <li>
            <a href="{{ route('admin.booking.calendar') }}" class="{{ request()->routeIs('admin.booking.calendar') ? 'active' : '' }}">
                <i class="ri-calendar-schedule-fill icon"></i> Lịch Đặt Phòng
            </a>
        </li>

        <li>
            <a href="{{ route('room_changes.index') }}" class="{{ request()->routeIs('room_changes.index') ? 'active' : '' }}">
                <i class="ri-exchange-2-fill icon"></i> Đổi Phòng
            </a>
        </li>

        <li class="divider" data-text="chi phí">Chi Phí</li>

        <li>
            <a href="{{ route('payment_methods.index') }}" class="{{ request()->routeIs('payment_methods.index') ? 'active' : '' }}">
                <i class="ri-cash-fill icon"></i> Phương Thức Thanh Toán
            </a>
        </li>
        <li>
            <a href="{{ route('additional_fees.index') }}" class="{{ request()->routeIs('additional_fees.index') ? 'active' : '' }}">
                <i class="ri-currency-fill icon"></i> Phụ Phí
            </a>
        </li>

        <li class="divider" data-text="mục khác">Mục Khác</li>

        <li>
            <a href="{{ route('admin.setting') }}" class="{{ request()->routeIs('admin.setting') ? 'active' : '' }}">
                <i class="ri-settings-5-fill icon"></i> Cài Đặt
            </a>
        </li>
    </ul>
</section>
