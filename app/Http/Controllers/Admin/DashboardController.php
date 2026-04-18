<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $now = now();
        $roomCount = Room::query()->count() ?: 1;
        $daysInMonth = $now->daysInMonth;

        $occupiedNightsThisMonth = Booking::query()
            ->whereNotIn('status', [Booking::STATUS_CANCELLED, Booking::STATUS_DRAFT])
            ->where(function ($q) {
                $q->whereBetween('check_in', [now()->startOfMonth(), now()->endOfMonth()])
                    ->orWhereBetween('check_out', [now()->startOfMonth(), now()->endOfMonth()])
                    ->orWhere(function ($q2) {
                        $q2->whereDate('check_in', '<=', now()->startOfMonth())
                            ->whereDate('check_out', '>=', now()->endOfMonth());
                    });
            })
            ->get()
            ->sum(fn (Booking $r) => $r->nights());

        $maxNights = $roomCount * $daysInMonth;
        $occupancyPct = $maxNights > 0 ? round(min(100, ($occupiedNightsThisMonth / $maxNights) * 100), 1) : 0;

        $from = $now->copy()->subMonths(5)->startOfMonth();
        $revenueByMonth = Invoice::query()
            ->where('issued_at', '>=', $from)
            ->get()
            ->groupBy(fn (Invoice $i) => $i->issued_at->format('Y-m'))
            ->map(fn ($group) => (float) $group->sum('total'))
            ->sortKeys();

        $labels = [];
        $revenue = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = $now->copy()->subMonths($i)->format('Y-m');
            $labels[] = $m;
            $revenue[] = $revenueByMonth[$m] ?? 0;
        }

        $revenueThisYear = (float) Invoice::query()
            ->whereBetween('issued_at', [$now->copy()->startOfYear(), $now->copy()->endOfDay()])
            ->sum('total');

        $revenueThisWeek = (float) Invoice::query()
            ->whereBetween('issued_at', [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()])
            ->sum('total');

        $invoices = Invoice::query()
            ->with(['booking.user'])
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->get();

        $bookingStatusOrder = [
            Booking::STATUS_DRAFT,
            Booking::STATUS_PENDING,
            Booking::STATUS_CONFIRMED,
            Booking::STATUS_CHECKED_IN,
            Booking::STATUS_CHECKED_OUT,
            Booking::STATUS_CANCELLED,
        ];

        $countsByStatus = Booking::query()
            ->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status');

        $bookingStatusRows = collect($bookingStatusOrder)->map(function (string $status) use ($countsByStatus) {
            return [
                'key' => $status,
                'label' => Booking::statusLabels()[$status] ?? $status,
                'count' => (int) ($countsByStatus[$status] ?? 0),
            ];
        });

        $bookingsTotal = (int) Booking::query()->count();

        return view('admin.dashboard', compact(
            'occupancyPct',
            'labels',
            'revenue',
            'revenueThisYear',
            'revenueThisWeek',
            'invoices',
            'bookingStatusRows',
            'bookingsTotal'
        ));
    }
}
