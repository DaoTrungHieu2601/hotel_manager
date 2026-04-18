<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $rooms = Room::query()->with('roomType')->orderBy('floor')->orderBy('code')->get();

        $activeByRoom = Booking::query()
            ->whereIn('room_id', $rooms->pluck('id'))
            ->whereIn('status', [Booking::STATUS_CHECKED_IN, Booking::STATUS_CONFIRMED])
            ->get()
            ->keyBy('room_id');

        $pendingCount = Booking::query()->where('status', Booking::STATUS_PENDING)->count();
        $todayIn = Booking::query()
            ->whereDate('check_in', today())
            ->whereIn('status', [Booking::STATUS_CONFIRMED, Booking::STATUS_PENDING])
            ->count();
        $todayOut = Booking::query()
            ->whereDate('check_out', today())
            ->where('status', Booking::STATUS_CHECKED_IN)
            ->count();

        $roomsByFloor = $rooms->groupBy('floor');
        $floors       = $roomsByFloor->keys()->sort()->values();

        return view('reception.dashboard', compact('rooms', 'roomsByFloor', 'floors', 'activeByRoom', 'pendingCount', 'todayIn', 'todayOut'));
    }
}
