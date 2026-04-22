<?php

namespace App\Http\Controllers\Api\Reception;

use App\Http\Controllers\Controller;
use App\Http\Requests\Reception\CheckInRequest;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with('user', 'roomType', 'room')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when(! $request->status, fn ($q) => $q->whereIn('status', [
                Booking::STATUS_PENDING,
                Booking::STATUS_CONFIRMED,
                Booking::STATUS_CHECKED_IN,
            ]))
            ->latest()
            ->paginate(20);

        return response()->json($bookings);
    }

    public function show(Booking $booking): JsonResponse
    {
        return response()->json($booking->load('user', 'roomType', 'room', 'services', 'invoice'));
    }

    public function confirm(Request $request, Booking $booking): JsonResponse
    {
        if ($booking->status !== Booking::STATUS_PENDING) {
            return response()->json(['message' => 'Chỉ có thể xác nhận đặt phòng đang chờ.'], 422);
        }

        $booking->update(['status' => Booking::STATUS_CONFIRMED, 'confirmed_at' => now()]);

        return response()->json($booking->fresh()->load('user', 'roomType', 'room'));
    }

    public function cancel(Booking $booking): JsonResponse
    {
        if (! in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return response()->json(['message' => 'Không thể hủy ở trạng thái này.'], 422);
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return response()->json($booking->fresh());
    }

    public function checkIn(CheckInRequest $request, Booking $booking): JsonResponse
    {
        $data = $request->validated();

        $room = Room::findOrFail($data['room_id']);

        if ($room->status !== Room::STATUS_AVAILABLE) {
            return response()->json(['message' => 'Phòng không ở trạng thái sẵn sàng.'], 422);
        }

        $booking->update([
            'status'        => Booking::STATUS_CHECKED_IN,
            'room_id'       => $room->id,
            'checked_in_at' => $data['actual_check_in'] ?? now(),
        ]);

        $room->update(['status' => Room::STATUS_OCCUPIED]);

        if ($data['cccd'] && $booking->user) {
            $booking->user->update(['cccd' => $data['cccd']]);
        }

        return response()->json($booking->fresh()->load('user', 'roomType', 'room'));
    }

    public function checkOut(Booking $booking): JsonResponse
    {
        if ($booking->status !== Booking::STATUS_CHECKED_IN) {
            return response()->json(['message' => 'Đặt phòng chưa check-in.'], 422);
        }

        $booking->update([
            'status'         => Booking::STATUS_CHECKED_OUT,
            'checked_out_at' => now(),
        ]);

        if ($booking->room) {
            $booking->room->update(['status' => Room::STATUS_CLEANING]);
        }

        return response()->json($booking->fresh()->load('user', 'roomType', 'room', 'invoice'));
    }

    public function addService(Request $request, Booking $booking): JsonResponse
    {
        $data = $request->validate([
            'service_id' => ['required', 'exists:services,id'],
            'quantity'   => ['required', 'integer', 'min:1'],
        ]);

        $service = \App\Models\Service::findOrFail($data['service_id']);

        $booking->bookingServices()->create([
            'service_id' => $service->id,
            'quantity'   => $data['quantity'],
            'unit_price' => $service->price,
        ]);

        return response()->json($booking->load('services'), 201);
    }
}
