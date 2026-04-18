<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class BookingController extends Controller
{
    public function index(): View
    {
        /** @var LengthAwarePaginator<int, Booking> $bookings */
        $bookings = Booking::query()
            ->with(['user', 'roomType', 'room'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('admin.bookings.index', compact('bookings'));
    }
}
