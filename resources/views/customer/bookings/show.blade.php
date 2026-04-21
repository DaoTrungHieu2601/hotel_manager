<x-hotel-layout>
    <x-slot name="header">Chi tiết đơn #{{ $booking->id }}</x-slot>

    @php
        $statusClass = [
            \App\Models\Booking::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
            \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-100 text-sky-800 ring-sky-200',
            \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-100 text-slate-700 ring-slate-200',
            \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
        $rate = (float) ($booking->rate_per_night ?? $booking->roomType->default_price);
        $nights = $booking->nights();
        $roomSubtotal = max(0, $nights * $rate);
    @endphp

    <div class="max-w-3xl space-y-6">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-slate-900">{{ __('Thông tin khách hàng') }}</h2>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('Họ và tên') }}</dt><dd class="mt-1 font-medium text-slate-800">{{ $booking->user->name }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Email') }}</dt><dd class="mt-1 text-slate-800">{{ $booking->user->email }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Số điện thoại') }}</dt><dd class="mt-1 text-slate-800">{{ $booking->user->phone ?: '—' }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('CCCD') }}</dt><dd class="mt-1 text-slate-800">{{ $booking->user->cccd ?: '—' }}</dd></div>
            </dl>
        </div>

        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-base font-semibold text-slate-900">{{ __('Thông tin đặt phòng') }}</h2>
            <dl class="grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">{{ __('Trạng thái') }}</dt><dd class="mt-1"><span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass[$booking->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ \App\Models\Booking::statusLabels()[$booking->status] ?? $booking->status }}</span></dd></div>
                <div><dt class="text-slate-500">{{ __('Mã đơn') }}</dt><dd class="mt-1 font-medium tabular-nums text-slate-800">#{{ $booking->id }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Loại phòng') }}</dt><dd class="mt-1 text-slate-800">{{ $booking->roomType->name }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Phòng gán') }}</dt><dd class="mt-1 font-medium text-slate-800">{{ $booking->room?->code ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Check-in') }}</dt><dd class="mt-1 text-slate-700">{{ $booking->check_in->format('d/m/Y') }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Check-out') }}</dt><dd class="mt-1 text-slate-700">{{ $booking->check_out->format('d/m/Y') }}</dd></div>
                @if ($booking->guest_planned_check_in || $booking->guest_planned_check_out)
                    <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('Giờ dự kiến (khách)') }}</dt><dd class="mt-1 text-slate-700 tabular-nums">{{ $booking->guest_planned_check_in ?? '—' }} / {{ $booking->guest_planned_check_out ?? '—' }}</dd></div>
                @endif
                <div><dt class="text-slate-500">{{ __('Số khách') }}</dt><dd class="mt-1 tabular-nums text-slate-800">{{ $booking->guests }}</dd></div>
                <div><dt class="text-slate-500">{{ __('Đơn giá / đêm') }}</dt><dd class="mt-1 tabular-nums text-slate-800">{{ number_format($rate, 0, ',', '.') }} ₫</dd></div>
                <div><dt class="text-slate-500">{{ __('Số đêm') }}</dt><dd class="mt-1 tabular-nums text-slate-800">{{ $nights }}</dd></div>
                <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('Tạm tính tiền phòng') }}</dt><dd class="mt-1 text-base font-semibold tabular-nums text-amber-950">{{ number_format($roomSubtotal, 0, ',', '.') }} ₫</dd></div>
                <div><dt class="text-slate-500">{{ __('Tiền cọc') }}</dt><dd class="mt-1 font-medium tabular-nums text-slate-800">{{ number_format((float) $booking->deposit_amount, 0, ',', '.') }} ₫</dd></div>
                <div><dt class="text-slate-500">{{ __('Thanh toán cọc') }}</dt><dd class="mt-1 text-slate-800">
                    @if ($booking->deposit_paid_at)
                        <span class="text-emerald-700">{{ __('Đã thanh toán') }}</span>
                        @if ($booking->payment_method)
                            <span class="text-slate-500">({{ strtoupper($booking->payment_method) }})</span>
                        @endif
                        <span class="block text-xs text-slate-500">{{ $booking->deposit_paid_at->format('d/m/Y H:i') }}</span>
                    @else
                        <span class="text-amber-800">{{ __('Chưa ghi nhận') }}</span>
                    @endif
                </dd></div>
                @if ($booking->guest_notes)
                    <div class="sm:col-span-2"><dt class="text-slate-500">{{ __('Ghi chú') }}</dt><dd class="mt-1 whitespace-pre-wrap text-slate-800">{{ $booking->guest_notes }}</dd></div>
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
