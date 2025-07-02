<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingPayment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        $bookings = Booking::with(['payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.payments', compact('bookings'));
    }

    public function show(BookingPayment $payment)
    {
        $payment->load('booking', 'paymentMethod');
        return view('admin.payments.show', compact('payment'));
    }

    public function updateStatus(Request $request, $id)
    {
        $payment = BookingPayment::findOrFail($id);
        $payment->status = $request->status;
        $payment->save();

        return response()->json(['message' => 'Cập nhật thành công']);
    }


    /**
     * Sinh URL sandbox VNPAY cho số tiền cọc (session('booking_amount')), rồi redirect.
     */
    public function vnpaySandbox(Request $request)
    {
        // 1. Lấy số tiền cọc đã lưu trong session
        if (!session()->has('booking_amount')) {
            abort(400, 'Thiếu thông tin số tiền thanh toán.');
        }
        $amount = session('booking_amount');

        // 2. Sinh orderId (UUID) để lưu vào transaction_id
        $orderId = Str::uuid()->toString();

        // 3. Chuẩn bị tham số VNPAY (lấy từ .env)
        $vnp_TmnCode    = env('VNPAY_TMN_CODE');
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $vnp_Version    = '2.1.0';
        $vnp_Command    = 'pay';
        $vnp_CurrCode   = 'VND';
        $vnp_Locale     = 'vn';
        $vnp_TxnRef     = $orderId;
        $vnp_OrderInfo  = 'Cọc Booking Hanoi Hotel';
        $vnp_OrderType  = 'other';
        $vnp_ReturnUrl  = route('payments.vnpay.return');
        $vnp_IpAddr     = $request->ip();
        $vnp_CreateDate = date('YmdHis');
        $vnp_Amount     = $amount * 100; // VNPAY yêu cầu nhân 100

        $inputData = [
            'vnp_Version'     => $vnp_Version,
            'vnp_Command'     => $vnp_Command,
            'vnp_TmnCode'     => $vnp_TmnCode,
            'vnp_Amount'      => $vnp_Amount,
            'vnp_CurrCode'    => $vnp_CurrCode,
            'vnp_TxnRef'      => $vnp_TxnRef,
            'vnp_OrderInfo'   => $vnp_OrderInfo,
            'vnp_OrderType'   => $vnp_OrderType,
            'vnp_Locale'      => $vnp_Locale,
            'vnp_ReturnUrl'   => $vnp_ReturnUrl,
            'vnp_IpAddr'      => $vnp_IpAddr,
            'vnp_CreateDate'  => $vnp_CreateDate,
        ];

        // 4. Sort và build hashData + queryString
        ksort($inputData);
        $hashData    = '';
        $queryString = '';
        foreach ($inputData as $key => $value) {
            if (strlen($value) > 0) {
                $hashData    .= $key . '=' . $value . '&';
                $queryString .= urlencode($key) . '=' . urlencode($value) . '&';
            }
        }
        $hashData    = rtrim($hashData, '&');
        $queryString = rtrim($queryString, '&');

        // 5. Tính chữ ký SHA512
        $vnp_SecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        // 6. Build URL sandbox VNPAY
        $vnp_Url     = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
        $paymentUrl  = $vnp_Url . '?' . $queryString . '&vnp_SecureHash=' . $vnp_SecureHash;

        // 7. Lưu orderId vào session để callback có thể lấy transaction_id
        session([
            'vnpay_order_id' => $orderId,
        ]);

        // 8. Redirect sang sandbox VNPAY
        return redirect()->away($paymentUrl);
    }

    /**
     * Nhận callback từ VNPAY, verify chữ ký, trả về view
     * payments.success nếu thành công, hoặc payments.failed nếu thất bại.
     */
    public function vnpayReturn(Request $request)
    {
        // 1. Lấy toàn bộ tham số trả về
        $inputData       = $request->all();
        $vnp_SecureHash  = $request->get('vnp_SecureHash');
        $vnp_ResponseCode= $request->get('vnp_ResponseCode');

        // 2. Xây dựng lại mảng để tính hash
        $dataHash = [];
        foreach ($inputData as $key => $value) {
            if (substr($key, 0, 4) === 'vnp_' && $key !== 'vnp_SecureHash') {
                $dataHash[$key] = $value;
            }
        }
        ksort($dataHash);
        $hashData = '';
        foreach ($dataHash as $key => $value) {
            if (strlen($value) > 0) {
                $hashData .= $key . '=' . $value . '&';
            }
        }
        $hashData = rtrim($hashData, '&');

        // 3. Tạo lại chữ ký và so sánh
        $vnp_HashSecret = env('VNPAY_HASH_SECRET');
        $secureHash     = hash_hmac('sha512', $hashData, $vnp_HashSecret);

        if ($secureHash === $vnp_SecureHash) {
            if ($vnp_ResponseCode === '00') {
                // Thanh toán cọc thành công
                return response()->view('payments.success')->header('Content-Type', 'text/html');
            } else {
                // Thanh toán thất bại
                return response()->view('payments.failed')->header('Content-Type', 'text/html');
            }
        } else {
            return response('Invalid signature', 400);
        }
    }


}

