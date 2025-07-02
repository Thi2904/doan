<?php
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BedTypeController;
use App\Http\Controllers\BookingController;
use App\Mail\BookingCreatedAdmin;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\OfflineBookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Mail\BookingCreatedGuest;
use App\Models\Booking;
use Illuminate\Http\Request;
use App\Http\Controllers\RoomChangeController;
use App\Http\Controllers\RoomImageController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SettingController;
use App\Models\RoomType;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\AdditionalFeeController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/login', [LoginController::class, 'loginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::post('/register', [RegisterController::class, 'store'])->name('register');
Route::view('/homepage', 'customer.homepage')->name('customer.homepage');
Route::get('/rooms-type', [RoomTypeController::class, 'accommodations'])->name('customer.rooms-type');
Route::get('/rooms/{id}', [RoomTypeController::class, 'details'])->name('rooms.show');
Route::get('/admin/panel', [AdminController::class, 'panel'])
    ->middleware('auth')
    ->name('admin.panel');
Route::get('/profile', [CustomerController::class, 'profile'])
    ->name('profile');
Route::put('/customer/profile', [CustomerController::class, 'update'])
    ->name('profile.update');


//Route cho quản lý bên admin
Route::get('/admin/customers', [CustomerController::class, 'index'])->name('admin.customers');
Route::get('/admin/customers/{id}', [CustomerController::class, 'show'])->name('customers.show'); // Xem chi tiết
Route::patch('/admin/customers/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggleStatus');


Route::get('/admin/settings', [SettingController::class, 'index'])->name('admin.setting');

// Quản lý tiện nghi
Route::prefix('admin')->name('features.')->group(function () {
    Route::get('/features', [FeatureController::class, 'index'])->name('index');
     Route::post('/features', [FeatureController::class, 'store'])->name('store');
     Route::get('/features/{feature}/edit', [FeatureController::class, 'edit'])->name('edit');
     Route::put('/features/{feature}', [FeatureController::class, 'update'])->name('update');
     Route::delete('/features/{feature}', [FeatureController::class, 'destroy'])->name('destroy');
});

// Quản lý loại phòng
Route::prefix('admin')->name('room_types.')->group(function () {
    Route::get('/room_types', [RoomTypeController::class, 'index'])->name('index');     // Hiển thị danh sách room_types
    Route::post('/room_types', [RoomTypeController::class, 'store'])->name('store');    // Thêm mới room_type
    Route::get('/room_types/{room_type}/edit', [RoomTypeController::class, 'edit'])->name('edit');   // Chỉnh sửa room_type
    Route::put('/room_types/{room_type}', [RoomTypeController::class, 'update'])->name('update');  // Cập nhật room_type
    Route::delete('/room_types/{room_type}', [RoomTypeController::class, 'destroy'])->name('destroy');  // Xóa room_type
});

// Quản lý loại giườnng
Route::prefix('admin')->name('bed_types.')->group(function () {
    Route::get('/bed_types', [BedTypeController::class, 'index'])->name('index');         // Hiển thị danh sách bed_types
    Route::post('/bed_types', [BedTypeController::class, 'store'])->name('store');        // Thêm mới bed_type
    Route::get('/bed_types/{bed_type}/edit', [BedTypeController::class, 'edit'])->name('edit');    // Chỉnh sửa bed_type
    Route::put('/bed_types/{bed_type}', [BedTypeController::class, 'update'])->name('update');  // Cập nhật bed_type
    Route::delete('/bed_types/{bed_type}', [BedTypeController::class, 'destroy'])->name('destroy');  // Xóa bed_type
});

// Quản lý phòng
Route::prefix('admin')->name('rooms.')->group(function () {
    Route::get('/rooms', [RoomController::class, 'index'])->name('index');        // Hiển thị danh sách rooms
    Route::post('/rooms', [RoomController::class, 'store'])->name('store');       // Thêm mới room
    Route::get('/rooms/{room}/edit', [RoomController::class, 'edit'])->name('edit'); // Chỉnh sửa room
    Route::put('/rooms/{room}', [RoomController::class, 'update'])->name('rooms.update');
    Route::delete('/rooms/{room}', [RoomController::class, 'destroy'])->name('destroy'); // Xóa room
});

