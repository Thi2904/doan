<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hạng Phòng - Hanoi Hotel Luxurious accommodations</title>
    <link rel="stylesheet" id="google-fonts-1-css" href="https://fonts.googleapis.com/css?family=Raleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CCormorant+Garamond%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CGothic+A1%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;subset=vietnamese&amp;ver=6.7.1" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/room_type.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<style>
    .header, .section__container, body {
        overflow: visible !important;
    }

    /* ======== DROPDOWN USER (AUTH) ======== */
    .user-dropdown {
        position: relative;
        display: flex;
        align-items: center;
        cursor: pointer;
        z-index: 9999;
    }

    /* Phần hiển thị avatar + tên + icon */
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 2rem;
        border: 1px solid gray;
        transition: background-color 0.3s;
    }

    .user-info:hover {
        background-color: rgba(255,255,255,0.1);
    }

    /* Avatar tròn */
    .user-avatar {
        width: 2rem;
        height: 2rem;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid var(--white);
    }

    /* Tên user */
    .user-name {
        color: var(--text-dark);
        font-size: 0.9rem;
        white-space: nowrap;
    }

    /* Icon mũi tên */
    .dropdown-icon {
        display: inline-block;
        width: 0.8rem;
        height: 0.8rem;
        background-image: url("data:image/svg+xml;charset=UTF-8,<svg fill='%23FFF' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 320 512'><path d='M143 352.3l-136-136c-9.4-9.4-9.4-24.6 0-33.9l22.6-22.6c9.4-9.4 24.6-9.4 33.9 0L160 241l96.4-96.4c9.4-9.4 24.6-9.4 33.9 0l22.6 22.6c9.4 9.4 9.4 24.6 0 33.9l-136 136c-9.5 9.4-24.7 9.4-34.0 0z'/></svg>");
        background-size: contain;
        background-repeat: no-repeat;
        transition: transform 0.3s;
    }

    /* Dropdown menu ẩn mặc định */
    .dropdown-menu {
        position: absolute;
        top: calc(100% + 0.5rem);
        right: 0;
        background-color: var(--primary-color-dark);
        border-radius: 0.5rem;
        box-shadow: 0 0.5rem 1rem rgba(0,0,0,0.2);
        overflow: visible;          /* cho phép nội dung không bị cắt */
        opacity: 0;
        visibility: hidden;
        transform: translateY(-0.5rem);
        transition: opacity 0.3s, transform 0.3s, visibility 0.3s;
        z-index: 10000;             /* cao hơn .user-dropdown để menu nổi trên */
    }

    /* Hiển thị khi .user-dropdown có class .open */
    .user-dropdown.open .dropdown-menu {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }
    .user-dropdown.open .dropdown-icon {
        transform: rotate(180deg);
    }

    /* Các item trong menu */
    .dropdown-item {
        display: block;
        padding: 0.75rem 1.5rem;
        color: var(--white);
        text-decoration: none;
        font-size: 0.9rem;
        transition: background-color 0.2s;
    }
    .dropdown-item:hover {
        background-color: rgba(255,255,255,0.1);
    }

    /* Nút logout */
    .logout-button {
        width: 100%;
        background: none;
        border: none;
        color: inherit;
        font: inherit;
        text-align: left;
        padding: 0;
        cursor: pointer;
    }
</style>
<body>
<header class="header">
    @include('customer.navbar')
</header>

<section class="section__container room-type">
    <h2 class="section__header">Các hạng phòng của chúng tôi</h2>
    <p class="section__description">Phòng nghỉ rộng rãi và tinh tế của Khách sạn Hà Nội là sự lựa
        chọn hoàn hảo dành cho những kỳ nghỉ dưỡng hay chuyến công tác dài ngày tại thủ đô. Mỗi
        phòng đều được trang bị đầy đủ tiện nghi và nội thất, mang lại sự thoải mái và thư giãn
        tuyệt vời.</p>

    <div class="divider"></div>
    <div class="rooms-grid">
        @forelse($roomTypes ?? [] as $type)
            <div class="room-card">
                {{-- Image carousel: show all active images --}}
                <div class="room-images">
                    @foreach($type->images->where('is_active', true) as $img)
                        <img
                            loading="lazy"
                            src="{{ Storage::url($img->image_url) }}"
                            alt="Ảnh phòng {{ $type->name }}"
                        />
                    @endforeach
                </div>
                <div class="room-info">
                    <h3>{{ $type->name }}</h3>
                    <p class="price">{{ number_format($type->base_price,0,',','.') }} VND/đêm</p>
                    <ul class="room-details">
                        <li><i class="ri-group-fill"></i>: {{ $type->max_adult }} Nguời lớn & {{ $type->max_children ?? 0 }} Trẻ em</li>
                        <li><i class="ri-drag-move-2-fill"></i>: {{ $type->area }} m²</li>
                        <li><i class="ri-image-circle-fill"></i>: {{ $type->view }}</li>
                        @if(method_exists($type, 'bedTypes'))
                            <li><i class="ri-hotel-bed-fill"></i>: {{ $type->bedTypes->pluck('name')->join(', ') }}</li>
                        @endif
                    </ul>
                    <p class="desc">{{ $type->short_desc }}</p>
                    <a href="{{ route('rooms.show', $type->id) }}" class="btn">Xem phòng</a>
                </div>
            </div>
        @empty
            <p>Hiện chưa có loại phòng nào. Vui lòng quay lại sau.</p>
        @endforelse
    </div>

</section>

@include('customer.footer')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script src="https://unpkg.com/scrollreveal"></script>
<script src="{{ asset('js/home.js') }}"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
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
