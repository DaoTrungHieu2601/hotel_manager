<x-hotel-layout>
    <x-slot name="header">Chi tiet don #{{ $booking->id }}</x-slot>

    @php
        $statusClass = [
            \App\Models\Booking::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
            \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-100 text-sky-800 ring-sky-200',
            \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-100 text-slate-700 ring-slate-200',
            \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Trang thai</dt><dd class="mt-1"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass[$booking->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ \App\Models\Booking::statusLabels()[$booking->status] ?? $booking->status }}</span></dd></div>
                <div><dt class="text-slate-500">Phong gan</dt><dd class="mt-1 font-medium text-slate-800">{{ $booking->room?->code ?? '?' }}</dd></div>
                <div><dt class="text-slate-500">Check-in</dt><dd class="mt-1 text-slate-700">{{ $booking->check_in->format('d/m/Y') }}</dd></div>
                <div><dt class="text-slate-500">Check-out</dt><dd class="mt-1 text-slate-700">{{ $booking->check_out->format('d/m/Y') }}</dd></div>
                @if ($booking->guest_planned_check_in || $booking->guest_planned_check_out)
                    <div class="sm:col-span-2"><dt class="text-slate-500">Gio du kien (khach)</dt><dd class="mt-1 text-slate-700 tabular-nums">{{ $booking->guest_planned_check_in ?? '—' }} / {{ $booking->guest_planned_check_out ?? '—' }}</dd></div>
                @endif
            </dl>
        </div>

        @if($booking->invoice)
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('invoices.show', $booking->invoice) }}" class="inline-flex rounded-full border border-violet-200 bg-violet-50 px-6 py-2.5 text-sm font-semibold text-violet-700 transition hover:bg-violet-100">
                    Xem hoa don
                </a>
                <a href="{{ route('invoices.pdf', $booking->invoice) }}" class="inline-flex rounded-full bg-gradient-to-r from-violet-600 to-fuchsia-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:from-violet-700 hover:to-fuchsia-700">
                    In hoa don
                </a>
            </div>
        @endif
    </div>
</x-hotel-layout>
