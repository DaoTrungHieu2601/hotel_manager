<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingCancelledNotification extends Notification
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
        $roomInfo = $booking->room?->code ?? ($booking->roomType?->name ?? '—');
        $checkIn = $booking->check_in?->format('d/m/Y') ?? '—';

        return [
            'title' => 'Đơn đặt phòng đã bị hủy',
            'body' => "Đơn đặt phòng #{$booking->id} ({$roomInfo}, {$checkIn}) đã bị hủy.",
            'url' => route('customer.bookings.show', $booking->id),
            'booking_id' => $booking->id,
            'type' => 'booking_cancelled',
            'icon' => 'x-circle',
        ];
    }
}
