<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Room;
use App\Models\SiteSetting;
use App\Notifications\BookingCancelledNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ReleaseNoShowBookings extends Command
{
    protected $signature = 'bookings:release-no-shows';

    protected $description = 'Release rooms for confirmed bookings past check-in cutoff without arrival.';

    public function handle(): int
    {
        $setting = SiteSetting::instance();
        $cutoff = (string) ($setting->no_show_cutoff_time ?? '23:30');
        $tz = (string) config('app.timezone', 'UTC');

        $candidates = Booking::query()
            ->where('status', Booking::STATUS_CONFIRMED)
            ->whereNotNull('room_id')
            ->whereNull('checked_in_at')
            ->whereDate('check_in', '<=', now($tz)->toDateString())
            ->with(['room', 'user', 'roomType'])
            ->get();

        $released = 0;
        foreach ($candidates as $booking) {
            $deadline = Carbon::parse($booking->check_in->toDateString().' '.$cutoff, $tz);
            if (now($tz)->lt($deadline)) {
                continue;
            }

            DB::transaction(function () use ($booking): void {
                $room = $booking->room;
                if ($room && $room->status === Room::STATUS_BOOKED) {
                    $room->update(['status' => Room::STATUS_AVAILABLE]);
                }

                $booking->update([
                    'status' => Booking::STATUS_CANCELLED,
                    'room_id' => null,
                ]);
            });

            $released++;
            $fresh = $booking->fresh(['user', 'roomType', 'room']);
            if ($fresh->user) {
                $fresh->user->notify(new BookingCancelledNotification($fresh));
            }
        }

        if ($released > 0) {
            $this->info("Released {$released} no-show booking(s).");
        }

        return self::SUCCESS;
    }
}
