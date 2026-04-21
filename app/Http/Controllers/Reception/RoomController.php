<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomController extends Controller
{
    public function updateStatus(Request $request, Room $room): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', [
                Room::STATUS_AVAILABLE,
                Room::STATUS_CLEANING,
                Room::STATUS_MAINTENANCE,
            ])],
        ]);

        if (in_array($room->status, [Room::STATUS_OCCUPIED, Room::STATUS_BOOKED], true)) {
            return redirect()->back()->with('error', __('Không đổi trạng thái phòng đang có khách/đã đặt từ đơn.'));
        }

        $room->update(['status' => $data['status']]);

        return redirect()->route('reception.dashboard')->with('status', __('Đã cập nhật trạng thái phòng.'));
    }
}
