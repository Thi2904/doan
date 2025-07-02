<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang chủ - Hanoi Hotel Luxurious accommodations</title>
    <link rel="stylesheet" id="google-fonts-1-css" href="https://fonts.googleapis.com/css?family=Raleway%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CCormorant+Garamond%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic%7CGothic+A1%3A100%2C100italic%2C200%2C200italic%2C300%2C300italic%2C400%2C400italic%2C500%2C500italic%2C600%2C600italic%2C700%2C700italic%2C800%2C800italic%2C900%2C900italic&amp;display=swap&amp;subset=vietnamese&amp;ver=6.7.1" type="text/css" media="all">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

</head>
<style>
    * {
        font-family: 'Raleway', sans-serif;
    }

</style>
<body>

<header class="header">
    <div class="header__slideshow" id="slideshow">
        @foreach (['header-banner1.webp', 'header-banner2.webp', 'header-banner3.webp'] as $image)
            <div class="header__slide"
                 style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('{{ asset("assets/{$image}") }}'); background-size: cover; background-position: center;">
            </div>
        @endforeach
    </div>
    @include('customer.navbar')
    <div class="section__container header__container" id="home">
        <h2>Hanoi Hotel</h2>
        <p>Live Oriental Heritage</p>
    </div>
    @php
        $action = route('bookings.create');
    @endphp
    <section class="section__container booking_container">
        <form action="{{ $action }}" method="GET" class="booking-form">
            <div class="input__group">
                <label>
                    <span><i class="ri-calendar-schedule-line"></i></span>
                    NHẬN PHÒNG *
                </label>
                <input type="text" placeholder="Ngày nhận phòng" id="checkin" name="checkin" class="datepicker" required />
            </div>
            <div class="input__group">
                <label>
                    <span><i class="ri-calendar-check-line"></i></span>
                    TRẢ PHÒNG *
                </label>
                <input type="text" placeholder="Ngày trả phòng" id="checkout" name="checkout" class="datepicker" required />
            </div>
            <div class="input__group">
                <label>
                    <span><i class="ri-user-2-fill"></i></span>
                    NGƯỜI LỚN *
                </label>
                <select name="adults" required>
                    @for ($i = 0; $i <= 4; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="input__group">
                <label>
                    <span><i class="ri-user-5-fill"></i></span>
                    TRẺ EM
                </label>
                <select name="children">
                    @for ($i = 0; $i <= 2; $i++)
                        <option value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
            <div class="input__btn">
                <button class="btn" type="submit">Đặt Phòng</button>
            </div>
        </form>
    </section>
</header>
<section class="section__container about__container" id="about">
    <div class="about__image">
        <img src="{{ asset('assets/home1.webp') }}" alt="">
    </div>
    <div class="about__content">
        <p class="section__subheader">
            Lời Chào
        </p>
        <h2 class="section__header">
            Chào mừng Quý khách đến với Khách sạn Hà Nội
        </h2>
        <p class="section__description">
            Khách sạn Hà Nội là khách sạn Quốc tế đầu tiên tại Hà Nội với 218 phòng nghỉ tiện nghi,
            hiện đại và sang trọng. Đặc biệt, sở hữu vị trí trung tâm bên Hồ Giảng Võ thanh bình, kết
            nối thuận tiện với các văn phòng chính phủ, đại sứ quán, khu thương mại sầm suất, nhà
            hàng,… Khách sạn là điểm dừng chân lý tưởng cho du khách trong và ngoài nước mỗi khi có
            chuyến công tác hay du lịch cùng bạn bè và người thân. Bên cạnh đó, Khách sạn Hà Nội nổi
            tiếng là địa chỉ hàng đầu về ẩm thực Trung Hoa cùng các dịch vụ giải trí phong phú, hy
            vọng sẽ đem lại cho Quý khách những trải nghiệm thú vị và hài lòng nhất.
        </p>
    </div>
</section>

<section class="section__container offers__container">
    <p class="section__subheader">Ưu đãi đặc biệt</p>
    <h2 class="section__header">Bất ngờ gì đang chờ đợi bạn?</h2>
    <p class="section__description">
        Tọa lạc tại khu vực trung tâm thủ đô, Khách sạn Hà Nội mang đến không gian lưu trú tiện nghi
        và sang trọng bên hồ Giảng Võ yên bình với nhiều hạng phòng đa dạng, đáp ứng mọi nhu cầu của
        Quý khách, dù là trong kỳ nghỉ dưỡng hay chuyến công tác dài ngày. Hãy đến và trải nghiệm những
        dịch vụ hấp dẫn tại khách sạn Hà Nội!
    </p>

    <div class="swiper offers__swiper">
        <div class="swiper-wrapper">
            <!-- Slide 1 -->
            <div class="swiper-slide">
                <div class="offers__card">
                    <div class="offers__card__image">
                        <img src="{{ asset('assets/offers1.jpg') }}" alt="Autumn Indulgence">
                    </div>
                    <div class="offers__card__content">
                        <h3>Hương vị mùa thu ngọt ngào</h3>
                        <p>
                            Cùng hòa mình vào bầu không khí trong lành, mát mẻ của mùa thu Hà Nội, nơi...
                        </p>
                        <a href="#" class="offers__card__link">Xem thêm</a>
                    </div>
                </div>
            </div>

            <!-- Slide 2 -->
            <div class="swiper-slide">
                <div class="offers__card">
                    <div class="offers__card__image">
                        <img src="{{ asset('assets/offers2.png') }}" alt="Chef Gift">
                    </div>
                    <div class="offers__card__content">
                        <h3>Dimsum thả ga – Nhận quà từ Bếp trưởng</h3>
                        <p>
                            Chào đón cuối tuần với bữa tiệc vị giác đỉnh cao tại Nhà hàng Golden Dragon!...
                        </p>
                        <a href="#" class="offers__card__link">Xem thêm</a>
                    </div>
                </div>
            </div>

            <!-- Slide 3 -->
            <div class="swiper-slide">
                <div class="offers__card">
                    <div class="offers__card__image">
                        <img src="{{ asset('assets/offers3.jpg') }}" alt="Dimsum Hong Kong">
                    </div>
                    <div class="offers__card__content">
                        <h3>Dimsum Hồng Kông – Mê không lối về</h3>
                        <p>
                            Thưởng thức đại tiệc Buffet Dimsum hào hạng với hơn 60 món hấp dẫn, chỉ từ...
                        </p>
                        <a href="#" class="offers__card__link">Xem thêm</a>
                    </div>
                </div>
            </div>

            <div class="swiper-slide">
                <div class="offers__card">
                    <div class="offers__card__image">
                        <img src="{{ asset('assets/offers3.jpg') }}" alt="Dimsum Hong Kong">
                    </div>
                    <div class="offers__card__content">
                        <h3>Dimsum Hồng Kông – Mê không lối về</h3>
                        <p>
                            Thưởng thức đại tiệc Buffet Dimsum hào hạng với hơn 60 món hấp dẫn, chỉ từ...
                        </p>
                        <a href="#" class="offers__card__link">Xem thêm</a>
                    </div>
                </div>
            </div>

        </div>

        <!-- Pagination -->
        <div class="swiper-pagination"></div>
    </div>

</section>
@include('customer.chatbox')
@include('customer.footer')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script src="https://unpkg.com/scrollreveal"></script>
<script src="{{ asset('js/home.js') }}"></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date();
        today.setHours(0,0,0,0);

        // Checkout picker
        let hasCheckin = false;
        const checkoutPicker = flatpickr("#checkout", {
            locale: "vn",
            dateFormat: "Y-m-d",      // dùng Y-m-d để dễ đọc trong URL
            disableMobile: true,
            monthSelectorType: "dropdown",
            clickOpens: false,
            onReady(_, __, inst) {
                inst.input.value = "";
            },
            onOpen(_, __, inst) {
                if (!hasCheckin) inst.close();
            }
        });
        document.querySelector("#checkout").addEventListener("click", () => {
            if (hasCheckin) checkoutPicker.open();
        });

        // Checkin picker
        flatpickr("#checkin", {
            locale: "vn",
            dateFormat: "Y-m-d",      // dùng Y-m-d để GET ra e.g. ?checkin=2025-05-09
            disableMobile: true,
            monthSelectorType: "dropdown",
            defaultDate: today,
            minDate: today,
            onReady(_, __, inst) {
                inst.input.value = "";
            },
            onChange([selected]) {
                if (!selected) return;
                hasCheckin = true;
                const nextDay = new Date(selected);
                nextDay.setDate(nextDay.getDate() + 1);
                checkoutPicker.set("minDate", nextDay);
                checkoutPicker.clear();
            }
        });
    });

    document.addEventListener('DOMContentLoaded', () => {
        const slides = document.querySelectorAll('.header__slide');
        let currentIndex = 0;

        function showNextSlide() {
            // Gỡ trạng thái active và thêm previous cho slide hiện tại
            slides[currentIndex].classList.remove('active');
            slides[currentIndex].classList.add('previous');

            // Cập nhật index cho slide tiếp theo
            currentIndex = (currentIndex + 1) % slides.length;

            // Đặt trạng thái active cho slide tiếp theo
            slides[currentIndex].classList.add('active');

            // Gỡ trạng thái previous sau khi animation kết thúc
            setTimeout(() => {
                slides.forEach(slide => slide.classList.remove('previous'));
            }, 1200);
        }

        // Thiết lập slide đầu tiên
        slides[currentIndex].classList.add('active');

        // Chuyển slide sau mỗi 5 giây
        setInterval(showNextSlide, 5000);
    });

    const swiper = new Swiper(".offers__swiper", {
        slidesPerView: 3,
        slidesPerGroup: 3,
        spaceBetween: 20,
        loop: false, // Nếu muốn slide lặp vô hạn thì nên bật true
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        autoplay: {
            delay: 5000,      // thời gian giữa mỗi lần tự chuyển slide (ms)
            disableOnInteraction: false, // vẫn tự động sau khi người dùng tương tác
        },
        breakpoints: {
            768: {
                slidesPerView: 2,
                slidesPerGroup: 2,
            },
            1024: {
                slidesPerView: 3,
                slidesPerGroup: 3,
            },
        },
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
</body>
</html>
