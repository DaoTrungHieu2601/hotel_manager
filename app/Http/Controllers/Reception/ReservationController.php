<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Mail\BookingConfirmedMail;
use App\Models\Booking;
use App\Models\Room;
use App\Models\Service;
use App\Notifications\BookingCancelledNotification;
use App\Notifications\BookingConfirmedNotification;
use App\Services\CheckoutTotalsService;
use App\Services\RoomAvailabilityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class ReservationController extends Controller
{
    public function __construct(
        private RoomAvailabilityService $availability,
        private CheckoutTotalsService $checkoutTotals,
    ) {}

    public function index(Request $request): View
    {
        $tab = $request->query('tab', 'pending');
        $q = Booking::query()->with(['user', 'roomType', 'room'])->orderByDesc('created_at');

        if ($tab === 'pending') {
            $q->where('status', Booking::STATUS_PENDING);
        } elseif ($tab === 'active') {
            $q->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_CHECKED_IN]);
        } else {
            $q->whereIn('status', [Booking::STATUS_CHECKED_OUT, Booking::STATUS_CANCELLED]);
        }

        $bookings = $q->paginate(20)->withQueryString();

        return view('reception.reservations.index', compact('bookings', 'tab'));
    }

    public function show(Booking $booking): View
    {
        $booking->load(['user', 'roomType', 'room', 'bookingServices.service', 'invoice']);
        $services = Service::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $assignableRooms = Room::query()
            ->where('room_type_id', $booking->room_type_id)
            ->orderBy('code')
            ->get()
            ->filter(fn (Room $r) => $this->availability->roomIsFree($r->id, $booking->check_in, $booking->check_out, $booking->id))
            ->values();

        $selectedQuantities = $booking->bookingServices
            ->mapWithKeys(fn ($line) => [$line->service_id => (int) $line->quantity]);

        $roomSubtotal = (float) ($booking->rate_per_night ?? $booking->roomType->default_price) * $booking->nights();
        $servicesSubtotal = (float) $booking->bookingServices->sum(fn ($line) => (float) $line->lineTotal());
        $deposit = (float) $booking->deposit_amount;
        $earlyLateSubtotal = 0.0;
        $earlyLateHours = 0;
        if ($booking->status === Booking::STATUS_CHECKED_IN && $booking->checked_in_at) {
            $t = $this->checkoutTotals->build($booking);
            $earlyLateSubtotal = (float) $t['earlyLateSubtotal'];
            $earlyLateHours = (int) $t['earlyLateHours'];
        }
        $estimatedTotal = max(0, $roomSubtotal + $servicesSubtotal + $earlyLateSubtotal - $deposit);

        return view('reception.reservations.show', compact(
            'booking',
            'assignableRooms',
            'services',
            'selectedQuantities',
            'roomSubtotal',
            'servicesSubtotal',
            'deposit',
            'earlyLateSubtotal',
            'earlyLateHours',
            'estimatedTotal'
        ));
    }

    public function confirm(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->status === Booking::STATUS_PENDING, 400);

        if ($booking->room_id) {
            $room = $booking->room;
            abort_unless($room, 404);
            if (! $this->availability->roomIsFree($room->id, $booking->check_in, $booking->check_out, $booking->id)) {
                return redirect()->back()->with('error', __('Phòng không còn trống cho khoảng thời gian này.'));
            }
        } else {
            $data = $request->validate([
                'room_id' => ['required', 'exists:rooms,id'],
            ]);

            $room = Room::query()->findOrFail($data['room_id']);
            abort_unless($room->room_type_id === $booking->room_type_id, 422);

            if (! $this->availability->roomIsFree($room->id, $booking->check_in, $booking->check_out, $booking->id)) {
                return redirect()->back()->with('error', __('Phòng đã bận trong khoản thời gian này.'));
            }
        }

        $booking->update([
            'room_id' => $room->id,
            'status' => Booking::STATUS_CONFIRMED,
            'rate_per_night' => $booking->roomType->default_price,
            'confirmed_at' => now(),
        ]);

        if (in_array($room->status, [Room::STATUS_AVAILABLE, Room::STATUS_CLEANING], true)) {
            $room->update(['status' => Room::STATUS_BOOKED]);
        }

        $fresh = $booking->fresh(['user', 'room', 'roomType']);
        $mailFailed = false;
        try {
            Mail::to($fresh->user->email)->send(new BookingConfirmedMail($fresh));
        } catch (Throwable $e) {
            $mailFailed = true;
            Log::warning('Send booking confirmation email failed', [
                'booking_id' => $fresh->id,
                'user_id' => $fresh->user_id,
                'error' => $e->getMessage(),
            ]);
        }
        $fresh->user->notify(new BookingConfirmedNotification($fresh));

        $status = $mailFailed
            ? __('Đã xác nhận đơn. Không thể gửi email lúc này, vui lòng kiểm tra cấu hình mail.')
            : __('Đã xác nhận đơn và gửi email.');

        return redirect()->route('reception.reservations.show', $booking)->with('status', $status);
    }

    public function cancel(Booking $booking): RedirectResponse
    {
        if (in_array($booking->status, [Booking::STATUS_CHECKED_OUT], true)) {
            return redirect()->back()->with('error', __('Không thể hủy đơn đã trả phòng.'));
        }

        if ($booking->room_id && $booking->room) {
            $room = $booking->room;
            if ($room->status === Room::STATUS_BOOKED) {
                $room->update(['status' => Room::STATUS_AVAILABLE]);
            }
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        $booking->load(['user', 'room', 'roomType']);
        if ($booking->user) {
            $booking->user->notify(new BookingCancelledNotification($booking));
        }

        return redirect()->route('reception.reservations.index')->with('status', __('Đã hủy đặt phòng.'));
    }
}
