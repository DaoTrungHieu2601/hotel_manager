<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SiteSetting;
use App\Services\CustomerBookingSubmitService;
use App\Services\RoomAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function __construct(
        private RoomAvailabilityService $availability,
        private CustomerBookingSubmitService $submitBooking
    ) {}

    public function createForRoom(Request $request, Room $room): View|RedirectResponse
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ]);

        $from = Carbon::parse($validated['check_in'])->startOfDay();
        $to = Carbon::parse($validated['check_out'])->startOfDay();
        $guests = (int) $validated['guests'];

        $type = $room->roomType;
        if ($type->max_occupancy < $guests) {
            return redirect()
                ->route('guest.search-rooms', ['check_in' => $from->toDateString(), 'check_out' => $to->toDateString(), 'guests' => $guests])
                ->with('error', __('Số khách vượt sức chứa loại phòng.'));
        }

        if ($room->status === Room::STATUS_MAINTENANCE) {
            return redirect()
                ->route('guest.search-rooms', ['check_in' => $from->toDateString(), 'check_out' => $to->toDateString(), 'guests' => $guests])
                ->with('error', __('Phòng đang bảo trì.'));
        }

        if (! $this->availability->roomIsFree($room->id, $from, $to)) {
            return redirect()
                ->route('guest.search-rooms', ['check_in' => $from->toDateString(), 'check_out' => $to->toDateString(), 'guests' => $guests])
                ->with('error', __('Phòng không còn trống trong khoảng thời gian đã chọn.'));
        }

        [$stayNights, $roomSubtotal, $requiredDeposit] = $this->roomSubtotalAndDeposit(
            $from,
            $to,
            (float) $type->default_price
        );

        return view('customer.booking.create-room', [
            'room' => $room->load('roomType'),
            'check_in' => $from,
            'check_out' => $to,
            'guests' => $guests,
            'siteSetting' => SiteSetting::instance(),
            'stayNights' => $stayNights,
            'roomSubtotal' => $roomSubtotal,
            'requiredDeposit' => $requiredDeposit,
        ]);
    }

    public function storeForRoom(Request $request, Room $room): RedirectResponse
    {
        $validated = $request->validate([
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
            'guest_notes' => ['nullable', 'string', 'max:1000'],
            'guest_planned_check_in' => ['nullable', 'date_format:H:i'],
            'guest_planned_check_out' => ['nullable', 'date_format:H:i'],
        ]);

        $from = Carbon::parse($validated['check_in'])->startOfDay();
        $to = Carbon::parse($validated['check_out'])->startOfDay();
        $guests = (int) $validated['guests'];
        $type = $room->roomType;

        if ($type->max_occupancy < $guests) {
            return redirect()->back()->withInput()->with('error', __('Số khách không phù hợp.'));
        }

        if ($room->status === Room::STATUS_MAINTENANCE || ! $this->availability->roomIsFree($room->id, $from, $to)) {
            return redirect()->back()->withInput()->with('error', __('Phòng không khả dụng.'));
        }

        [, , $deposit] = $this->roomSubtotalAndDeposit($from, $to, (float) $type->default_price);

        $plannedIn = $validated['guest_planned_check_in'] ?? null;
        $plannedOut = $validated['guest_planned_check_out'] ?? null;

        $booking = Booking::query()->create([
            'user_id' => $request->user()->id,
            'room_type_id' => $type->id,
            'room_id' => $room->id,
            'check_in' => $from,
            'check_out' => $to,
            'guests' => $guests,
            'status' => Booking::STATUS_DRAFT,
            'deposit_amount' => $deposit,
            'guest_notes' => $validated['guest_notes'] ?? null,
            'guest_planned_check_in' => $plannedIn !== '' ? $plannedIn : null,
            'guest_planned_check_out' => $plannedOut !== '' ? $plannedOut : null,
            'rate_per_night' => $type->default_price,
        ]);

        if ($deposit <= 0) {
            return redirect()
                ->route('customer.bookings.review', $booking);
        }

        return redirect()
            ->route('customer.bookings.payment', $booking);
    }

    private function roomSubtotalAndDeposit(Carbon $from, Carbon $to, float $ratePerNight): array
    {
        $nights = max(1, $from->diffInDays($to));
        $roomSubtotal = $ratePerNight * $nights;
        $deposit = round($roomSubtotal * 0.30, 2);

        return [$nights, $roomSubtotal, $deposit];
    }

    public function create(Request $request): View|RedirectResponse
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
        ]);

        $type = RoomType::query()->findOrFail($validated['room_type_id']);
        $from = Carbon::parse($validated['check_in'])->startOfDay();
        $to = Carbon::parse($validated['check_out'])->startOfDay();

        if ($type->max_occupancy < (int) $validated['guests']) {
            return redirect()->back()->with('error', __('Số khách vượt sức chứa loại phòng.'));
        }

        if ($this->availability->countAvailableRoomsForType($type->id, $from, $to) < 1) {
            return redirect()->route('guest.search-rooms', [
                'check_in' => $from->toDateString(),
                'check_out' => $to->toDateString(),
                'guests' => $validated['guests'],
            ])->with('error', __('Loại phòng đã hết trong khoảng thời gian này.'));
        }

        [$stayNights, $roomSubtotal, $requiredDeposit] = $this->roomSubtotalAndDeposit(
            $from,
            $to,
            (float) $type->default_price
        );

        return view('customer.booking.create', [
            'roomType' => $type,
            'check_in' => $from,
            'check_out' => $to,
            'guests' => (int) $validated['guests'],
            'siteSetting' => SiteSetting::instance(),
            'stayNights' => $stayNights,
            'roomSubtotal' => $roomSubtotal,
            'requiredDeposit' => $requiredDeposit,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests' => ['required', 'integer', 'min:1'],
            'guest_notes' => ['nullable', 'string', 'max:1000'],
            'guest_planned_check_in' => ['nullable', 'date_format:H:i'],
            'guest_planned_check_out' => ['nullable', 'date_format:H:i'],
        ]);

        $type = RoomType::query()->findOrFail($validated['room_type_id']);
        $from = Carbon::parse($validated['check_in'])->startOfDay();
        $to = Carbon::parse($validated['check_out'])->startOfDay();

        if ($type->max_occupancy < (int) $validated['guests']) {
            return redirect()->back()->withInput()->with('error', __('Số khách không phù hợp.'));
        }

        if ($this->availability->countAvailableRoomsForType($type->id, $from, $to) < 1) {
            return redirect()->back()->withInput()->with('error', __('Không còn phòng trống cho loại này.'));
        }

        [, , $deposit] = $this->roomSubtotalAndDeposit($from, $to, (float) $type->default_price);

        $plannedIn = $validated['guest_planned_check_in'] ?? null;
        $plannedOut = $validated['guest_planned_check_out'] ?? null;

        $booking = Booking::query()->create([
            'user_id' => $request->user()->id,
            'room_type_id' => $type->id,
            'check_in' => $from,
            'check_out' => $to,
            'guests' => (int) $validated['guests'],
            'status' => Booking::STATUS_DRAFT,
            'deposit_amount' => $deposit,
            'guest_notes' => $validated['guest_notes'] ?? null,
            'guest_planned_check_in' => $plannedIn !== '' ? $plannedIn : null,
            'guest_planned_check_out' => $plannedOut !== '' ? $plannedOut : null,
            'rate_per_night' => $type->default_price,
        ]);

        if ($deposit <= 0) {
            return redirect()
                ->route('customer.bookings.review', $booking);
        }

        return redirect()
            ->route('customer.bookings.payment', $booking);
    }

    public function review(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === Booking::STATUS_DRAFT, 404);

        if ($booking->depositOutstanding()) {
            return redirect()
                ->route('customer.bookings.payment', $booking);
        }

        $booking->load(['roomType', 'room']);

        return view('customer.booking.review', [
            'booking' => $booking,
            'siteSetting' => SiteSetting::instance(),
        ]);
    }

    public function confirmReview(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === Booking::STATUS_DRAFT, 400);

        if ($booking->depositOutstanding()) {
            return redirect()
                ->route('customer.bookings.payment', $booking)
                ->with('error', __('Vui lòng hoàn tất thanh toán cọc trước khi xác nhận đơn.'));
        }

        $result = $this->submitBooking->submitDraftBooking($booking->fresh());

        if (! $result['ok']) {
            return redirect()
                ->route('customer.bookings.review', $booking)
                ->with('error', $result['error']);
        }

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with('status', __('Đơn đã gửi — chờ lễ tân xác nhận.'));
    }

    public function payment(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === Booking::STATUS_DRAFT, 404);

        if ((float) $booking->deposit_amount <= 0) {
            return redirect()->route('customer.bookings.review', $booking);
        }

        if ($booking->deposit_paid_at) {
            if ($booking->status === Booking::STATUS_PENDING) {
                return redirect()->route('customer.bookings.show', $booking);
            }

            return redirect()->route('customer.bookings.review', $booking);
        }

        $booking->load(['roomType', 'room']);

        return view('customer.booking.payment', compact('booking'));
    }

    public function paymentGateway(Request $request, Booking $booking, string $gateway): View
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === Booking::STATUS_DRAFT, 404);
        abort_unless(in_array($gateway, ['vnpay', 'momo'], true), 404);
        abort_if((float) $booking->deposit_amount <= 0, 400);
        abort_if($booking->deposit_paid_at !== null, 400);

        $booking->load(['roomType', 'room']);

        return view('customer.booking.payment-gateway', compact('booking', 'gateway'));
    }

    public function paymentComplete(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);
        abort_unless($booking->status === Booking::STATUS_DRAFT, 400);

        $data = $request->validate([
            'gateway' => ['required', 'in:vnpay,momo'],
        ]);

        abort_if((float) $booking->deposit_amount <= 0, 400);

        if ($booking->deposit_paid_at) {
            return redirect()->route('customer.bookings.review', $booking);
        }

        $booking->update([
            'deposit_paid_at' => now(),
            'payment_method' => $data['gateway'],
        ]);

        $result = $this->submitBooking->submitDraftBooking($booking->fresh());

        if (! $result['ok']) {
            return redirect()
                ->route('customer.bookings.review', $booking)
                ->with('error', $result['error']);
        }

        return redirect()
            ->route('customer.bookings.show', $booking)
            ->with('status', __('Thanh toán thành công. Đơn đã được gửi — chờ lễ tân xác nhận.'));
    }

    public function index(Request $request): View
    {
        $bookings = Booking::query()
            ->where('user_id', $request->user()->id)
            ->with(['roomType', 'room'])
            ->orderByDesc('created_at')
            ->paginate(12);

        return view('customer.bookings.index', compact('bookings'));
    }

    public function continueAfterVnpay(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless(
            in_array($booking->status, [Booking::STATUS_DRAFT, Booking::STATUS_PENDING], true),
            404
        );

        $statusFlash = session('status');
        $errorFlash = session('error');

        if ($booking->status === Booking::STATUS_PENDING) {
            if ($request->user() && (int) $request->user()->id === (int) $booking->user_id) {
                $redirect = redirect()->route('customer.bookings.show', $booking);
                if ($statusFlash) {
                    $redirect->with('status', $statusFlash);
                }
                if ($errorFlash) {
                    $redirect->with('error', $errorFlash);
                }

                return $redirect;
            }

            session(['url.intended' => route('customer.bookings.show', $booking)]);

            $toLogin = redirect()->route('login');
            if ($statusFlash) {
                $toLogin->with('status', $statusFlash);
            }
            if ($errorFlash) {
                $toLogin->with('error', $errorFlash);
            }

            return $toLogin;
        }

        if ($booking->depositOutstanding()) {
            if ($request->user() && (int) $request->user()->id === (int) $booking->user_id) {
                return redirect()
                    ->route('customer.bookings.payment', $booking)
                    ->with('error', __('Chưa ghi nhận thanh toán cọc. Vui lòng thanh toán hoặc liên hệ hỗ trợ.'));
            }

            abort(403);
        }

        if ($request->user()) {
            abort_unless((int) $request->user()->id === (int) $booking->user_id, 403);

            $redirect = redirect()->route('customer.bookings.review', $booking);
            if ($statusFlash) {
                $redirect->with('status', $statusFlash);
            }
            if ($errorFlash) {
                $redirect->with('error', $errorFlash);
            }

            return $redirect;
        }

        $booking->load(['roomType', 'room']);
        session(['url.intended' => route('customer.bookings.review', $booking)]);

        return view('customer.booking.review', [
            'booking' => $booking,
            'loginRequiredToConfirm' => true,
            'siteSetting' => SiteSetting::instance(),
        ]);
    }

    public function show(Request $request, Booking $booking): View|RedirectResponse
    {
        abort_unless($booking->user_id === $request->user()->id, 403);

        if ($booking->status === Booking::STATUS_DRAFT) {
            if ($booking->depositOutstanding()) {
                return redirect()->route('customer.bookings.payment', $booking);
            }

            return redirect()->route('customer.bookings.review', $booking);
        }

        $booking->load(['roomType', 'room', 'user', 'bookingServices.service', 'invoice']);

        return view('customer.bookings.show', compact('booking'));
    }
}
