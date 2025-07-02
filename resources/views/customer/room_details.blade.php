<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hạng Phòng - Hanoi Hotel Luxurious accommodations</title>
    <link rel="stylesheet" id="google-fonts-1-css" href="https://fonts.googleapis.com/css?family=Raleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CCormorant+Garamond%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CGothic+A1%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;subset=vietnamese&amp;ver=6.7.1" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/room_type.css') }}">
    <link rel="stylesheet" href="{{ asset('css/nav.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<style>
    .header {
        position: relative;  /* hoặc fixed nếu bạn cần header luôn cố định */
        z-index: 1000;       /* cao hơn hero */
    }

    .header, body {
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

    .hero, .hero-content {
        overflow: visible !important;
    }

</style>
<body>
<header class="header">
    @include('customer.navbar')
</header>
    <!-- Hero Section -->

<section class="hero">
    <img
        loading="lazy"
        class="hero-background"
        src="{{ $heroImage ? Storage::url($heroImage) : asset('images/default-room.jpg') }}"
        alt="Ảnh phòng {{ $roomType->name }}"
    />
    <div class="hero-content">
        <h1>{{ $roomType->name }}</h1>
        <p>Live Oriental Heritage</p>
    </div>
</section>
<main class="main-content">
    <!-- Room Details -->
    <section class="section__container">
        <h2 class="section__header">Chi tiết phòng</h2>
        <div class="room-details">
            <!-- Icon 1: Giường -->
            <div class="room-detail">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" viewBox="0 0 512 512" xml:space="preserve">
          <g>
              <path d="M496 344h-8v-64a32.042 32.042 0 0 0-32-32V112a32.042 32.042 0 0 0-32-32H88a32.042 32.042 0 0 0-32 32v136a32.042 32.042 0 0 0-32 32v64h-8a8 8 0 0 0-8 8v32a8 8 0 0 0 8 8h8v32a8 8 0 0 0 8 8h24a7.99 7.99 0 0 0 7.84-6.43L70.56 392h370.88l6.72 33.57A7.99 7.99 0 0 0 456 432h24a8 8 0 0 0 8-8v-32h8a8 8 0 0 0 8-8v-32a8 8 0 0 0-8-8ZM72 112a16.021 16.021 0 0 1 16-16h336a16.021 16.021 0 0 1 16 16v136h-16v-32a32.042 32.042 0 0 0-32-32h-96a32.042 32.042 0 0 0-32 32v32h-16v-32a32.042 32.042 0 0 0-32-32h-96a32.042 32.042 0 0 0-32 32v32H72Zm336 104v32H280v-32a16.021 16.021 0 0 1 16-16h96a16.021 16.021 0 0 1 16 16Zm-176 0v32H104v-32a16.021 16.021 0 0 1 16-16h96a16.021 16.021 0 0 1 16 16ZM40 280a16.021 16.021 0 0 1 16-16h400a16.021 16.021 0 0 1 16 16v64H40Zm9.44 136H40v-24h14.24ZM472 416h-9.44l-4.8-24H472Zm16-40H24v-16h464Z"/>
          </g>
        </svg>
                <span>{{ $roomType->bedTypes->pluck('name')->join(', ') }}</span>
            </div>

            <!-- Icon 2: Người -->
            <div class="room-detail">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" viewBox="0 0 490.667 490.667" xml:space="preserve">
          <g>
              <path d="M244.587 241.557c32.128-18.389 54.08-52.629 54.08-92.224 0-58.816-47.851-106.667-106.667-106.667S85.333 90.517 85.333 149.333c0 39.595 21.952 73.835 54.08 92.224C59.051 262.656 0 330.603 0 411.136v26.197C0 443.221 4.779 448 10.667 448s10.667-4.779 10.667-10.667v-26.197C21.333 325.611 97.899 256 192 256s170.667 69.611 170.667 155.136v26.197c0 5.888 4.779 10.667 10.667 10.667s10.667-4.779 10.667-10.667v-26.197c-.001-80.533-59.052-148.501-139.414-169.579zm-137.92-92.224C106.667 102.272 144.939 64 192 64s85.333 38.272 85.333 85.333-38.272 85.333-85.333 85.333-85.333-38.271-85.333-85.333z"/>
              <path d="M388.224 241.835c23.125-15.296 38.443-41.451 38.443-71.168 0-47.061-38.272-85.333-85.333-85.333-5.888 0-10.667 4.779-10.667 10.667s4.779 10.667 10.667 10.667c35.285 0 64 28.715 64 64s-28.715 64-64 64c-5.888 0-10.667 4.779-10.667 10.667S335.445 256 341.333 256c70.592 0 128 53.056 128 118.293v20.373c0 5.888 4.779 10.667 10.667 10.667s10.667-4.779 10.667-10.667v-20.373c0-61.653-43.008-114.026-102.443-132.458z"/>
          </g>
        </svg>
                <span>{{ $roomType->max_adult }} Người lớn &amp; {{ $roomType->max_children ?? 0 }} Trẻ em</span>
            </div>

            <!-- Icon 3: Diện tích -->
            <div class="room-detail">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M93.867 426.667h409.6c5.12 0 8.533-3.413 8.533-8.533V8.533C512 3.413 508.587 0 503.467 0H93.866c-5.12 0-8.533 3.413-8.533 8.533V418.133c0 5.12 3.414 8.534 8.534 8.534zm8.533-136.534h85.333c-4.267 45.227-40.107 81.067-85.333 85.333v-85.333zm0-273.066h136.533v51.2H230.4c-5.12 0-8.533 3.413-8.533 8.533s3.413 8.533 8.533 8.533h34.133c5.12 0 8.533-3.413 8.533-8.533s-3.413-8.533-8.533-8.533H256v-51.2h76.8v93.867c0 5.12 3.413 8.533 8.533 8.533h34.133V128c0 5.12 3.413 8.533 8.533 8.533s8.533-3.413 8.533-8.533V93.867c0-5.12-3.413-8.533-8.533-8.533s-8.533 3.413-8.533 8.533v8.533h-25.6V17.067h145.067V102.4H460.8v-8.533c0-5.12-3.413-8.533-8.533-8.533-5.12 0-8.533 3.413-8.533 8.533V128c0 5.12 3.413 8.533 8.533 8.533 5.12 0 8.533-3.413 8.533-8.533v-8.533h34.133V256h-51.2v-8.533c0-5.12-3.413-8.533-8.533-8.533s-8.533 3.413-8.533 8.533V281.6c0 5.12 3.413 8.533 8.533 8.533s8.533-3.413 8.533-8.533v-8.533h51.2V409.6H307.2V273.067h51.2v8.533c0 5.12 3.413 8.533 8.533 8.533s8.533-3.413 8.533-8.533v-34.133c0-5.12-3.413-8.533-8.533-8.533s-8.533 3.413-8.533 8.533V256h-59.733c-5.12 0-8.533 3.413-8.533 8.533V409.6H102.4v-17.388c57.425-4.332 102.4-52.044 102.4-110.612 0-5.12-3.413-8.533-8.533-8.533H102.4v-51.2h145.067c5.12 0 8.533-3.413 8.533-8.533V153.6h8.533c5.12 0 8.533-3.413 8.533-8.533 0-5.12-3.413-8.533-8.533-8.533H230.4c-5.12 0-8.533 3.413-8.533 8.533 0 5.12 3.413 8.533 8.533 8.533h8.533v51.2H102.4V17.067zM65.707 424.107c3.413-3.413 3.413-8.533 0-11.947-3.413-3.413-8.533-3.413-11.947 0l-11.093 11.093V29.013L53.76 40.107c1.707 1.707 3.413 2.56 5.973 2.56s4.267-.853 5.973-2.56c3.413-3.413 3.413-8.533 0-11.947L41.567 4.02C40.172 1.511 37.532 0 34.133 0s-6.038 1.511-7.434 4.02L2.56 28.16c-3.413 3.413-3.413 8.533 0 11.947s8.533 3.413 11.947 0L25.6 29.013v394.24L14.507 412.16c-3.413-3.413-8.533-3.413-11.947 0-3.413 3.413-3.413 8.533 0 11.947l24.14 24.14c1.395 2.509 4.034 4.02 7.434 4.02 3.399 0 6.038-1.511 7.433-4.02l24.14-24.14zM512 477.874v-.016c-.002-3.395-1.512-6.031-4.02-7.426l-24.14-24.14c-3.413-3.413-8.533-3.413-11.947 0-3.413 3.413-3.413 8.533 0 11.947l11.093 11.093H88.747L99.84 458.24c3.413-3.413 3.413-8.533 0-11.947-3.413-3.413-8.533-3.413-11.947 0l-24.14 24.14c-2.509 1.395-4.02 4.034-4.02 7.434s1.511 6.038 4.02 7.434l24.14 24.14c1.707 1.707 3.413 2.56 5.973 2.56s4.267-.853 5.973-2.56c3.413-3.413 3.413-8.533 0-11.947L88.747 486.4h394.24l-11.093 11.093c-3.413 3.413-3.413 8.533 0 11.947 1.707 1.707 3.413 2.56 5.973 2.56s4.267-.853 5.973-2.56l24.14-24.14c2.507-1.394 4.017-4.03 4.02-7.426z" fill="" opacity="1" data-original="#000000" class=""></path></g></svg>
                <span>{{ number_format($roomType->area,0,',','.') }} m²</span>
            </div>

            <!-- Icon 4: Hướng hồ -->
            <div class="room-detail">
                <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="512" height="512" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M388 367c-1 0-2-1-4-1-3-2-4-6-2-10 11-20 17-44 17-68 0-79-64-143-143-143s-143 64-143 143c0 24 6 48 17 68 2 4 1 8-2 10-4 2-8 0-10-3-12-23-19-49-19-75 0-86 70-157 157-157s157 71 157 157c0 26-7 52-19 75-1 2-4 4-6 4z" fill="" opacity="1" data-original="#000000" class=""></path><path d="M256 145c-4 0-7-3-7-7V82c0-3 3-7 7-7s7 4 7 7v56c0 4-3 7-7 7zM362 189c-2 0-4-1-5-2-3-3-3-7 0-10l40-39c2-3 7-3 9 0 3 2 3 7 0 10l-39 39c-1 1-3 2-5 2zM462 295h-56c-4 0-7-3-7-7s3-7 7-7h56c4 0 7 3 7 7s-3 7-7 7zM106 295H50c-4 0-7-3-7-7s3-7 7-7h56c4 0 7 3 7 7s-3 7-7 7zM150 189c-2 0-4-1-5-2l-39-39c-3-3-3-8 0-10 2-3 7-3 9 0l40 39c3 3 3 7 0 10-1 1-3 2-5 2zM295 150h-2c-4-1-6-5-5-8l7-27c1-4 5-6 9-5s6 4 5 8l-7 27c-1 3-4 5-7 5zM331 165c-1 0-2 0-4-1-3-2-4-6-2-9l14-24c2-4 6-5 9-3 4 2 5 6 3 10l-14 24c-1 2-4 3-6 3zM386 220c-3 0-5-1-6-3-2-4-1-8 2-10l25-14c3-2 7-1 9 3 2 3 1 7-2 9l-25 14c-1 1-2 1-3 1zM401 256c-3 0-6-2-7-5-1-4 1-7 5-8l27-8c4-1 8 2 9 5 1 4-2 8-5 9l-27 7h-2zM428 341h-2l-27-7c-4-1-6-5-5-9s5-6 9-5l27 7c3 1 6 5 5 9-1 3-4 5-7 5zM84 341c-3 0-6-2-7-5-1-4 2-8 5-9l27-7c4-1 8 1 9 5s-1 8-5 9l-27 7h-2zM111 256h-2l-27-7c-3-1-6-5-5-9 1-3 5-6 9-5l27 8c4 1 6 4 5 8-1 3-4 5-7 5zM126 220c-1 0-2 0-3-1l-25-14c-3-2-4-6-2-9 2-4 6-5 9-3l25 14c3 2 4 6 2 10-1 2-3 3-6 3zM181 165c-2 0-5-1-6-3l-14-24c-2-4-1-8 3-10 3-2 7-1 9 3l14 24c2 3 1 7-2 9-2 1-3 1-4 1zM217 150c-3 0-6-2-7-5l-7-27c-1-4 1-7 5-8s8 1 9 5l7 27c1 3-1 7-5 8h-2zM505 367H7c-4 0-7-3-7-7s3-7 7-7h498c4 0 7 3 7 7s-3 7-7 7zM72 124c-2 0-4-1-6-3-14-19-33-9-35-8-3 2-7 1-9-3-2-3-1-7 2-9 8-5 24-9 39-1 2-17 15-28 23-31 4-2 8 0 9 3 2 4 0 8-3 9-2 1-21 11-14 33 1 4 0 7-3 9-1 0-2 1-3 1zM440 124c-1 0-2-1-3-1-3-2-4-5-3-9 7-23-13-32-14-33-3-1-5-5-3-9 1-3 5-5 9-3 8 3 21 14 23 31 15-8 31-4 39 1 3 2 4 6 2 9-2 4-6 5-9 3-2-1-21-11-35 8-2 2-4 3-6 3zM477 405H35c-4 0-7-3-7-7 0-3 3-7 7-7h442c4 0 7 4 7 7 0 4-3 7-7 7zM429 444H83c-4 0-7-3-7-7s3-7 7-7h346c4 0 7 3 7 7s-3 7-7 7z" fill="" opacity="1" data-original="#000000" class=""></path></g></svg>
                <span>Hướng {{ $roomType->view }} </span>
            </div>
        </div>
    </section>

    <div class="section_container">
        <div class="description__container" role="main">
            <h1>{{ $roomType->name }}</h1>
            <p style="font-weight: 500; font-style: italic; font-size: 1.6rem; ">Nghỉ dưỡng hoàn hảo</p>
            <p style="font-style: italic">" {{ $roomType->description }} "</p>

            <a href="{{ route('bookings.create', ['room_type' => $roomType->id]) }}"
               class="booking-button">
            Đặt phòng ngay
            </a>
        </div>
        <div class="section__image">
            <img
                loading="lazy"
                src="{{ $secondImage ? Storage::url($secondImage) : asset('images/default-room.jpg') }}"
                alt="Ảnh khác của phòng {{ $roomType->name }}"
            />
        </div>
    </div>

    <div class="section__container feature__container">
        <h2 class="features__title">Các tiện nghi</h2>
        <ul class="features__list">
            @foreach($features as $feature)
                <li class="feature__item">
                    <i class="fa-solid fa-circle-check"></i> {{ $feature->name }}
                </li>
            @endforeach
        </ul>
    </div>

    <div class="section__container room__gallery">
        <h2 class="">Các hình ảnh liên quan đến phòng</h2>
        <div class="masonry-grid">
            @foreach($roomType->images as $image)
                <div class="masonry-item">
                    <img src="{{ Storage::url($image->image_url) }}" alt="Ảnh phòng" />
                </div>
            @endforeach
        </div>

        <!-- Lightbox Modal -->
        <div id="lightbox">
            <span class="lightbox-close">&times;</span>
            <img class="lightbox-img" src="" alt="Lightbox" />
            <a class="lightbox-prev">&#10094;</a>
            <a class="lightbox-next">&#10095;</a>
        </div>
    </div>
    <!-- Gợi ý thêm -->
    <div class="container">
        <div class="section__container more__options">
            <h2>Xem thêm các lựa chọn</h2>
            <div class="options__grid">
                @foreach($otherRoomTypes as $other)
                    <div class="option__card">
                        <img src="{{ optional($other->images->first())->image_url ? Storage::url($other->images->first()->image_url) : asset('images/default-room.jpg') }}" alt="Ảnh phòng {{ $other->name }}">

                        <div class="option__card-content">
                            <h4 class="room__title">{{ $other->name }}</h4>
                            <p class="room__info">{{ $other->max_adult }} Người lớn & {{ $other->max_children ?? 0 }} Trẻ em / {{ number_format($other->area,0,',','.') }} m<sup>2</sup></p>
                            <p class="room__desc">{{ $other->description }}</p>
                            <a class="room__link" href="{{ route('rooms.show', $other->id) }}">Xem phòng</a>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Paginate dạng chấm tròn --}}
            @if ($otherRoomTypes->lastPage() > 1)
                <div class="dot-pagination">
                    @for ($i = 1; $i <= $otherRoomTypes->lastPage(); $i++)
                        <a href="{{ $otherRoomTypes->url($i) }}"
                           class="dot {{ $otherRoomTypes->currentPage() == $i ? 'active' : '' }}">
                        </a>
                    @endfor
                </div>
            @endif
        </div>
    </div>


