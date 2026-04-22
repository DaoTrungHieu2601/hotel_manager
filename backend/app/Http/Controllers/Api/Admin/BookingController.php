<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $bookings = Booking::with('user', 'roomType', 'room')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->when($request->search, fn ($q) => $q->whereHas('user', fn ($u) => $u->where('name', 'like', "%{$request->search}%")->orWhere('email', 'like', "%{$request->search}%")))
            ->latest()
            ->paginate(20);

        return response()->json($bookings);
    }

    public function stats(): JsonResponse
    {
        $today = now()->toDateString();

        return response()->json([
            'pending'     => Booking::where('status', Booking::STATUS_PENDING)->count(),
            'confirmed'   => Booking::where('status', Booking::STATUS_CONFIRMED)->count(),
            'checked_in'  => Booking::where('status', Booking::STATUS_CHECKED_IN)->count(),
            'today_checkins'  => Booking::where('status', Booking::STATUS_CONFIRMED)->whereDate('check_in', $today)->count(),
            'today_checkouts' => Booking::where('status', Booking::STATUS_CHECKED_IN)->whereDate('check_out', $today)->count(),
        ]);
    }
}
