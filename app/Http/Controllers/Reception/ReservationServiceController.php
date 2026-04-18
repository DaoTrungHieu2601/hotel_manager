<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReservationServiceController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        if ($booking->status !== Booking::STATUS_CHECKED_IN) {
            return redirect()->back()->with('error', __('Chỉ thêm dịch vụ khi khách đang lưu trú.'));
        }

        $data = $request->validate([
            'services' => ['array'],
            'services.*' => ['integer', 'exists:services,id'],
            'quantities' => ['array'],
            'quantities.*' => ['nullable', 'integer', 'min:0'],
        ]);

        $selected = collect($data['services'] ?? [])->map(fn ($id) => (int) $id)->unique()->values();
        $qtyMap = collect($data['quantities'] ?? []);

        if ($selected->isEmpty()) {
            DB::transaction(function () use ($booking) {
                BookingService::query()->where('booking_id', $booking->id)->delete();
            });
            return redirect()->route('reception.reservations.show', $booking)->with('status', __('Đã cập nhật dịch vụ (trống).'));
        }

        $services = Service::query()
            ->whereIn('id', $selected)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        DB::transaction(function () use ($booking, $selected, $services, $qtyMap) {
            BookingService::query()->where('booking_id', $booking->id)->delete();

            foreach ($selected as $serviceId) {
                if (! isset($services[$serviceId])) {
                    continue;
                }

                $qty = (int) ($qtyMap[$serviceId] ?? 1);
                if ($qty < 1) {
                    continue;
                }

                $service = $services[$serviceId];
                BookingService::query()->create([
                    'booking_id' => $booking->id,
                    'service_id' => $service->id,
                    'quantity' => $qty,
                    'unit_price' => $service->price,
                ]);
            }
        });

        return redirect()->route('reception.reservations.show', $booking)->with('status', __('Đã cập nhật dịch vụ sử dụng.'));
    }
}
