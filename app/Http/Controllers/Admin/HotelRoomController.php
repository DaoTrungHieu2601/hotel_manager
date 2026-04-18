<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HotelRoomController extends Controller
{
    public function index(): View
    {
        $rooms = Room::query()->with('roomType')->orderBy('code')->paginate(20);

        return view('admin.hotel-rooms.index', compact('rooms'));
    }

    public function create(): View
    {
        $types = RoomType::query()->orderBy('name')->get();

        return view('admin.hotel-rooms.create', compact('types'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        Room::query()->create($data);

        return redirect()->route('admin.hotel-rooms.index')->with('status', __('Đã tạo phòng.'));
    }

    public function edit(Room $room): View
    {
        $types = RoomType::query()->orderBy('name')->get();

        return view('admin.hotel-rooms.edit', ['room' => $room, 'types' => $types]);
    }

    public function update(Request $request, Room $room): RedirectResponse
    {
        $data = $this->validated($request);
        $room->update($data);

        return redirect()->route('admin.hotel-rooms.index')->with('status', __('Đã cập nhật.'));
    }

    public function destroy(Room $room): RedirectResponse
    {
        $room->delete();

        return redirect()->route('admin.hotel-rooms.index')->with('status', __('Đã xóa.'));
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        return $request->validate([
            'room_type_id' => ['required', 'exists:room_types,id'],
            'code' => ['required', 'string', 'max:20'],
            'floor' => ['nullable', 'string', 'max:20'],
            'status' => ['required', 'string', 'in:'.implode(',', [
                Room::STATUS_AVAILABLE,
                Room::STATUS_OCCUPIED,
                Room::STATUS_BOOKED,
                Room::STATUS_CLEANING,
                Room::STATUS_MAINTENANCE,
            ])],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
