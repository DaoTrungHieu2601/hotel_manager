<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingConfirmedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking)
    {
        $this->booking->load(['roomType', 'room', 'user']);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Xác nhận đặt phòng #:id — :hotel', [
                'id' => $this->booking->id,
                'hotel' => config('app.name'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.bookings.confirmed',
        );
    }
}
