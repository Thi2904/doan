<?php

namespace App\Http\Controllers;
use App\Models\BookingPayment;
use App\Models\BookingRoom;
use App\Models\BookingRoomFee;
use App\Models\PaymentMethod;
use App\Models\RoomChange;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use App\Models\BookingAllocation;
use Illuminate\Support\Facades\DB;
use App\Models\AdditionalFee;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use App\Mail\BookingCreatedGuest;
use App\Mail\BookingCreatedAdmin;
class BookingController extends Controller
{
    public function create(Request $request)
    {
        try {
            if (! $request->has('check_in') || ! $request->has('check_out')) {
                // như cũ: lấy tất cả phòng trống hiện thời
                $roomTypes = RoomType::withCount(['rooms as available_count' => function ($q) {
                    $q->whereDoesntHave('allocations', function ($q2) {
                        $q2->whereNull('end_at');
                    });
                }])
                    ->with(['images', 'bedTypes'])
                    ->get();
            } else {
                $checkIn  = Carbon::parse($request->input('check_in'))->startOfDay();
                $checkOut = Carbon::parse($request->input('check_out'))->startOfDay();

                $roomTypes = RoomType::query()
                    ->withCount(['rooms as available_count' => function ($q) use ($checkIn, $checkOut) {
                        $q->whereDoesntHave('allocations', function ($q2) use ($checkIn, $checkOut) {
                            $q2->where('start_at', '<', $checkOut)
                                ->where(function ($q3) use ($checkIn) {
                                    $q3->whereNull('end_at')
                                        ->orWhere('end_at', '>', $checkIn);
                                });
                        });
                    }])
                    ->having('available_count', '>', 0)
                    ->with(['images', 'bedTypes'])
                    ->get();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau!');
        }

        if ($roomTypes->isEmpty()) {
            return redirect()->back()->with('warning', 'Rất tiếc, hiện không có phòng trống phù hợp.');
        }

        // Phần còn lại giữ nguyên
        $maxAdults   = $roomTypes->max('max_adult');
        $maxChildren = $roomTypes->max('max_children');
        $pmethods    = PaymentMethod::where('is_active', 1)->pluck('name', 'id');
        $fees = AdditionalFee::where('is_active', 1)
            ->where('type', 'pre_fee')
            ->get();


        return view('customer.booking', compact(
            'roomTypes', 'fees', 'pmethods', 'maxAdults', 'maxChildren'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'check_in'             => 'required|date|after_or_equal:today',
            'check_out'            => 'required|date|after:check_in',
            'guest_name'           => 'required|string|max:255',
            'guest_email'          => 'required|email|max:255',
            'guest_phone'          => 'required|string|max:20',
            'payment_method_id'    => 'required|exists:payment_methods,id',
            'rooms'                => 'required|array|min:1',
            'rooms.*.room_type_id' => 'required|exists:room_types,id',
            'rooms.*.adults'       => 'required|integer|min:1',
            'rooms.*.children'     => 'required|integer|min:0',
            'rooms.*.fees'         => 'nullable|array',
            'rooms.*.fees.*'       => 'exists:additional_fees,id',
        ]);

        DB::beginTransaction();
        try {
            $timezone = 'Asia/Ho_Chi_Minh';
            $startDate = Carbon::parse($validated['check_in'], $timezone)->startOfDay();
            $endDate   = Carbon::parse($validated['check_out'], $timezone)->startOfDay();
            $nights    = $startDate->diffInDays($endDate);

            $checkIn  = $startDate->setTime(14, 0); // 14h00 ngày check-in
            $checkOut = $endDate->setTime(12, 0);   // 12h00 ngày check-out
            // Tạo booking
            $booking = Booking::create([
                'booking_code'      => strtoupper(Str::random(8)),
                'user_id'           => auth()->id(),
                'guest_name'        => $validated['guest_name'],
                'guest_email'       => $validated['guest_email'],
                'guest_phone'       => $validated['guest_phone'],
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'num_adults'        => 0,
                'num_children'      => 0,
                'total_price'       => 0,
                'status'            => 'pending',
                'payment_method_id' => $validated['payment_method_id'],
            ]);

            // Tính giá rooms + fees
            $totalAdults = $totalChildren = $totalPrice = 0;
            $bookingRooms = [];

            foreach ($validated['rooms'] as $roomData) {
                $rt  = RoomType::findOrFail($roomData['room_type_id']);
                $sub = $rt->base_price * $nights;
                $totalAdults   += $roomData['adults'];
                $totalChildren += $roomData['children'];
                $totalPrice    += $sub;

                $br = BookingRoom::create([
                    'booking_id'      => $booking->id,
                    'room_type_id'    => $rt->id,
                    'adults'          => $roomData['adults'],
                    'children'        => $roomData['children'],
                    'price_per_night' => $rt->base_price,
                    'nights'          => $nights,
                    'sub_total'       => $sub,
                ]);
                $bookingRooms[] = $br;

                if (!empty($roomData['fees'])) {
                    foreach ($roomData['fees'] as $feeId) {
                        $fee = AdditionalFee::findOrFail($feeId);
                        $totalPrice += $fee->default_price;
                        BookingRoomFee::create([
                            'booking_room_id' => $br->id,
                            'fee_id'          => $fee->id,
                            'price'           => $fee->default_price,
                        ]);
                    }
                }
            }

            // Tạo allocations
            foreach ($bookingRooms as $br) {
                $room = Room::where('room_type_id', $br->room_type_id)
                    ->whereDoesntHave('allocations', fn($q) =>
                    $q->where('start_at', '<', $checkOut)
                        ->where('end_at', '>', $checkIn)
                    )->first();

                if (!$room) {
                    throw new \Exception("Không còn phòng loại {$br->roomType->name}");
                }

                BookingAllocation::create([
                    'booking_id' => $booking->id,
                    'room_id'    => $room->id,
                    'start_at'   => $checkIn,
                    'end_at'     => $checkOut,
                ]);
            }

            // Cập nhật tổng số người và tổng giá
            $booking->update([
                'num_adults'   => $totalAdults,
                'num_children' => $totalChildren,
                'total_price'  => $totalPrice,
            ]);

            DB::commit();

            // Tính cọc và còn lại
            $deposit = round($totalPrice * 0.3, 2);
            $remain  = $totalPrice - $deposit;

            // Tạo payments
            if ($validated['payment_method_id'] == 2) {
                // VNPay: cọc 30% pending + thanh toán 70%
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => 2,
                    'amount'            => $deposit,
                    'entry_type'        => 'deposit',
                    'status'            => 'pending',
                    'description'       => 'Cọc 30% qua VNPay',
                ]);
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => 2,
                    'amount'            => $remain,
                    'entry_type'        => 'payment',
                    'status'            => 'pending',
                    'description'       => 'Thanh toán 70% còn lại',
                ]);
            } else {
                // Phương thức khác: cọc paid, 70% pending
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => $validated['payment_method_id'],
                    'amount'            => $deposit,
                    'entry_type'        => 'deposit',
                    'status'            => 'paid',
                    'description'       => 'Cọc 30%',
                    'paid_at'           => now(),
                ]);
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => $validated['payment_method_id'],
                    'amount'            => $remain,
                    'entry_type'        => 'payment',
                    'status'            => 'pending',
                    'description'       => 'Thanh toán 70% còn lại',
                ]);
            }

            // Gửi mail đồng bộ
            try {
                Mail::to($booking->guest_email)
                    ->send(new BookingCreatedGuest($booking));

                Mail::to(config('mail.admin_address'))
                    ->send(new BookingCreatedAdmin($booking));
            } catch (\Exception $mailEx) {
                \Log::error('Lỗi gửi mail: ' . $mailEx->getMessage());
            }

            // Nếu VNPay, chuyển hướng sau gửi mail
            if ($validated['payment_method_id'] == 2) {
                return $this->redirectToVnpay($deposit, $booking->id);
            }

            return redirect()
                ->route('bookings.show', $booking)
                ->with('success', 'Đặt phòng thành công! Email xác nhận đã được gửi.');
        }
        catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    protected function redirectToVnpay($amount, $bookingId)
    {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $tmn    = config('services.vnpay.tmn_code');
        $secret = config('services.vnpay.hash_secret');
        $url    = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $return = route('vnpay.return');

        $txn    = $bookingId . '_' . time();
        $params = [
            'vnp_Version'    => '2.1.0',
            'vnp_TmnCode'    => $tmn,
            'vnp_Amount'     => $amount * 100,
            'vnp_Command'    => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode'   => 'VND',
            'vnp_IpAddr'     => request()->ip(),
            'vnp_Locale'     => 'vn',
            'vnp_OrderInfo'  => "Cọc đặt phòng #{$bookingId}",
            'vnp_OrderType'  => 'billpayment',
            'vnp_ReturnUrl'  => $return,
            'vnp_TxnRef'     => $txn,
            'vnp_ExpireDate' => date('YmdHis', strtotime('+15 minutes')),
        ];

        ksort($params);
        $query = []; $hash = [];
        foreach ($params as $k => $v) {
            $query[] = "$k=" . urlencode($v);
            $hash[]  = "$k=" . urlencode($v);
        }

        $qs     = implode('&', $query);
        $data   = implode('&', $hash);
        $secure = hash_hmac('sha512', $data, $secret);

        return redirect()->away("{$url}?{$qs}&vnp_SecureHash={$secure}");
    }
    public function vnpayReturn(Request $request)
    {
        if ($request->vnp_ResponseCode === '00') {
            [$bookingId] = explode('_', $request->vnp_TxnRef);

            if ($booking = Booking::find($bookingId)) {
                // Cập nhật trạng thái booking
                $booking->update(['status' => 'pending']);
                // Đánh dấu cọc 30% thành paid
                BookingPayment::where('booking_id', $bookingId)
                    ->where('entry_type', 'deposit')
                    ->where('status', 'pending')
                    ->update([
                        'status'  => 'paid',
                        'paid_at' => now(),
                    ]);

                // Giữ nguyên 70% pending cho thanh toán sau
            }

            return redirect()
                ->route('bookings.show', $bookingId)
                ->with('success','Thanh toán cọc VNPay thành công!');
        }

        // Thất bại/hủy
        [$bookingId] = explode('_', $request->vnp_TxnRef);
        return redirect()
            ->route('bookings.show', $bookingId)
            ->with('error','Thanh toán VNPay thất bại hoặc bị hủy.');
    }

    public function show(Booking $booking)
    {
        $booking->load([
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room'
        ]);

        return view('customer.booking_show', compact('booking'));
    }

    public function index()
    {
        $bookings = Booking::with([
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room',
        ])
            ->orderByRaw("FIELD(status, 'pending', 'confirmed', 'checked_in', 'completed', 'cancelled')")
            ->orderBy('created_at', 'ASC')
            ->get();

        foreach ($bookings as $booking) {
            $booking->status_label = match ($booking->status) {
                'checked_in' => 'Đã nhận phòng',
                'pending' => 'Đang chờ',
                'cancelled' => 'Đã hủy',
                'completed' => 'Hoàn thành',
                'confirmed' => 'Đã xác nhận',
                default => 'Không xác định',
            };

            $badgeClass = match ($booking->status) {
                'pending' => 'badge-pending',
                'confirmed' => 'badge-confirmed',
                'checked_in' => 'badge-checkedin',
                'checked_out' => 'badge-checkedout',
                'completed' => 'badge-completed',
                'cancelled' => 'badge-cancelled',
                default => 'badge-default',
            };

            $booking->status_badge_html = '<span class="badge ' . $badgeClass . '">' . $booking->status_label . '</span>';

            // Gán đúng check-in / check-out từ allocation
            foreach ($booking->bookingRooms as $room) {
                $alloc = $booking->allocations
                    ->firstWhere('booking_room_id', $room->id);

                if ($alloc) {
                    $room->check_in = Carbon::parse($alloc->start_at)
                        ->timezone('Asia/Ho_Chi_Minh')
                        ->format('Y-m-d');
                    $room->check_out = Carbon::parse($alloc->end_at)
                        ->timezone('Asia/Ho_Chi_Minh')
                        ->format('Y-m-d');
                } else {
                    // fallback nếu không có allocation
                    $room->check_in = Carbon::parse($booking->check_in)
                        ->timezone('Asia/Ho_Chi_Minh')
                        ->format('Y-m-d');
                    $room->check_out = Carbon::parse($booking->check_out)
                        ->timezone('Asia/Ho_Chi_Minh')
                        ->format('Y-m-d');
                }
            }
        }

        $totalCount = $bookings->count();

        $statusCounts = $bookings
            ->groupBy('status')
            ->map(fn($group) => $group->count())
            ->toArray();

        $statusCounts = array_merge([
            'pending'     => 0,
            'confirmed'   => 0,
            'checked_in'  => 0,
            'completed'   => 0,
            'cancelled'   => 0,
        ], $statusCounts);

        $additionalFees = AdditionalFee::where('is_active', true)
            ->get()
            ->groupBy('type');

        $roomTypes = RoomType::all();

        return view('admin.bookings', compact(
            'bookings',
            'additionalFees',
            'roomTypes',
            'totalCount',
            'statusCounts'
        ));
    }

    public function show_details(Booking $booking)
    {
        $booking->load([
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room',
            'allocations.room.roomType',
        ]);

        return response()->json($booking);
    }
    public function updateStatus(Request $request, $id)
    {
        // Validation: guest_id_number chỉ bắt buộc khi status = checked_in
        $request->validate([
            'status'            => 'required|in:pending,confirmed,checked_in,completed,cancelled',
            'cancel_reason'     => 'nullable|string|max:255',
            'actual_check_in'   => 'nullable|date',
            'actual_check_out'  => 'nullable|date',
            'guest_id_number'   => 'required_if:status,checked_in|string|max:50',
        ]);

        $booking    = Booking::with(['allocations', 'booking_rooms'])->findOrFail($id);
        $oldStatus  = $booking->status;
        $newStatus  = $request->status;

        // Các trạng thái hợp lệ để chuyển đổi
        $allowed = [
            'pending'    => ['confirmed', 'cancelled'],
            'confirmed'  => ['checked_in', 'cancelled'],
            'checked_in' => ['completed'],
            'completed'  => [],
            'cancelled'  => ['pending'],
        ];

        if (!in_array($newStatus, $allowed[$oldStatus])) {
            return response()->json([
                'error' => "Không thể chuyển từ '{$oldStatus}' sang '{$newStatus}'."
            ], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Nếu chuyển sang "checked_in"
            if ($newStatus === 'checked_in') {
                $booking->actual_check_in = $request->filled('actual_check_in')
                    ? Carbon::parse($request->actual_check_in)
                    : now();
                $booking->guest_id_number = $request->guest_id_number;
            }

            // 2. Nếu chuyển sang "completed"
            if ($newStatus === 'completed') {
                $hasUnpaid = $booking->payments()->where('status', '!=', 'paid')->exists();
                if ($hasUnpaid) {
                    return response()->json([
                        'error' => 'Không thể hoàn tất đơn vì vẫn còn khoản thanh toán chưa được trả.'
                    ], 422);
                }

                $booking->actual_check_out = $request->filled('actual_check_out')
                    ? Carbon::parse($request->actual_check_out)
                    : now();

                $scheduled = Carbon::parse($booking->check_out)->setTime(12, 0);
                $actual    = $booking->actual_check_out;

                // 2.1. Phụ thu trả phòng trễ
                if ($actual->isSameDay($scheduled) && $actual->gt($scheduled)) {
                    $minutes = $scheduled->diffInMinutes($actual);
                    $percent = match (true) {
                        $minutes <= 120 => 15,
                        $minutes <= 240 => 20,
                        $minutes <= 360 => 50,
                        $minutes <= 720 => 100,
                        default => 0,
                    };
                    if ($percent) {
                        $fee = round($booking->booking_rooms->sum('price_per_night') * $percent / 100, 2);
                        BookingPayment::create([
                            'booking_id'  => $booking->id,
                            'amount'      => $fee,
                            'entry_type'  => 'charge',
                            'status'      => 'pending',
                            'description' => "Phụ thu trả phòng trễ {$percent}%"
                        ]);
                        $booking->increment('total_price', $fee);
                    }
                }

                // 2.2. Hoàn tiền trả phòng sớm (nếu checkout trước ngày đã đặt)
                if ($actual->lt($scheduled)) {
                    $originalCheckIn  = Carbon::parse($booking->check_in)->startOfDay();
                    $originalCheckOut = Carbon::parse($booking->check_out)->startOfDay();
                    $actualCheckOut   = $actual->copy()->startOfDay();

                    // Số đêm đã sử dụng = từ check_in đến actual_check_out
                    $usedNights = $originalCheckIn->diffInDays($actualCheckOut);

                    // Tổng số đêm ban đầu
                    $originalNights = $originalCheckIn->diffInDays($originalCheckOut);

                    // Đêm hoàn lại
                    $refundedNights = $originalNights - $usedNights;

                    if ($refundedNights > 0) {
                        $refund = $booking->booking_rooms->sum('price_per_night') * $refundedNights;

                        BookingPayment::create([
                            'booking_id'  => $booking->id,
                            'amount'      => $refund,
                            'entry_type'  => 'refund',
                            'status'      => 'pending',
                            'description' => "Hoàn tiền {$refundedNights} đêm trả sớm"
                        ]);

                        $booking->decrement('total_price', $refund);
                    }
                }

            }

            // 3. Nếu chuyển sang "cancelled"
            if ($newStatus === 'cancelled') {
                $booking->allocations()
                    ->where('start_at', '>', now())
                    ->delete();

                $booking->allocations()
                    ->where('start_at', '<=', now())
                    ->update(['end_at' => now()]);
            }

            // 4. Nếu từ "cancelled" trở lại "pending", cấp phát lại phòng
            if ($oldStatus === 'cancelled' && $newStatus === 'pending') {
                $booking->allocations()->delete();

                $start = Carbon::parse($booking->check_in)->setTime(14, 0);
                $end   = Carbon::parse($booking->check_out)->setTime(12, 0);

                foreach ($booking->booking_rooms as $br) {
                    $room = Room::where('room_type_id', $br->room_type_id)
                        ->whereDoesntHave('allocations', function($q) use ($start, $end) {
                            $q->where('start_at', '<', $end)
                                ->where('end_at', '>', $start);
                        })->first();

                    if (!$room) {
                        throw new \Exception("Không có phòng trống cho loại {$br->room_type_id}");
                    }

                    BookingAllocation::create([
                        'booking_id' => $booking->id,
                        'room_id'    => $room->id,
                        'start_at'   => $start,
                        'end_at'     => $end,
                    ]);
                }
            }

            // 5. Cập nhật trạng thái và lý do hủy (nếu có)
            $booking->status        = $newStatus;
            $booking->cancel_reason = $newStatus === 'cancelled' ? $request->cancel_reason : null;
            $booking->save();

            // 6. Nếu "completed", cập nhật lại end_at cho allocations
            if ($newStatus === 'completed') {
                foreach ($booking->allocations as $alloc) {
                    $alloc->update(['end_at' => $booking->actual_check_out]);
                }
            }

            DB::commit();
            return response()->json(['message' => 'Cập nhật trạng thái thành công.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Đã xảy ra lỗi: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $booking = Booking::with([
            'bookingRooms.fees',
            'allocations.room.roomType',
            'payments'
        ])->findOrFail($id);

        $validated = $request->validate([
            'guest_name'  => 'required|string|max:255',
            'guest_email' => 'required|email|max:255',
            'guest_phone' => 'nullable|string|max:15',
            'check_in'    => 'required|date|after_or_equal:today',
            'check_out'   => 'required|date|after:check_in',
        ]);

        // Gán giờ mặc định: check-in 14:00, check-out 12:00
        // Gán giờ mặc định: check-in 14:00, check-out 12:00
        $checkInDate  = Carbon::parse($validated['check_in'])->startOfDay(); // <--- thêm startOfDay()
        $checkOutDate = Carbon::parse($validated['check_out'])->startOfDay(); // <--- thêm startOfDay()

// Đặt lại giờ hiển thị, nhưng dùng ngày thuần để tính số đêm
        $newCheckIn  = $checkInDate->copy()->setTime(14, 0);
        $newCheckOut = $checkOutDate->copy()->setTime(12, 0);
        $newNights = $checkInDate->diffInDays($checkOutDate);


        $newTotal  = 0;
        $originalTotal = $booking->total_price;

        DB::beginTransaction();

        try {
            // Cập nhật thông tin khách
            $booking->update([
                'guest_name'  => $validated['guest_name'],
                'guest_email' => $validated['guest_email'],
                'guest_phone' => $validated['guest_phone'],
            ]);

            // Cập nhật chi tiết bookingRooms
            foreach ($booking->bookingRooms as $bookingRoom) {
                $pricePerNight = $bookingRoom->price_per_night;
                $roomFeeTotal  = $bookingRoom->fees->sum('price');
                $newSubTotal   = $pricePerNight * $newNights;

                $bookingRoom->update([
                    'nights'    => $newNights,
                    'sub_total' => $newSubTotal,
                ]);

                $newTotal += $newSubTotal + $roomFeeTotal;
            }

            // Cập nhật lại ngày giờ và tổng tiền mới
            $booking->update([
                'check_in'    => $newCheckIn,
                'check_out'   => $newCheckOut,
                'total_price' => $newTotal,
            ]);

            // Kiểm tra và cập nhật allocations với giờ cụ thể
            foreach ($booking->allocations as $allocation) {
                $room = $allocation->room;

                $conflict = BookingAllocation::where('room_id', $room->id)
                    ->where('booking_id', '!=', $booking->id)
                    ->where(function ($q) use ($newCheckIn, $newCheckOut) {
                        $q->where('start_at', '<', $newCheckOut)
                            ->where('end_at', '>', $newCheckIn);
                    })->exists();

                if ($conflict) {
                    // Tìm phòng thay thế
                    $newRoom = Room::where('room_type_id', $room->room_type_id)
                        ->where('id', '!=', $room->id)
                        ->where('room_status', 'available')
                        ->whereDoesntHave('allocations', function ($q) use ($newCheckIn, $newCheckOut) {
                            $q->where('start_at', '<', $newCheckOut)
                                ->where('end_at', '>', $newCheckIn);
                        })->first();

                    if (! $newRoom) {
                        throw new \Exception("Không còn phòng loại {$room->roomType->name} trống cho thời gian mới.");
                    }

                    // Cập nhật phòng và giờ
                    $allocation->update([
                        'room_id'  => $newRoom->id,
                        'start_at' => $newCheckIn,
                        'end_at'   => $newCheckOut,
                    ]);

                    // Cập nhật trạng thái phòng
                    $room->update(['room_status' => 'available']);
                    $newRoom->update(['room_status' => 'booked']);
                } else {
                    // Chỉ cập nhật giờ
                    $allocation->update([
                        'start_at' => $newCheckIn,
                        'end_at'   => $newCheckOut,
                    ]);
                }
            }

            // Xử lý chênh lệch giá
            $difference = $newTotal - $originalTotal;

            if ($difference > 0) {
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => null,
                    'amount'            => $difference,
                    'entry_type'        => 'payment',
                    'status'            => 'pending',
                    'description'       => 'Phát sinh thêm do thay đổi ngày ở',
                ]);
            } elseif ($difference < 0) {
                $deposit = $booking->payments->firstWhere('description', 'Cọc 30%');
                BookingPayment::create([
                    'booking_id'        => $booking->id,
                    'payment_method_id' => $deposit->payment_method_id ?? null,
                    'amount'            => abs($difference),
                    'entry_type'        => 'refund',
                    'status'            => 'pending',
                    'description'       => 'Hoàn tiền do giảm số đêm',
                ]);
            }

            DB::commit();

            return response()->json(['message' => 'Cập nhật đặt phòng thành công!'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function surchargeModal(Booking $booking)
    {
        // load rooms + quan hệ fees đã chọn (nếu có)
        $booking->load(['booking_rooms.roomType', 'booking_rooms.surcharges']);

        // lấy all fee đang active, nhóm theo type
        $additionalFees = AdditionalFee::where('is_active', true)
            ->get()
            ->groupBy('type');

        // trả về partial view
        return view('admin.bookings.partials.surcharge_modal', compact('booking', 'additionalFees'));
    }
    public function updateFees(Request $request, $bookingId)
    {
        $data = $request->validate([
            'fees'   => 'array',
            'fees.*' => 'array',
        ]);

        DB::transaction(function() use ($data, $bookingId) {
            $booking = Booking::findOrFail($bookingId);
            $totalFeeChange = 0;

            // Lấy danh sách tất cả phòng thuộc booking này
            $bookingRooms = BookingRoom::where('booking_id', $bookingId)->get();

            foreach ($bookingRooms as $room) {
                $roomId = $room->id;
                $feeIds = $data['fees'][$roomId] ?? []; // lấy mảng hoặc mặc định rỗng

                $existing = BookingRoomFee::where('booking_room_id', $roomId)
                    ->pluck('fee_id')
                    ->toArray();

                $toRemove = array_diff($existing, $feeIds);
                $toAdd    = array_diff($feeIds, $existing);

                foreach ($toRemove as $feeId) {
                    $fee = AdditionalFee::find($feeId);
                    if (!$fee) continue;

                    BookingRoomFee::where('booking_room_id', $roomId)
                        ->where('fee_id', $feeId)
                        ->delete();

                    BookingPayment::create([
                        'booking_id'        => $bookingId,
                        'payment_method_id' => null,
                        'amount'            => $fee->default_price,
                        'entry_type'        => 'refund',
                        'status'            => 'paid',
                        'description'       => "Hoàn phụ phí “{$fee->name}” – phòng {$room->roomType->name}",
                        'paid_at'           => now(),
                    ]);

                    $totalFeeChange -= $fee->default_price;
                }

                foreach ($toAdd as $feeId) {
                    $fee = AdditionalFee::find($feeId);
                    if (!$fee) continue;

                    BookingRoomFee::create([
                        'booking_room_id' => $roomId,
                        'fee_id'          => $feeId,
                        'price'           => $fee->default_price,
                    ]);

                    BookingPayment::create([
                        'booking_id'        => $bookingId,
                        'payment_method_id' => null,
                        'amount'            => $fee->default_price,
                        'entry_type'        => 'charge',
                        'status'            => 'paid',
                        'description'       => "Phụ phí “{$fee->name}” – phòng {$room->roomType->name}",
                        'paid_at'           => now(),
                    ]);

                    $totalFeeChange += $fee->default_price;
                }
            }

            if ($totalFeeChange !== 0) {
                $booking->total_price += $totalFeeChange;
                $booking->save();
            }
        });

        return response()->json([
            'message' => 'Cập nhật phụ phí và thanh toán thành công!'
        ]);
    }
    public function changeRoomType(Request $request)
    {
        $data = $request->validate([
            'booking_room_id'   => 'required|exists:booking_rooms,id',
            'new_room_type_id'  => 'required|exists:room_types,id',
            'change_start_date' => 'required|date',
            'change_end_date'   => 'required|date|after_or_equal:change_start_date',
            'note'              => 'nullable|string',
        ]);

        $oldBR   = BookingRoom::findOrFail($data['booking_room_id']);
        $booking = Booking::findOrFail($oldBR->booking_id);

        // Lấy allocation theo ngày, giữ giờ gốc
        $alloc = BookingAllocation::where('booking_id', $booking->id)
            ->whereDate('start_at', $data['change_start_date'])
            ->whereDate('end_at', $data['change_end_date'])
            ->firstOrFail();

        $fromOriginal = Carbon::parse($alloc->start_at);
        $toOriginal   = Carbon::parse($alloc->end_at);

        // Tính số đêm dựa trên ngày (loại bỏ ảnh hưởng giờ)
        $fromDate = $fromOriginal->copy()->startOfDay();
        $toDate   = $toOriginal->copy()->startOfDay();
        $newNights = $fromDate->diffInDays($toDate);

        $oldPricePerNight = $oldBR->price_per_night;
        $originalSubtotal = $oldBR->sub_total;

        $newType          = RoomType::findOrFail($data['new_room_type_id']);
        $newPricePerNight = $newType->base_price;
        $newSubtotal      = $newPricePerNight * $newNights;

        try {
            DB::transaction(function () use (
                $booking, $oldBR, $alloc,
                $fromOriginal, $toOriginal,
                $newNights, $newPricePerNight,
                $oldPricePerNight, $originalSubtotal,
                $newType, $newSubtotal, $data
            ) {
                // Tìm phòng mới trống trong khoảng gốc
                $targetRoom = Room::where('room_type_id', $newType->id)
                    ->whereDoesntHave('allocations', function ($q) use ($fromOriginal, $toOriginal) {
                        $q->where('start_at', '<', $toOriginal)
                            ->where('end_at',   '>', $fromOriginal);
                    })
                    ->firstOrFail();

                // Ghi nhận lịch sử đổi phòng
                RoomChange::create([
                    'booking_id'             => $booking->id,
                    'booking_room_id'        => $oldBR->id,
                    'booking_allocation_id'  => $alloc->id,
                    'from_room_type_id'      => $oldBR->room_type_id,
                    'to_room_type_id'        => $newType->id,
                    'change_start_date'      => $fromOriginal,
                    'change_end_date'        => $toOriginal,
                    'changed_at'             => now(),
                    'note'                   => $data['note'] ?? null,
                ]);

                // Cập nhật BookingRoom
                $oldBR->update([
                    'room_type_id'    => $newType->id,
                    'room_id'         => $targetRoom->id,
                    'price_per_night' => $newPricePerNight,
                    'nights'          => $newNights,
                    'sub_total'       => $newSubtotal,
                ]);

                // Cập nhật allocation sang phòng mới
                $alloc->update([
                    'room_id'  => $targetRoom->id,
                    'start_at' => $fromOriginal,
                    'end_at'   => $toOriginal,
                ]);

                // Tính và tạo payment tương ứng
                $diff = $newSubtotal - $originalSubtotal;
                if ($diff > 0) {
                    BookingPayment::create([
                        'booking_id'        => $booking->id,
                        'payment_method_id' => null,
                        'amount'            => $diff,
                        'entry_type'        => 'adjustment',
                        'status'            => 'pending',
                        'description'       => 'Phụ thu đổi phòng',
                        'paid_at'           => null,
                    ]);
                    $booking->increment('total_price', $diff);
                } elseif ($diff < 0) {
                    $refund = abs($diff);
                    BookingPayment::create([
                        'booking_id'        => $booking->id,
                        'payment_method_id' => null,
                        'amount'            => $refund,
                        'entry_type'        => 'refund',
                        'status'            => 'pending',
                        'description'       => 'Hoàn tiền đổi phòng',
                        'paid_at'           => null,
                    ]);
                    $booking->decrement('total_price', $refund);
                }
            });

            return response()->json(['success' => true]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy phân đoạn cấp phòng hoặc không có phòng trống phù hợp.',
            ], 422);
        } catch (\Exception $e) {
            Log::error('ChangeRoomType error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?? 'Có lỗi xảy ra, vui lòng thử lại sau.',
            ], 500);
        }
    }

    public function calendar(Request $request)
    {
        // Lấy date từ query string (nếu có), mặc định là hôm nay
        $selectedDate = $request->query('date', now()->format('Y-m-d'));

        // Lấy danh sách bookings kèm thông tin phòng và CCCD
        $bookings = DB::table('bookings')
            ->join('booking_allocations', 'bookings.id', '=', 'booking_allocations.booking_id')
            ->join('rooms', 'rooms.id', '=', 'booking_allocations.room_id')
            ->select(
                'bookings.id as booking_id',
                'booking_allocations.room_id',
                'bookings.guest_name',
                'bookings.guest_email',
                'bookings.guest_phone',
                'bookings.guest_id_number',     // Thêm trường CCCD
                'bookings.status',
                'booking_allocations.start_at',
                'booking_allocations.end_at',
                'rooms.room_number'
            )
            ->get();

        // Chuyển bookings thành danh sách sự kiện FullCalendar
        $events = $bookings->map(function ($b) {
            // Gán màu theo trạng thái (tông màu dễ nhìn, phân biệt rõ)
            $color = match ($b->status) {
                'pending'    => '#FFD54F', // Vàng nhạt (chờ xác nhận)
                'confirmed'  => '#64B5F6', // Xanh dương nhạt (đã xác nhận)
                'checked_in' => '#81C784', // Xanh lá mạ (đã nhận phòng)
                'completed'  => '#4DB6AC', // Xanh ngọc nhạt (hoàn tất)
                'cancelled'  => '#E57373', // Đỏ nhạt (đã hủy)
                default      => '#B0BEC5', // Xám nhẹ (trạng thái không xác định)
            };



            // Gán nhãn trạng thái tiếng Việt
            $statusLabel = match ($b->status) {
                'checked_in'  => 'Đã nhận phòng',
                'pending'     => 'Đang chờ',
                'cancelled'   => 'Đã hủy',
                'completed'   => 'Hoàn thành',
                'confirmed'   => 'Đã xác nhận',
                default       => 'Không xác định',
            };

            // Format lại end_at (nếu muốn cộng thêm 1 ngày để FullCalendar bao gồm cả ngày checkout, có thể sử dụng addDay())
            $endAt = (new \DateTime($b->end_at))
                ->format('Y-m-d H:i:s');

            return [
                'id'    => $b->booking_id,
                'title' => "P{$b->room_number} - {$b->guest_name}",
                'start' => $b->start_at,
                'end'   => $endAt,
                'color' => $color,
                'extendedProps' => [
                    'booking_id'      => $b->booking_id,
                    'room_id'         => $b->room_id,
                    'guest_name'      => $b->guest_name,
                    'guest_email'     => $b->guest_email,
                    'guest_phone'     => $b->guest_phone,
                    'guest_id_number' => $b->guest_id_number,   // Hiển thị CCCD
                    'room_number'     => $b->room_number,
                    'status'          => $statusLabel,
                ],
            ];
        });

        // Lấy danh sách room_number để làm dropdown filter
        $roomNumbers = DB::table('rooms')->pluck('room_number');

        return view('admin.calendar', [
            'calendarEvents' => $events,
            'roomNumbers'    => $roomNumbers,
            'selectedDate'   => $selectedDate,
        ]);
    }
    public function getAvailableRooms(Request $request)
    {
        $checkIn = Carbon::parse($request->input('check_in'));
        $checkOut = Carbon::parse($request->input('check_out'));

        // Lấy tổng số phòng từng loại
        $roomTypes = RoomType::withCount('rooms')->get();

        // Lấy số phòng đã đặt trong khoảng ngày này, theo loại phòng
        $bookedRooms = BookingAllocation::whereHas('booking', function($q) use ($checkIn, $checkOut) {
            // Điều kiện trùng ngày:
            // booking.check_in < check_out AND booking.check_out > check_in
            $q->where('check_in', '<', $checkOut)
                ->where('check_out', '>', $checkIn);
        })
            ->selectRaw('room_type_id, count(*) as booked_count')
            ->groupBy('room_type_id')
            ->pluck('booked_count', 'room_type_id'); // mảng ['room_type_id' => số phòng đã đặt]

        // Tính số phòng còn lại
        $roomTypes->map(function ($roomType) use ($bookedRooms) {
            $booked = $bookedRooms->get($roomType->id, 0);
            $roomType->available_count = $roomType->rooms_count - $booked;
            return $roomType;
        });

        return response()->json($roomTypes);
    }
    public function createAdmin(Request $request)
    {
        try {
            if (! $request->has('check_in') || ! $request->has('check_out')) {
                // như cũ: lấy tất cả phòng trống hiện thời
                $roomTypes = RoomType::withCount(['rooms as available_count' => function ($q) {
                    $q->whereDoesntHave('allocations', function ($q2) {
                        $q2->whereNull('end_at');
                    });
                }])
                    ->with(['images', 'bedTypes'])
                    ->get();
            } else {
                $checkIn  = Carbon::parse($request->input('check_in'))->startOfDay();
                $checkOut = Carbon::parse($request->input('check_out'))->startOfDay();

                $roomTypes = RoomType::query()
                    ->withCount(['rooms as available_count' => function ($q) use ($checkIn, $checkOut) {
                        $q->whereDoesntHave('allocations', function ($q2) use ($checkIn, $checkOut) {
                            $q2->where('start_at', '<', $checkOut)
                                ->where(function ($q3) use ($checkIn) {
                                    $q3->whereNull('end_at')
                                        ->orWhere('end_at', '>', $checkIn);
                                });
                        });
                    }])
                    ->having('available_count', '>', 0)
                    ->with(['images', 'bedTypes'])
                    ->get();
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại sau!');
        }

        if ($roomTypes->isEmpty()) {
            return redirect()->back()->with('warning', 'Rất tiếc, hiện không có phòng trống phù hợp.');
        }

        // Phần còn lại giữ nguyên
        $maxAdults   = $roomTypes->max('max_adult');
        $maxChildren = $roomTypes->max('max_children');
        $pmethods    = PaymentMethod::where('is_active', 1)->pluck('name', 'id');
        $fees = AdditionalFee::where('is_active', 1)
            ->where('type', 'pre_fee')
            ->get();


        return view('admin.booking_offline', compact(
            'roomTypes', 'fees', 'pmethods', 'maxAdults', 'maxChildren'
        ));
    }
    public function storeAdmin(Request $request)
    {
        $validated = $request->validate([
            'check_in'             => 'required|date|after_or_equal:today',
            'check_out'            => 'required|date|after:check_in',
            'guest_name'           => 'required|string|max:255',
            'guest_email'          => 'required|email|max:255',
            'guest_phone'          => 'required|string|max:20',
            'payment_method_id'    => 'required|exists:payment_methods,id',
            'rooms'                => 'required|array|min:1',
            'rooms.*.room_type_id' => 'required|exists:room_types,id',
            'rooms.*.adults'       => 'required|integer|min:1',
            'rooms.*.children'     => 'required|integer|min:0',
            'rooms.*.fees'         => 'nullable|array',
            'rooms.*.fees.*'       => 'exists:additional_fees,id',
        ]);

        DB::beginTransaction();

        try {
            // Tính số đêm dựa trên ngày (startOfDay) để tránh lẻ ngày
            $startDate = Carbon::parse($validated['check_in'])->startOfDay();
            $endDate   = Carbon::parse($validated['check_out'])->startOfDay();
            $nights    = $startDate->diffInDays($endDate);

            // Gán giờ cố định: check-in 14:00, check-out 12:00
            $checkIn   = $startDate->setTime(14, 0);
            $checkOut  = $endDate->setTime(12, 0);

            // Tạo booking chính
            $booking = Booking::create([
                'booking_code'      => strtoupper(Str::random(8)),
                'user_id'           => auth()->id(),
                'guest_name'        => $validated['guest_name'],
                'guest_email'       => $validated['guest_email'],
                'guest_phone'       => $validated['guest_phone'],
                'check_in'          => $checkIn,
                'check_out'         => $checkOut,
                'num_adults'        => 0,
                'num_children'      => 0,
                'total_price'       => 0,
                'status'            => 'pending',
                'payment_method_id' => $validated['payment_method_id'],
            ]);

            $totalAdults   = 0;
            $totalChildren = 0;
            $totalPrice    = 0;
            $bookingRooms  = [];

            // Tạo booking_rooms và tính giá
            foreach ($validated['rooms'] as $roomData) {
                $roomType      = RoomType::findOrFail($roomData['room_type_id']);
                $adults        = $roomData['adults'];
                $children      = $roomData['children'];
                $pricePerNight = $roomType->base_price;
                $subTotal      = $pricePerNight * $nights;

                $totalAdults   += $adults;
                $totalChildren += $children;
                $totalPrice    += $subTotal;

                $br = BookingRoom::create([
                    'booking_id'      => $booking->id,
                    'room_type_id'    => $roomType->id,
                    'adults'          => $adults,
                    'children'        => $children,
                    'price_per_night' => $pricePerNight,
                    'nights'          => $nights,
                    'sub_total'       => $subTotal,
                ]);

                $bookingRooms[] = $br;
                if (!empty($roomData['fees'])) {
                    foreach ($roomData['fees'] as $feeId) {
                        $fee = AdditionalFee::findOrFail($feeId);
                        $totalPrice   += $fee->default_price;
                        BookingRoomFee::create([
                            'booking_room_id' => $br->id,
                            'fee_id'          => $fee->id,
                            'price'           => $fee->default_price,
                        ]);
                    }
                }
            }

            // Tạo thanh toán (Cọc và phần còn lại)
            $depositAmount   = round($totalPrice * 0.3, 2);
            $remainingAmount = $totalPrice - $depositAmount;

            BookingPayment::create([
                'booking_id'        => $booking->id,
                'payment_method_id' => $validated['payment_method_id'],
                'amount'            => $depositAmount,
                'entry_type'        => 'deposit',
                'status'            => 'paid',
                'description'       => 'Cọc 30%',
                'paid_at'           => now(),
            ]);

            BookingPayment::create([
                'booking_id'        => $booking->id,
                'payment_method_id' => null,
                'amount'            => $remainingAmount,
                'entry_type'        => 'payment',
                'status'            => 'pending',
                'description'       => 'Thanh toán phần còn lại',
                'paid_at'           => null,
            ]);

            // Tạo booking_allocations với giờ cụ thể
            foreach ($bookingRooms as $br) {
                $room = Room::where('room_type_id', $br->room_type_id)
                    ->whereDoesntHave('allocations', function ($q) use ($checkIn, $checkOut) {
                        $q->where('start_at', '<',  $checkOut)
                            ->where('end_at',   '>',  $checkIn);
                    })
                    ->first();

                if (!$room) {
                    throw new \Exception("Không còn phòng loại {$br->roomType->name} cho khoảng thời gian này.");
                }

                BookingAllocation::create([
                    'booking_id' => $booking->id,
                    'room_id'    => $room->id,
                    'start_at'   => $checkIn,
                    'end_at'     => $checkOut,
                ]);
            }

            // Cập nhật tổng khách và tổng giá
            $booking->update([
                'num_adults'   => $totalAdults,
                'num_children' => $totalChildren,
                'total_price'  => $totalPrice,
            ]);

            DB::commit();

            return redirect()->route('admin.bookings.index')
                ->with('success', 'Đặt phòng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Lỗi: ' . $e->getMessage()]);
        }
    }
    public function invoicePage(Booking $booking)
    {
        $booking->load([
            'user',
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room',
        ]);

        return view('admin.invoice', compact('booking'));
    }
    public function downloadInvoice(Booking $booking)
    {
        $booking->load([
            'user',
            'bookingRooms.roomType',
            'bookingRooms.surcharges',
            'payments.paymentMethod',
            'allocations.room',
        ]);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.invoice', [
            'booking' => $booking,
            'isPdf' => true, // truyền biến này vào view
        ]);

        return $pdf->download("hoa_don_{$booking->id}.pdf");
    }

}
