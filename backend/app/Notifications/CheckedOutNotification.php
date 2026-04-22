<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CheckedOutNotification extends Notification
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
        $roomCode = $booking->room?->code ?? '—';

        return [
            'title' => 'Khách đã trả phòng',
            'body' => "{$guestName} đã check-out phòng {$roomCode}. Hóa đơn đã được tạo.",
            'url' => route('reception.reservations.show', $booking->id),
            'booking_id' => $booking->id,
            'type' => 'checked_out',
            'icon' => 'logout',
        ];
    }
}
