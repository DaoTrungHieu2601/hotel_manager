<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Services\CheckoutTotalsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        private CheckoutTotalsService $checkoutTotals
    ) {}

    private function finalizeReceptionCheckout(Booking $booking): void
    {
        if ($booking->invoice) {
            return;
        }

        $room = $booking->room;
        $totals = $this->checkoutTotals->build($booking);
        $invoiceNo = 'INV-'.now()->format('Y').'-'.str_pad((string) (Invoice::query()->count() + 1), 5, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($booking, $room, $totals, $invoiceNo) {
            Invoice::query()->create([
                'booking_id' => $booking->id,
                'invoice_number' => $invoiceNo,
                'nights' => $totals['nights'],
                'room_subtotal' => $totals['roomSubtotal'],
                'services_subtotal' => $totals['servicesSubtotal'],
                'early_late_subtotal' => $totals['earlyLateSubtotal'],
                'deposit' => $totals['deposit'],
                'total' => $totals['total'],
                'issued_at' => now(),
            ]);

            $booking->update([
                'status' => Booking::STATUS_CHECKED_OUT,
                'checked_out_at' => now(),
            ]);

            if ($room && $room->status === Room::STATUS_OCCUPIED) {
                $room->update(['status' => Room::STATUS_CLEANING]);
            }
        });
    }

    /**
     * POST /vnpay/create-payment
     *
     * Refactor từ vnpay_create_payment.php:
     * - Nhận amount (VND), language, bankCode, order_id, order_info (tùy chọn).
     * - Tạo URL thanh toán VNPAY và redirect.
     */
    public function create(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'integer', 'min:1'],
            'language' => ['nullable', 'string', 'max:5'],
            'bankCode' => ['nullable', 'string', 'max:20'],
            'order_id' => ['nullable', 'string', 'max:64'],
            'order_info' => ['nullable', 'string', 'max:255'],
        ]);

        $tmnCode = (string) config('services.vnpay.tmn_code');
        $hashSecret = (string) config('services.vnpay.hash_secret');
        $baseUrl = (string) config('services.vnpay.url');
        $returnUrl = (string) config('services.vnpay.return_url');

        abort_unless($tmnCode !== '' && $hashSecret !== '' && $baseUrl !== '' && $returnUrl !== '', 500);

        // Tương đương $vnp_TxnRef (mã giao dịch tham chiếu)
        $txnRef = $validated['order_id'] ?? (string) random_int(1, 10000);
        $locale = $validated['language'] ?? 'vn';
        $bankCode = $validated['bankCode'] ?? null;

        $orderInfo = $validated['order_info'] ?? ('Thanh toan GD:' . $txnRef);

        // vnp_Amount = amount * 100
        $vnpAmount = (int) $validated['amount'] * 100;
        $now = now('Asia/Ho_Chi_Minh');

        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $tmnCode,
            'vnp_Amount' => $vnpAmount,
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => $now->format('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => (string) $request->ip(),
            'vnp_Locale' => $locale,
            'vnp_OrderInfo' => $orderInfo,
            'vnp_OrderType' => 'other',
            'vnp_ReturnUrl' => $returnUrl,
            'vnp_TxnRef' => $txnRef,
            'vnp_ExpireDate' => $now->copy()->addMinutes(15)->format('YmdHis'),
        ];

        if ($bankCode) {
            $inputData['vnp_BankCode'] = $bankCode;
        }

        ksort($inputData);

        $query = '';
        $hashData = '';
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode((string) $key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode((string) $key) . '=' . urlencode((string) $value);
                $i = 1;
            }
            $query .= urlencode((string) $key) . '=' . urlencode((string) $value) . '&';
        }

        $vnpUrl = rtrim($baseUrl, '?') . '?' . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashData, $hashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;

        return redirect()->away($vnpUrl);
    }

    /**
     * GET /vnpay/vnpay-return
     *
     * Refactor từ vnpay_return.php:
     * - Đọc các tham số vnp_* từ query
     * - Tính lại SecureHash và so sánh
     * - Thông báo kết quả; đồng thời, nếu mapping với Booking draft thì cập nhật cọc.
     */
    public function vnpayReturn(Request $request): View|RedirectResponse
    {
        $hashSecret = (string) config('services.vnpay.hash_secret');
        abort_unless($hashSecret !== '', 500);

        $inputData = [];
        foreach ($request->query() as $key => $value) {
            if (str_starts_with((string) $key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        $vnpSecureHash = (string) ($inputData['vnp_SecureHash'] ?? '');
        unset($inputData['vnp_SecureHash'], $inputData['vnp_SecureHashType']);

        ksort($inputData);
        $i = 0;
        $hashData = '';
        foreach ($inputData as $key => $value) {
            if ($i === 1) {
                $hashData .= '&' . urlencode((string) $key) . '=' . urlencode((string) $value);
            } else {
                $hashData .= urlencode((string) $key) . '=' . urlencode((string) $value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $hashSecret);

        $responseCode = (string) ($request->query('vnp_ResponseCode') ?? '');
        $txnRef = (string) ($request->query('vnp_TxnRef') ?? '');
        $amount = $request->has('vnp_Amount') ? ((int) $request->query('vnp_Amount')) / 100 : null;

        $isValidSignature = hash_equals($secureHash, $vnpSecureHash);
        $success = $isValidSignature && $responseCode === '00';

        // Flow checkout cho le tan/admin: txnRef dang "RCO-{bookingId}-{timestamp}"
        if ($success && str_starts_with($txnRef, 'RCO-')) {
            $parts = explode('-', $txnRef);
            $bookingId = isset($parts[1]) && ctype_digit($parts[1]) ? (int) $parts[1] : null;
            $booking = $bookingId ? Booking::query()->with(['room', 'roomType', 'bookingServices'])->find($bookingId) : null;

            if (! $booking || $booking->status !== Booking::STATUS_CHECKED_IN) {
                return redirect()
                    ->route('reception.dashboard')
                    ->with('error', __('Thanh toán VNPAY thất bại: không tìm thấy đơn check-out hợp lệ.'));
            }

            $expected = (float) $this->checkoutTotals->build($booking)['total'];
            $amountOk = $amount !== null && abs(((float) $amount) - $expected) < 0.01;
            if (! $amountOk) {
                return redirect()
                    ->route('reception.reservations.show', $booking)
                    ->with('error', __('Thanh toán VNPAY thất bại: số tiền không khớp.'));
            }

            $this->finalizeReceptionCheckout($booking);

            // Một lần redirect thôi: tránh mất flash khi /dashboard lại redirect tiếp sang admin/reception.
            return redirect()
                ->route('reception.reservations.show', $booking->fresh(['invoice', 'room', 'roomType', 'user', 'bookingServices.service']))
                ->with('status', __('Thanh toán VNPAY thành công. Đã check-out và tạo hóa đơn — bạn có thể in hóa đơn bên dưới.'));
        }

        // Flow dat coc cua khach (draft booking)
        if ($success) {
            $bookingId = ctype_digit($txnRef) ? (int) $txnRef : null;
            $booking = $bookingId ? Booking::query()->find($bookingId) : null;

            if ($booking && $booking->status === Booking::STATUS_DRAFT) {
                $expected = (float) $booking->deposit_amount;
                $amountOk = $amount !== null && abs(((float) $amount) - $expected) < 0.01;

                if ($expected > 0 && $amountOk) {
                    if ($booking->deposit_paid_at === null) {
                        $booking->update([
                            'deposit_paid_at' => now(),
                            'payment_method' => 'vnpay',
                        ]);
                    }

                    // Luôn dùng URL có chữ ký: sau khi quay lại từ VNPAY, session/cookie đôi khi không gửi kèm
                    // (localhost vs 127.0.0.1, trình duyệt/SameSite...), không phụ thuộc auth()->check().
                    return redirect()
                        ->to(URL::temporarySignedRoute(
                            'booking.vnpay-continue',
                            now()->addMinutes(60),
                            ['booking' => $booking->fresh()]
                        ))
                        ->with('status', __('Thanh toán VNPAY thành công. Vui lòng xem lại đơn và xác nhận gửi đơn.'));
                }

                $success = false;
            }
        }

        return view('payments.vnpay-return', [
            'success' => $success,
            'isValidSignature' => $isValidSignature,
            'responseCode' => $responseCode,
            'txnRef' => $txnRef,
            'amount' => $amount,
            'raw' => $request->query(),
        ]);
    }
}

