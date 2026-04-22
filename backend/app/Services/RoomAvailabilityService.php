<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomType;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class RoomAvailabilityService
{
    /**
     * @return Collection<int, Room>
     */
    public function freeRoomsForType(int $roomTypeId, CarbonInterface $checkIn, CarbonInterface $checkOut): Collection
    {
        $rooms = Room::query()
            ->where('room_type_id', $roomTypeId)
            ->where('status', '!=', Room::STATUS_MAINTENANCE)
            ->get();

        return $rooms->filter(fn (Room $room) => $this->roomIsFree($room->id, $checkIn, $checkOut));
    }

    public function roomIsFree(int $roomId, CarbonInterface $checkIn, CarbonInterface $checkOut, ?int $ignoreBookingId = null): bool
    {
        return ! Booking::query()
            ->where('room_id', $roomId)
            ->when($ignoreBookingId, fn ($q) => $q->where('id', '!=', $ignoreBookingId))
            ->whereNotIn('status', [
                Booking::STATUS_CANCELLED,
                Booking::STATUS_CHECKED_OUT,
                Booking::STATUS_DRAFT,
            ])
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn)
            ->exists();
    }

    /**
     * Phòng còn trống theo loại: status available/booked không overlap logic — overlap chỉ xét booking.
     */
    public function countAvailableRoomsForType(int $roomTypeId, CarbonInterface $checkIn, CarbonInterface $checkOut): int
    {
        return $this->freeRoomsForType($roomTypeId, $checkIn, $checkOut)->count();
    }

    /**
     * @return Collection<int, array{type: RoomType, available: int}>
     */
    public function searchRoomTypes(CarbonInterface $checkIn, CarbonInterface $checkOut, int $guests): Collection
    {
        return RoomType::query()
            ->orderBy('name')
            ->get()
            ->filter(fn (RoomType $t) => $t->max_occupancy >= $guests)
            ->map(fn (RoomType $t) => [
                'type' => $t,
                'available' => $this->countAvailableRoomsForType($t->id, $checkIn, $checkOut),
            ])
            ->values();
    }
}
