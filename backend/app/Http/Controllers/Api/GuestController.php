<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GuestController extends Controller
{
    public function home(): JsonResponse
    {
        $settings  = SiteSetting::instance();
        $roomTypes = RoomType::withCount('rooms')->orderBy('default_price')->get();

        return response()->json([
            'settings'   => $settings,
            'room_types' => $roomTypes,
        ]);
    }

    public function searchRooms(Request $request): JsonResponse
    {
        $data = $request->validate([
            'check_in'  => ['required', 'date', 'after_or_equal:today'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'guests'    => ['required', 'integer', 'min:1'],
        ]);

        $roomTypes = RoomType::where('max_occupancy', '>=', $data['guests'])
            ->with(['rooms' => fn ($q) => $q->where('status', Room::STATUS_AVAILABLE)])
            ->get()
            ->filter(fn ($type) => $type->rooms->isNotEmpty())
            ->values();

        return response()->json($roomTypes);
    }

    public function roomTypes(): JsonResponse
    {
        return response()->json(RoomType::orderBy('default_price')->get());
    }

    public function roomType(RoomType $roomType): JsonResponse
    {
        return response()->json($roomType->load('rooms'));
    }
}
