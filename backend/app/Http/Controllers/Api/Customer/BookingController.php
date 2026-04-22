<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\Booking\CreateBookingRequest;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = $request->user()
            ->bookings()
            ->with('roomType', 'room', 'invoice')
            ->latest()
            ->paginate(10);

        return response()->json($bookings);
    }

    public function store(CreateBookingRequest $request): JsonResponse
    {
        $data = $request->validated();

        $roomType = RoomType::findOrFail($data['room_type_id']);

        if ($data['guests'] > $roomType->max_occupancy) {
            return response()->json(['message' => 'Số khách vượt quá sức chứa của loại phòng.'], 422);
        }

        $booking = $request->user()->bookings()->create([
            ...$data,
            'rate_per_night' => $roomType->default_price,
            'status'         => Booking::STATUS_PENDING,
        ]);

        return response()->json($booking->load('roomType'), 201);
    }

    public function show(Request $request, Booking $booking): JsonResponse
    {
        $this->authorizeBooking($request, $booking);

        return response()->json($booking->load('roomType', 'room', 'services', 'invoice'));
    }

    public function confirm(Request $request, Booking $booking): JsonResponse
    {
        $this->authorizeBooking($request, $booking);

        if ($booking->status !== Booking::STATUS_PENDING) {
            return response()->json(['message' => 'Đặt phòng không ở trạng thái chờ xác nhận.'], 422);
        }

        $booking->update(['status' => Booking::STATUS_CONFIRMED, 'confirmed_at' => now()]);

        return response()->json($booking->fresh()->load('roomType'));
    }

    public function cancel(Request $request, Booking $booking): JsonResponse
    {
        $this->authorizeBooking($request, $booking);

        if (! in_array($booking->status, [Booking::STATUS_PENDING, Booking::STATUS_CONFIRMED])) {
            return response()->json(['message' => 'Không thể hủy đặt phòng ở trạng thái này.'], 422);
        }

        $booking->update(['status' => Booking::STATUS_CANCELLED]);

        return response()->json($booking->fresh());
    }

    private function authorizeBooking(Request $request, Booking $booking): void
    {
        if ($booking->user_id !== $request->user()->id && ! $request->user()->canAccessAdmin()) {
            abort(403);
        }
    }
}
