<nav>
    <i class="ri-menu-unfold-2-line toggle-sidebar"></i>
    <form action="#">
        <div class="form-group">
            <input type="text" placeholder="Tìm kiếm...">
            <i class="ri-search-line icon"></i>
        </div>
    </form>
    <span class="divider"></span>
    <div class="profile">
        <img src="{{ asset('assets/avatar1.jpg') }}" alt="">
        <ul class="profile-link">
            <li><a href="#"><i class='bx bxs-user-circle icon'></i> Hồ Sơ</a></li>
            <li><a href="#"><i class='bx bxs-cog'></i> Cài Đặt </a></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit">
                        <i class='bx bxs-log-out-circle'></i> Đăng Xuất
                    </button>
                </form>
            </li>
        </ul>
    </div>
</nav>
