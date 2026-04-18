<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Services\RoomAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RoomSearchController extends Controller
{
    public function __construct(
        private RoomAvailabilityService $availability
    ) {}

    public function index(Request $request): View|RedirectResponse
    {
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $guests = (int) $request->query('guests', 2);

        if (! $checkIn || ! $checkOut) {
            $defaultIn = now()->startOfDay();
            $defaultOut = now()->addDay()->startOfDay();

            return view('guest.search', [
                'results' => collect(),
                'check_in' => $defaultIn,
                'check_out' => $defaultOut,
                'guests' => max(1, $guests),
                'searchExecuted' => false,
            ]);
        }

        try {
            $from = Carbon::parse($checkIn)->startOfDay();
            $to = Carbon::parse($checkOut)->startOfDay();
        } catch (\Throwable) {
            return redirect()->route('guest.search-rooms')->with('error', __('Ngày không hợp lệ.'));
        }

        if ($to->lte($from)) {
            return redirect()->route('guest.search-rooms')->with('error', __('Ngày trả phòng phải sau ngày nhận phòng.'));
        }

        $results = $this->availability->searchRoomTypes($from, $to, max(1, $guests))
            ->map(function (array $row) use ($from, $to) {
                $row['rooms'] = $this->availability->freeRoomsForType($row['type']->id, $from, $to);

                return $row;
            });

        return view('guest.search', [
            'results' => $results,
            'check_in' => $from,
            'check_out' => $to,
            'guests' => max(1, $guests),
            'searchExecuted' => true,
        ]);
    }
}
