<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\RoomType\StoreRoomTypeRequest;
use App\Models\RoomType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoomTypeController extends Controller
{
    public function index(): JsonResponse
    {
        $types = RoomType::withCount('rooms')->orderBy('name')->get();

        return response()->json($types);
    }

    public function store(StoreRoomTypeRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = Str::slug($data['name']);

        $type = RoomType::create($data);

        return response()->json($type, 201);
    }

    public function show(RoomType $roomType): JsonResponse
    {
        return response()->json($roomType->load('rooms'));
    }

    public function update(StoreRoomTypeRequest $request, RoomType $roomType): JsonResponse
    {
        $roomType->update($request->validated());

        return response()->json($roomType->fresh());
    }

    public function destroy(RoomType $roomType): JsonResponse
    {
        if ($roomType->rooms()->exists()) {
            return response()->json(['message' => 'Không thể xóa loại phòng đang có phòng.'], 422);
        }

        $roomType->delete();

        return response()->json(null, 204);
    }
}
