<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Room\StoreRoomRequest;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $rooms = Room::with('roomType')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->room_type_id, fn ($q) => $q->where('room_type_id', $request->room_type_id))
            ->orderBy('code')
            ->get();

        return response()->json($rooms);
    }

    public function store(StoreRoomRequest $request): JsonResponse
    {
        $room = Room::create($request->validated());

        return response()->json($room->load('roomType'), 201);
    }

    public function show(Room $room): JsonResponse
    {
        return response()->json($room->load('roomType'));
    }

    public function update(StoreRoomRequest $request, Room $room): JsonResponse
    {
        $room->update($request->validated());

        return response()->json($room->fresh()->load('roomType'));
    }

    public function destroy(Room $room): JsonResponse
    {
        $room->delete();

        return response()->json(null, 204);
    }

    public function updateStatus(Request $request, Room $room): JsonResponse
    {
        $request->validate([
            'status' => ['required', 'in:' . implode(',', array_keys(Room::statusLabels()))],
        ]);

        $room->update(['status' => $request->status]);

        return response()->json($room->fresh()->load('roomType'));
    }
}
