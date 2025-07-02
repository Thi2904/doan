<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Hanoi Hotel Footer</title>
    <style>
        /* === Footer Wrapper === */
        .hanoi-footer {
            background: linear-gradient(135deg, #0e1e5e 0%, #102348 100%);
            color: #e0e7ff;
            font-size: 14px;
            box-shadow: 0 -8px 24px rgba(0, 0, 0, 0.3);
            font-family: 'Cormorant Garamond', serif;
        }

        /* === Footer Container === */
        .hanoi-footer footer {
            padding: 60px 24px 24px;
            max-width: 1200px;
            margin: 0 auto;
        }

        /* === Footer Top Section === */
        .hanoi-footer .footer-top {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 40px;
            margin-bottom: 40px;
        }

        /* Logo & Slogan */
        .hanoi-footer .footer-logo {
            flex: 1 1 200px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .hanoi-footer .footer-logo img {
            width: 120px;
            height: auto;
            transition: transform 0.3s ease;
        }
        .hanoi-footer .footer-logo img:hover {
            transform: rotate(-5deg) scale(1.05);
        }
        .hanoi-footer .footer-logo p {
            margin: 0;
            font-size: 16px;
            font-weight: 400;
        }

        /* Footer Links */
        .hanoi-footer .footer-links {
            flex: 2 1 400px;
            display: flex;
            justify-content: space-between;
            gap: 40px;
            align-items: flex-start;
        }
        .hanoi-footer .footer-column {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .hanoi-footer .footer-column h3 {
            position: relative;
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 500;
        }
        .hanoi-footer .footer-column h3::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: #a5b4fc;
            border-radius: 1px;
        }
        .hanoi-footer .footer-column ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .hanoi-footer .footer-column ul li {
            margin-bottom: 10px;
            overflow: hidden;
        }
        .hanoi-footer .footer-column ul li:last-child {
            margin-bottom: 0;
        }
        .hanoi-footer .footer-column a {
            display: inline-block;
            color: #e0e7ff;
            text-decoration: none;
            position: relative;
            padding-bottom: 2px;
            transition: color 0.3s ease;
        }
        .hanoi-footer .footer-column a::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            width: 0;
            height: 2px;
            background: #a5b4fc;
            transition: width 0.3s ease, left 0.3s ease;
        }
        .hanoi-footer .footer-column a:hover {
            color: #a5b4fc;
        }
        .hanoi-footer .footer-column a:hover::after {
            width: 100%;
            left: 0;
        }

        /* Đẩy <ul> cột 2 xuống ngang hàng cột 1 */
        .hanoi-footer .footer-links .footer-column:nth-child(2) ul {
            margin-top: 38px; /* điều chỉnh theo line-height + margin-bottom của h3 */
        }

        /* Address Section */
        .hanoi-footer .footer-address {
            flex: 1 1 250px;
        }
        .hanoi-footer .footer-address h3 {
            position: relative;
            margin-bottom: 16px;
            font-size: 18px;
            font-weight: 500;
        }
        .hanoi-footer .footer-address h3::after {
            content: '';
            position: absolute;
            bottom: -4px;
            left: 0;
            width: 40px;
            height: 2px;
            background-color: #a5b4fc;
        }
        .hanoi-footer .footer-address ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .hanoi-footer .footer-address li {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            font-size: 14px;
        }
        .hanoi-footer .footer-address li:last-child {
            margin-bottom: 0;
        }
        .hanoi-footer .footer-address li i {
            margin-right: 8px;
            font-size: 16px;
            width: 20px;
            text-align: center;
            color: #a5b4fc;
        }

        /* === Footer Bottom Section === */
        .hanoi-footer .footer-bottom {
            border-top: 1px solid rgba(224, 231, 255, 0.2);
            padding: 16px 0;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
        }
        .hanoi-footer .footer-bottom .links {
            display: flex;
            gap: 16px;
        }
        .hanoi-footer .footer-bottom .links a {
            color: #c7d2fe;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease, transform 0.2s ease;
        }
        .hanoi-footer .footer-bottom .links a:hover {
            color: #a5b4fc;
            transform: translateY(-2px);
        }

        /* === Responsive === */
        @media (max-width: 768px) {
            .hanoi-footer .footer-top {
                flex-direction: column;
                gap: 32px;
            }
            .hanoi-footer .footer-links {
                flex-direction: column;
                gap: 24px;
            }
            .hanoi-footer .footer-bottom {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }
            .hanoi-footer .footer-bottom .links {
                justify-content: center;
            }
        }

        @media (max-width: 480px) {
            .hanoi-footer footer {
                padding: 40px 16px 16px;
            }
            .hanoi-footer .footer-logo img {
                width: 100px;
            }
            .hanoi-footer .footer-column h3,
            .hanoi-footer .footer-address h3 {
                font-size: 16px;
                margin-bottom: 12px;
            }
            .hanoi-footer .footer-column ul li,
            .hanoi-footer .footer-address li {
                font-size: 13px;
                margin-bottom: 8px;
            }
            .hanoi-footer .footer-bottom {
                font-size: 12px;
            }
        }

    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
</head>
<body>
<div class="hanoi-footer">
    <footer>
        <div class="footer-top">
            <div class="footer-logo">
                <img src="{{ asset('/Logo.png') }}" alt="Logo" />
                <p>Live Oriental Heritage</p>
            </div>
            <div class="footer-links">
                <div class="footer-column">
                    <h3>Khám Phá</h3>
                    <ul>
                        <li><a href="#">Trang chủ</a></li>
                        <li><a href="#">Loại phòng</a></li>
                        <li><a href="#">Ẩm thực</a></li>
                        <li><a href="#">Họp &amp; Sự kiện</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3 style="visibility: hidden; height: 0; margin: 0; padding: 0;">.</h3>
                    <ul>
                        <li><a href="#">Liên hệ</a></li>
                        <li><a href="#">Thư viện</a></li>
                        <li><a href="#">Tin tức</a></li>
                        <li><a href="#">Ưu đãi</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-address">
                <h3>Địa Chỉ</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> D8 Giảng Võ, Ba Đình, Hà Nội</li>
                    <li><i class="fas fa-phone-alt"></i> (+84) 24 3845 2270</li>
                    <li><i class="fas fa-mobile-alt"></i> (+84) 88 856 0126</li>
                    <li><i class="fas fa-envelope"></i> sales@hanoihotel.com.vn</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <div>© 2025 HANOI HOTEL. All rights reserved.</div>
            <div class="links">
                <a href="#">Điều khoản</a>
                <a href="#">Chính sách Bảo mật và Cookie</a>
            </div>
        </div>
    </footer>
</div>
</body>
</html>
