<x-guest-hotel-layout :title="__('Đặt phòng :code', ['code' => $room->code])">
    <div class="mx-auto max-w-lg px-4 py-12">
        <div class="rounded-3xl bg-white p-8 font-sans text-slate-900 shadow-xl ring-1 ring-stone-200">
            <h1 class="font-display text-2xl font-bold text-amber-950">{{ __('Đặt phòng :code', ['code' => $room->code]) }}</h1>
            <p class="mt-2 text-sm font-medium text-slate-600">{{ $room->roomType->name }}</p>
            <dl class="mt-6 space-y-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="shrink-0 text-slate-600">{{ __('Check-in') }}</dt><dd class="text-right font-medium tabular-nums text-slate-900">{{ $check_in->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="shrink-0 text-slate-600">{{ __('Check-out') }}</dt><dd class="text-right font-medium tabular-nums text-slate-900">{{ $check_out->format('d/m/Y') }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="shrink-0 text-slate-600">{{ __('Số khách') }}</dt><dd class="text-right font-medium tabular-nums text-slate-900">{{ $guests }}</dd></div>
                <div class="flex justify-between gap-4 text-lg font-bold text-amber-900"><dt class="shrink-0">{{ __('Giá tham khảo / đêm') }}</dt><dd class="text-right tabular-nums">{{ number_format($room->roomType->default_price, 0, ',', '.') }} ₫</dd></div>
                <div class="flex justify-between gap-4"><dt class="shrink-0 text-slate-600">{{ __('Tổng giá phòng') }} ({{ $stayNights }} {{ __('đêm') }})</dt><dd class="text-right font-semibold tabular-nums text-slate-900">{{ number_format($roomSubtotal, 0, ',', '.') }} ₫</dd></div>
                <div class="flex justify-between gap-4 rounded-xl bg-amber-50 px-3 py-2 text-amber-950 ring-1 ring-amber-100"><dt class="shrink-0 font-semibold">{{ __('Tiền cọc bắt buộc') }} (30%)</dt><dd class="text-right text-lg font-bold tabular-nums">{{ number_format($requiredDeposit, 0, ',', '.') }} ₫</dd></div>
            </dl>
            @if ($errors->any())
                <div class="mt-4 text-sm text-red-600">{{ $errors->first() }}</div>
            @endif
            @if (session('error'))
                <div class="mt-4 text-sm text-red-600">{{ session('error') }}</div>
            @endif
            <form method="post" action="{{ route('customer.bookings.store-room', $room) }}" class="mt-8 space-y-4">
                @csrf
                <input type="hidden" name="check_in" value="{{ $check_in->toDateString() }}" />
                <input type="hidden" name="check_out" value="{{ $check_out->toDateString() }}" />
                <input type="hidden" name="guests" value="{{ $guests }}" />
                @php($s = $siteSetting)
                @include('customer.booking.partials.planned-times')
                <div>
                    <label class="mb-1 block text-sm font-medium text-slate-600" for="guest_notes">{{ __('Ghi chú') }}</label>
                    <textarea id="guest_notes" name="guest_notes" rows="3" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm placeholder:text-slate-400 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500">{{ old('guest_notes') }}</textarea>
                </div>
                <button type="submit" class="w-full rounded-full bg-amber-900 py-3 text-sm font-semibold text-white hover:bg-amber-950">{{ __('Tiếp tục') }}</button>
            </form>
        </div>
    </div>
    @include('customer.booking.partials.auto-checkout-script')
</x-guest-hotel-layout>
