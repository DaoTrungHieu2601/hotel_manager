<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\CheckedInNotification;
use App\Notifications\CheckedOutNotification;
use App\Services\CheckoutTotalsService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StayController extends Controller
{
    public function __construct(
        private CheckoutTotalsService $checkoutTotals
    ) {}

    private function finalizeCheckoutInvoice(Booking $booking): void
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

            if ($room) {
                $room->update(['status' => Room::STATUS_CLEANING]);
            }
        });
    }

    public function checkIn(Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_CONFIRMED, 400);
        abort_unless($booking->room_id, 400);

        $room = $booking->room;
        abort_if($room->status === Room::STATUS_MAINTENANCE, 400);

        return redirect()->route('reception.stays.check-in-form', $booking);
    }

    public function checkInForm(Booking $booking): View
    {
        abort_unless($booking->status === Booking::STATUS_CONFIRMED, 400);
        abort_unless($booking->room_id, 400);
        $booking->load(['user', 'roomType', 'room']);
        $siteSetting = SiteSetting::instance();
        [$checkInSurcharge, $checkInSurchargeHours] = $this->checkoutTotals->previewCheckInEarlyFee($booking, now());
        $referenceCheckInTime = $this->normalizeReferenceCheckInTime(
            $booking->guest_planned_check_in,
            $siteSetting->policy_check_in_start ?? '08:00'
        );

        return view('reception.reservations.check-in-form', compact(
            'booking',
            'siteSetting',
            'checkInSurcharge',
            'checkInSurchargeHours',
            'referenceCheckInTime'
        ));
    }

    public function completeCheckIn(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_CONFIRMED, 400);
        abort_unless($booking->room_id, 400);

        $room = $booking->room;
        abort_if($room->status === Room::STATUS_MAINTENANCE, 400);

        $data = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'cccd' => ['required', 'regex:/^[0-9]{9,12}$/'],
            'client_checked_in_at' => ['nullable', 'date'],
            'effective_planned_check_in' => ['nullable', 'date_format:H:i'],
        ], [
            'cccd.regex' => __('Số CCCD chỉ gồm chữ số và có độ dài 9-12 ký tự.'),
        ]);

        $checkedInAt = ! empty($data['client_checked_in_at'])
            ? Carbon::parse($data['client_checked_in_at'])
            : now();

        $effectivePlannedCheckIn = $this->normalizeReferenceCheckInTime(
            $data['effective_planned_check_in'] ?? $booking->guest_planned_check_in,
            SiteSetting::instance()->policy_check_in_start ?? '08:00'
        );

        DB::transaction(function () use ($booking, $room, $data, $checkedInAt, $effectivePlannedCheckIn) {
            $booking->user->update([
                'phone' => $data['phone'],
                'cccd' => $data['cccd'],
            ]);

            $booking->update([
                'status' => Booking::STATUS_CHECKED_IN,
                'checked_in_at' => $checkedInAt,
                'guest_planned_check_in' => $effectivePlannedCheckIn,
            ]);
            $room->update(['status' => Room::STATUS_OCCUPIED]);
        });

        $booking->load(['user', 'room', 'roomType']);
        $admins = User::query()->where('role', User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new CheckedInNotification($booking));
        }

        return redirect()->route('reception.reservations.show', $booking)->with('status', __('Check-in thành công sau khi đã bổ sung thông tin khách.'));
    }

    private function normalizeReferenceCheckInTime(?string $value, string $fallback): string
    {
        $time = trim((string) $value);
        if ($time === '' || $time === '00:00') {
            return $fallback;
        }

        return $time;
    }

    public function checkOut(Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_CHECKED_IN, 400);
        return redirect()->route('reception.stays.checkout-payment', $booking);
    }

    public function checkoutPayment(Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_CHECKED_IN, 400);
        if ($booking->invoice) {
            return redirect()->route('reception.reservations.show', $booking)->with('error', __('Đã có hóa đơn cho đơn này.'));
        }

        $booking->load(['user', 'roomType', 'room', 'bookingServices.service']);
        $totals = $this->checkoutTotals->build($booking);

        return view('reception.reservations.checkout-payment', [
            'booking' => $booking,
            'totalPayable' => $totals['total'],
            'roomSubtotal' => $totals['roomSubtotal'],
            'servicesSubtotal' => $totals['servicesSubtotal'],
            'earlyLateSubtotal' => $totals['earlyLateSubtotal'],
            'earlyLateHours' => $totals['earlyLateHours'],
            'deposit' => $totals['deposit'],
            'nights' => $totals['nights'],
        ]);
    }

    public function checkOutCash(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_CHECKED_IN, 400);
        if ($booking->invoice) {
            return redirect()->route('reception.reservations.show', $booking)->with('error', __('Đã có hóa đơn cho đơn này.'));
        }

        $request->validate([
            'cash_received_confirmed' => ['accepted'],
        ], [
            'cash_received_confirmed.accepted' => __('Vui lòng xác nhận đã thu tiền mặt trước khi in hóa đơn.'),
        ]);

        $fresh = $booking->fresh(['room', 'roomType', 'bookingServices', 'user']);
        $this->finalizeCheckoutInvoice($fresh);
        $fresh->update(['payment_method' => 'cash']);

        $admins = User::query()->where('role', User::ROLE_ADMIN)->get();
        foreach ($admins as $admin) {
            $admin->notify(new CheckedOutNotification($fresh));
        }
        if ($fresh->user) {
            $fresh->user->notify(new CheckedOutNotification($fresh));
        }

        return redirect()->route('reception.reservations.show', $booking)->with('status', __('Đã check-out và thu tiền mặt thành công.'));
    }
}
