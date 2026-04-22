<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\BookingPendingNotification;
use Illuminate\Support\Facades\Log;

class CustomerBookingSubmitService
{
    public function __construct(
        private RoomAvailabilityService $availability
    ) {}

    public function submitDraftBooking(Booking $booking): array
    {
        if ($booking->status !== Booking::STATUS_DRAFT) {
            return ['ok' => true, 'error' => null];
        }

        if ($booking->depositOutstanding()) {
            return [
                'ok' => false,
                'error' => __('Vui lòng hoàn tất thanh toán cọc trước khi gửi đơn.'),
            ];
        }

        $from = $booking->check_in->copy()->startOfDay();
        $to = $booking->check_out->copy()->startOfDay();

        if ($booking->room_id) {
            if (! $this->availability->roomIsFree($booking->room_id, $from, $to, $booking->id)) {
                return [
                    'ok' => false,
                    'error' => __('Phòng không còn trống. Vui lòng chọn ngày hoặc phòng khác.'),
                ];
            }
        } elseif ($this->availability->countAvailableRoomsForType($booking->room_type_id, $from, $to) < 1) {
            return [
                'ok' => false,
                'error' => __('Loại phòng đã hết chỗ trong khoảng thời gian này.'),
            ];
        }

        $booking->update(['status' => Booking::STATUS_PENDING]);

        $staff = User::query()
            ->whereHas('systemRole', fn ($q) => $q->where('notify_pending_customer_booking', true))
            ->get();
        $booking->load(['user', 'room', 'roomType']);
        foreach ($staff as $member) {
            try {
                $member->notify(new BookingPendingNotification($booking));
            } catch (\Throwable $e) {
                Log::warning('BookingPendingNotification failed', [
                    'user_id' => $member->id,
                    'booking_id' => $booking->id,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return ['ok' => true, 'error' => null];
    }
}
