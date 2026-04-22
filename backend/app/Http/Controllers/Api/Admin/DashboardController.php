<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $today = now()->toDateString();

        $totalRooms     = Room::count();
        $occupiedRooms  = Room::where('status', Room::STATUS_OCCUPIED)->count();
        $occupancyRate  = $totalRooms > 0 ? round($occupiedRooms / $totalRooms * 100, 1) : 0;

        $revenueThisMonth = Booking::where('status', Booking::STATUS_CHECKED_OUT)
            ->whereMonth('checked_out_at', now()->month)
            ->whereYear('checked_out_at', now()->year)
            ->sum('deposit_amount');

        return response()->json([
            'occupancy_rate'      => $occupancyRate,
            'total_rooms'         => $totalRooms,
            'occupied_rooms'      => $occupiedRooms,
            'pending_bookings'    => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'today_checkins'      => Booking::where('status', Booking::STATUS_CONFIRMED)->whereDate('check_in', $today)->count(),
            'today_checkouts'     => Booking::where('status', Booking::STATUS_CHECKED_IN)->whereDate('check_out', $today)->count(),
            'revenue_this_month'  => $revenueThisMonth,
            'total_customers'     => User::where('role', User::ROLE_CUSTOMER)->count(),
        ]);
    }
}
