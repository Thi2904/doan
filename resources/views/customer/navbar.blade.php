<nav>
    <div class="nav__bar">
        <div class="logo">
            <a href="{{route('home') }}">
                <img src="{{ asset('/Logo.png') }}" alt="Hanoi Hotel Logo" id="logo">
            </a>
        </div>
        <div class="nav__menu__btn" id="menu-btn">
            <i class="ri-menu-line"></i>
        </div>
    </div>
    <ul class="nav__links" id="nav-links">
        <li><a href="{{route('home')}}">Trang Chủ</a></li>
        <li><a href="{{route('customer.rooms-type')}}">Hạng Phòng</a></li>
        <li><a href="#cuisine">Ẩm Thực</a></li>
        <li><a href="#offers">Ưu Đãi</a></li>
        <li><a href="#convenience">Tiện Nghi</a></li>
        <li><a href="#meeting_events">Họp & Sự Kiện</a></li>
        <li><a href="#gallery">Thư Viện</a></li>
        <li><a href="#contact">Liên Hệ</a></li>
    </ul>
    @auth
        <div class="user-dropdown">
            <!-- Hiển thị ảnh đại diện và tên người dùng -->
            <div class="user-info">
                <img
                    src="{{
        Auth::user()->avatar_url
            ? Storage::url(Auth::user()->avatar_url)
            : asset('assets/avatar1.jpg')
    }}"
                    alt="Avatar"
                    class="user-avatar"
                />

                <span class="user-name">Xin chào, {{ Auth::user()->name }}</span>
                <i class="dropdown-icon"></i>
            </div>

            <!-- Dropdown menu -->
            <div class="dropdown-menu">
                <a href="{{ route('profile') }}" class="dropdown-item">Hồ sơ</a>
                <form action="{{ route('logout') }}" method="POST" class="dropdown-item">
                    @csrf
                    <button type="submit" class="logout-button">Đăng Xuất</button>
                </form>
            </div>
        </div>
    @else
        <!-- Hiển thị nút Đăng Nhập nếu chưa đăng nhập -->
        <a href="{{ route('login') }}" class="btn nav__btn">Đăng Nhập</a>
    @endauth
</nav>
