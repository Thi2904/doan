<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/login.css') }}" />
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png" />
    <title>Trang Đăng Nhập - Hanoi Hotel</title>
</head>
<body>

<a href="{{ route('home') }}" class="login__logo-link">
    <div class="login__logo">
        <img src="{{ asset('assets/Logo.png') }}" alt="Hanoi Hotel Logo" />
        <span class="login__logo-text">Hanoi Hotel</span>
    </div>
</a>

<svg class="login__blob" viewBox="0 0 566 840" xmlns="http://www.w3.org/2000/svg">
    <mask id="mask0" mask-type="alpha">
        <path d="M342.407 73.6315C388.53 56.4007 394.378 17.3643 391.538
        0H566V840H0C14.5385 834.991 100.266 804.436 77.2046 707.263C49.6393
        591.11 115.306 518.927 176.468 488.873C363.385 397.026 156.98 302.824
        167.945 179.32C173.46 117.209 284.755 95.1699 342.407 73.6315Z"/>
    </mask>
    <g mask="url(#mask0)">
        <image href="{{ asset('assets/login.jpg') }}" x="0" y="0" width="566" height="840" preserveAspectRatio="xMidYMid slice"/>
    </g>
</svg>

<div class="login container grid" id="loginAccessRegister">
    <div class="login__access">
        <h1 class="login__title">Đăng nhập tài khoản.</h1>
        <div class="login__area">
            <form method="POST" action="{{ route('login') }}" class="login__form">
                @csrf
                <div class="login__box">
                    <input type="email" name="email" required placeholder=" " class="login__input" />
                    <label class="login__label">Email</label>
                    <i class="ri-mail-fill login__icon"></i>
                </div>

                <div class="login__box">
                    <input type="password" id="password" name="password" required placeholder=" " class="login__input" />
                    <label class="login__label">Mật Khẩu</label>
                    <i class="ri-eye-off-fill login__icon login__password" id="loginPassword"></i>
                </div>

                <button type="submit" class="login__button">Đăng Nhập</button>
            </form>

            <p class="login__switch">
                Không có tài khoản?
                <button id="loginButtonRegister">Tạo tài khoản</button>
            </p>
        </div>
    </div>

    <div class="login__register">
        <h1 class="login__title">Tạo tài khoản mới.</h1>
        <div class="login__area">
            <form method="POST" action="{{ route('register') }}" class="login__form">
                @csrf
                <div class="login__group grid">
                    <div class="login__box">
                        <input type="text" name="name" required placeholder=" " class="login__input" />
                        <label class="login__label">Họ và Tên </label>
                        <i class="ri-id-card-fill login__icon"></i>
                    </div>

                    <div class="login__box">
                        <input type="text" name="phone" placeholder=" " class="login__input" />
                        <label class="login__label">Số Điện Thoại</label>
                        <i class="ri-phone-find-fill login__icon"></i>
                    </div>
                </div>

                <div class="login__box">
                    <input type="email" name="email" required placeholder=" " class="login__input" />
                    <label class="login__label">Email</label>
                    <i class="ri-mail-fill login__icon"></i>
                </div>

                <div class="login__box">
                    <input type="password" id="passwordCreate" name="password" required placeholder=" " class="login__input" />
                    <label class="login__label">Mật Khẩu</label>
                    <i class="ri-eye-off-fill login__icon login__password" id="loginPasswordCreate"></i>
                </div>

                <div class="login__box">
                    <input type="password" id="confirmPasswordCreate" name="password_confirmation" required placeholder=" " class="login__input" />
                    <label class="login__label">Xác nhận mật khẩu</label>
                    <i class="ri-eye-off-fill login__icon" id="confirmPasswordToggle"></i>
                </div>

                <button type="submit" class="login__button">Tạo Tài Khoản</button>
            </form>

            <p class="login__switch">
                Đã có tài khoản?
                <button id="loginButtonAccess">Đăng Nhập</button>
            </p>
        </div>
    </div>
</div>
<!--=============== MAIN JS ===============-->
<script src="https://unpkg.com/scrollreveal"></script>
<script src="{{ asset('js/login.js') }}"></script>
<script>
    const scrollRevealOption = {
        distance: "60px",
        duration: 2000,
        easing: "ease-in-out",
        origin: "bottom",
        reset: false,
    };

    ScrollReveal().reveal(".login__logo-link", {
        ...scrollRevealOption,
        origin: "top",
        delay: 200,
        reset: true,
    });

    ScrollReveal().reveal(".login__blob", {
        ...scrollRevealOption,
        origin: "left",
        delay: 400,
        distance: "40px",
    });

    ScrollReveal().reveal(".login__access .login__title, .login__register .login__title", {
        ...scrollRevealOption,
        delay: 600,
    });

    ScrollReveal().reveal(".login__access .login__form, .login__register .login__form", {
        ...scrollRevealOption,
        delay: 800,
    });

    ScrollReveal().reveal(".login__box", {
        ...scrollRevealOption,
        interval: 150,
        delay: 900,
    });

    ScrollReveal().reveal(".login__switch", {
        ...scrollRevealOption,
        delay: 1000,
    });
</script>
</body>
</html>
