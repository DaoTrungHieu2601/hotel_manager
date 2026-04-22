<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\SiteSetting;
use Carbon\Carbon;

class CheckoutTotalsService
{
    /**
     * @return array{0: string, 1: int} [subtotal, billable hours]
     */
    public function previewCheckInEarlyFee(Booking $booking, ?Carbon $arrivalAt = null): array
    {
        $setting = SiteSetting::instance();
        $hourPrice = max(0, (float) ($setting->extra_hour_price ?? 0));
        if ($hourPrice <= 0) {
            return ['0.00', 0];
        }

        $tz = (string) config('app.timezone', 'UTC');
        $arrival = ($arrivalAt ?? now())->copy()->timezone($tz);
        $checkInDate = Carbon::parse($booking->check_in->toDateString(), $tz)->startOfDay();
        $referenceIn = $booking->guest_planned_check_in
            ? $checkInDate->copy()->setTimeFromTimeString($booking->guest_planned_check_in)
            : $checkInDate->copy()->setTimeFromTimeString($setting->policy_check_in_start ?? '08:00');

        $earlySeconds = max(0, $referenceIn->timestamp - $arrival->timestamp);
        $hoursEarly = (int) ceil($earlySeconds / 3600);
        $subtotal = bcmul((string) $hourPrice, (string) $hoursEarly, 2);

        return [$subtotal, $hoursEarly];
    }

    /**
     * @return array{nights:int, roomSubtotal:string, servicesSubtotal:string, earlyLateSubtotal:string, deposit:string, total:string, earlyLateHours:int}
     */
    public function build(Booking $booking, ?Carbon $checkoutAt = null): array
    {
        $booking->loadMissing(['roomType', 'bookingServices']);

        $nights = $booking->nights();
        $rate = (string) ($booking->rate_per_night ?? $booking->roomType->default_price);
        $roomSubtotal = bcmul($rate, (string) $nights, 2);

        $servicesSubtotal = '0.00';
        foreach ($booking->bookingServices as $line) {
            $servicesSubtotal = bcadd($servicesSubtotal, $line->lineTotal(), 2);
        }

        [$earlyLate, $earlyLateHours] = $this->computeEarlyLate($booking, $checkoutAt);

        $deposit = (string) $booking->deposit_amount;
        $beforeDeposit = bcadd(bcadd($roomSubtotal, $servicesSubtotal, 2), $earlyLate, 2);
        $total = bcsub($beforeDeposit, $deposit, 2);
        if (bccomp($total, '0', 2) < 0) {
            $total = '0.00';
        }

        return [
            'nights' => $nights,
            'roomSubtotal' => $roomSubtotal,
            'servicesSubtotal' => $servicesSubtotal,
            'earlyLateSubtotal' => $earlyLate,
            'deposit' => $deposit,
            'total' => $total,
            'earlyLateHours' => $earlyLateHours,
        ];
    }

    /**
     * @return array{0: string, 1: int} [subtotal, billable hours]
     */
    public function computeEarlyLate(Booking $booking, ?Carbon $checkoutAt = null): array
    {
        if (! $booking->checked_in_at) {
            return ['0.00', 0];
        }

        $setting = SiteSetting::instance();
        $hourPrice = max(0, (float) ($setting->extra_hour_price ?? 0));
        if ($hourPrice <= 0) {
            return ['0.00', 0];
        }

        $tz = (string) config('app.timezone', 'UTC');
        $in = $booking->checked_in_at->copy()->timezone($tz);
        $out = ($checkoutAt ?? $booking->checked_out_at ?? now())->copy()->timezone($tz);

        $checkInDate = Carbon::parse($booking->check_in->toDateString(), $tz)->startOfDay();
        $checkOutDate = Carbon::parse($booking->check_out->toDateString(), $tz)->startOfDay();

        $policyInEnd = $checkInDate->copy()->setTimeFromTimeString($setting->policy_check_in_end ?? '08:30');
        $policyOutEnd = $checkOutDate->copy()->setTimeFromTimeString($setting->policy_check_out_end ?? '11:00');

        $plannedIn = $booking->guest_planned_check_in
            ? $checkInDate->copy()->setTimeFromTimeString($booking->guest_planned_check_in)
            : $policyInEnd;

        $plannedOut = $booking->guest_planned_check_out
            ? $checkOutDate->copy()->setTimeFromTimeString($booking->guest_planned_check_out)
            : $policyOutEnd;

        $grace = max(0, (int) ($setting->check_time_grace_minutes ?? 15));
        $deadlineIn = $plannedIn->copy()->addMinutes($grace);
        $deadlineOut = $plannedOut->copy()->addMinutes($grace);

        $earlySeconds = max(0, $plannedIn->timestamp - $in->timestamp);
        $hoursEarly = (int) ceil($earlySeconds / 3600);

        $lateInSeconds = max(0, $in->timestamp - $deadlineIn->timestamp);
        $hoursLateIn = (int) ceil($lateInSeconds / 3600);

        $lateOutSeconds = max(0, $out->timestamp - $deadlineOut->timestamp);
        $hoursLateOut = (int) ceil($lateOutSeconds / 3600);

        $totalHours = $hoursEarly + $hoursLateIn + $hoursLateOut;
        $subtotal = bcmul((string) $hourPrice, (string) $totalHours, 2);

        return [$subtotal, $totalHours];
    }
}
