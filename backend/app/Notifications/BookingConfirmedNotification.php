<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BookingConfirmedNotification extends Notification
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
        $roomCode = $booking->room?->code ?? '—';
        $checkIn = $booking->check_in?->format('d/m/Y') ?? '—';

        return [
            'title' => 'Đơn đặt phòng đã được xác nhận',
            'body' => "Đơn đặt phòng #{$booking->id} ({$roomCode}) của bạn đã được xác nhận. Nhận phòng: {$checkIn}.",
            'url' => route('customer.bookings.show', $booking->id),
            'booking_id' => $booking->id,
            'type' => 'booking_confirmed',
            'icon' => 'check-circle',
        ];
    }
}
