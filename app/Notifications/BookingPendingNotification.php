<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingPendingNotification extends Notification
{
    use Queueable;

    public function __construct(public Booking $booking) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $booking = $this->booking;
        $guestName = $booking->user?->name ?? 'Khách hàng';
        $roomInfo = $booking->room?->code ?? ($booking->roomType?->name ?? '—');

        return [
            'title' => 'Đơn đặt phòng mới',
            'body' => "{$guestName} vừa gửi đơn đặt phòng {$roomInfo} — chờ xác nhận.",
            'url' => route('reception.reservations.show', $booking->id),
            'booking_id' => $booking->id,
            'type' => 'booking_pending',
            'icon' => 'calendar',
        ];
    }
}
