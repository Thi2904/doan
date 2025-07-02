<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/panel.css') }}">
    <link rel="icon" href="{{ asset('/favicon.png') }}" type="image/png">
    <title>Trang Admin - Hanoi Hotel</title>
</head>
<body>

<!-- SIDEBAR -->
@include('admin.sidebar')
<!-- SIDEBAR -->

<section id="content">
    <!-- NAVBAR -->
    @include('admin.navbar')
    <!-- NAVBAR -->

    <!-- MAIN -->
    <main>
        <h1 class="title">Cài Đặt</h1>
        <ul class="breadcrumbs">
            <li><a href="#">Trang Chủ</a></li>
            <li class="divider">/</li>
            <li><a href="#" class="active">Cài Đặt</a></li>
        </ul>
        <div class="info-data">
            <div class="card">
                <div class="head">
                    <h2>Thông tin chung</h2>
                </div>
                <form action="" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="site_name">Site Name</label>
                        <input type="text" id="site_name" name="site_name" value="{{ old('site_name', $settings->site_name) }}">
                    </div>
                    <div class="form-group">
                        <label for="about_us">About Us</label>
                        <textarea id="about_us" name="about_us" rows="4">{{ old('about_us', $settings->about_us) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="contact_address">Contact Address</label>
                        <input type="text" id="contact_address" name="contact_address" value="{{ old('contact_address', $settings->contact_address) }}">
                    </div>
                    <button type="submit" class="btn-upgrade">Lưu Cài Đặt</button>
                </form>
            </div>
        </div>
    </main>
</section>

<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="{{ asset('js/script.js') }}"></script>
</body>
</html>