Route::prefix('admin')->name('room_images.')->group(function () {
    Route::get('/room-images', [RoomImageController::class, 'index'])->name('index');
    Route::post('/room-images', [RoomImageController::class, 'store'])->name('store');
    Route::get('/room-images/{roomImage}/edit', [RoomImageController::class, 'edit'])->name('edit');
    Route::put('/room-images/{roomImage}', [RoomImageController::class, 'update'])->name('update');
    Route::delete('/room-images/{roomImage}', [RoomImageController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin')->name('additional_fees.')->group(function () {
    Route::get('/additional-fees', [AdditionalFeeController::class, 'index'])->name('index');
    Route::post('/additional-fees', [AdditionalFeeController::class, 'store'])->name('store');
    Route::get('/additional-fees/{additionalFee}/edit', [AdditionalFeeController::class, 'edit'])->name('edit');
    Route::put('/additional-fees/{additionalFee}', [AdditionalFeeController::class, 'update'])->name('update');
    Route::delete('/additional-fees/{additionalFee}', [AdditionalFeeController::class, 'destroy'])->name('destroy');
});

Route::prefix('admin')->name('payment_methods.')->group(function () {
    Route::get('/payment-methods', [PaymentMethodController::class, 'index'])->name('index');
    Route::post('/payment-methods', [PaymentMethodController::class, 'store'])->name('store');
    Route::get('/payment-methods/{paymentMethod}/edit', [PaymentMethodController::class, 'edit'])->name('edit');
    Route::put('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'update'])->name('update');
    Route::delete('/payment-methods/{paymentMethod}', [PaymentMethodController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->group(function(){
    Route::resource('bookings', BookingController::class)
        ->only(['index','create','store','show']);
});

Route::prefix('admin')
    ->name('admin.')
    ->group(function () {
        // 1. Trang list booking
        Route::get('bookings', [BookingController::class, 'index'])
            ->name('bookings.index');
        // 2. JSON detail cho AJAX (GET /admin/bookings/{booking})
        Route::get('bookings/{booking}', [BookingController::class, 'show_details'])
            ->name('bookings.show');
    });

Route::put('/admin/bookings/{id}/status', [BookingController::class, 'updateStatus']);
Route::put('/admin/bookings/{id}', [BookingController::class, 'update'])->name('admin.bookings.update');
Route::prefix('admin')->name('admin.')->group(function(){
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
});

Route::put('/admin/payments/{id}/status', [PaymentController::class, 'updateStatus']);
// routes/web.php
Route::get('/admin/bookings/{booking}/surcharge-modal', [BookingController::class, 'surchargeModal'])
    ->name('admin.bookings.surchargeModal');
// routes/web.php
Route::post(
    '/admin/bookings/{booking}/fees',
    [BookingController::class, 'updateFees']
)->name('admin.bookings.fees.update');


Route::get('/get-room-types', [RoomTypeController::class, 'getRoomTypes'])->name('get.room.types');
Route::patch('/booking/change-room-type', [BookingController::class, 'changeRoomType'])
    ->name('booking.changeRoomType');

Route::get('/booking-calendar', [BookingController::class, 'calendar'])->name('admin.booking.calendar');

Route::get('/room_changes', [RoomChangeController::class, 'index'])
    ->name('room_changes.index');

Route::put('/profile/update', [CustomerController::class, 'update'])->name('profile.update');
Route::post('/profile/change-password', [CustomerController::class, 'changePassword'])->name('profile.change-password');
Route::get('/admin/room-matrix', [RoomController::class, 'roomStatusMatrix'])->name('admin.room.matrix');
Route::get('/admin/room/table-view', [RoomController::class, 'roomTableView'])->name('admin.room.table_view');
Route::get('/admin/room-details', [RoomController::class, 'getRoomDetails'])->name('admin.room.details');
Route::get('/api/available-rooms', [BookingController::class, 'getAvailableRooms']);

// thêm 2 route cho tạo mới
Route::get('/admin/create', [BookingController::class, 'createAdmin'])
    ->name('admin.bookings.create');
Route::post('/admin/bookings', [BookingController::class, 'storeAdmin'])
    ->name('admin.bookings.store');
Route::get('/vnpay-return', [BookingController::class, 'vnpayReturn'])
    ->name('vnpay.return');
// Trang hiển thị hóa đơn
Route::get('/bookings/{booking}/invoice', [BookingController::class, 'invoicePage'])->name('bookings.invoice.page');
Route::get('/bookings/{booking}/invoice/download', [BookingController::class, 'downloadInvoice'])->name('bookings.invoice.download');
Route::get('/test-mail', function () {
    $booking = Booking::latest()->first();
    Mail::to('likebkorn@gmail.com')->send(new BookingCreatedGuest($booking));
    return 'Đã gửi mail test!';
});
Route::get('/test-admin-mail', function () {
    $booking = Booking::latest()->first(); // Lấy booking gần nhất

    if (!$booking) {
        return 'Không có booking để test.';
    }

    Mail::to(config('mail.admin_address', 'admin@example.com'))
        ->send(new BookingCreatedAdmin($booking));

    return 'Đã gửi mail test cho admin!';
});