</main>
@include('customer.chatbox')
@include('customer.footer')
</body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Lấy tất cả ảnh trong masonry
        const items = Array.from(document.querySelectorAll('.masonry-item img'));
        const lightbox = document.getElementById('lightbox');
        const lightboxImg = document.querySelector('.lightbox-img');
        const btnClose = document.querySelector('.lightbox-close');
        const btnPrev  = document.querySelector('.lightbox-prev');
        const btnNext  = document.querySelector('.lightbox-next');

        let currentIndex = 0;

        // Gán ký tự mũi tên nếu chưa có
        btnPrev.textContent = btnPrev.textContent.trim() || '\u2039';  // ‹
        btnNext.textContent = btnNext.textContent.trim() || '\u203A';  // ›

        // Mở lightbox với ảnh tại index
        function openLightbox(index) {
            currentIndex = index;
            lightboxImg.src = items[currentIndex].src;
            lightbox.classList.add('active');
            document.body.style.overflow = 'hidden'; // Ẩn scroll khi lightbox mở
        }

        // Đóng lightbox
        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Hiển thị ảnh theo index, wrap-around
        function showImage(index) {
            if (index < 0) index = items.length - 1;
            if (index >= items.length) index = 0;
            currentIndex = index;
            lightboxImg.src = items[currentIndex].src;
        }

        // Click vào ảnh để mở lightbox
        items.forEach((img, idx) => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', () => openLightbox(idx));
        });

        // Đóng khi click nút X hoặc click ngoài ảnh
        btnClose.addEventListener('click', (e) => {
            e.preventDefault();
            closeLightbox();
        });
        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) closeLightbox();
        });

        // Prev / Next — thêm preventDefault để <a> không nhảy trang
        btnPrev.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            showImage(currentIndex - 1);
        });
        btnNext.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            showImage(currentIndex + 1);
        });

        // Bắt phím ESC để đóng, mũi tên để chuyển ảnh
        document.addEventListener('keydown', (e) => {
            if (!lightbox.classList.contains('active')) return;
            switch (e.key) {
                case 'Escape':
                    closeLightbox();
                    break;
                case 'ArrowLeft':
                    showImage(currentIndex - 1);
                    break;
                case 'ArrowRight':
                    showImage(currentIndex + 1);
                    break;
            }
        });
    });

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
</html>
