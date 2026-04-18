<div class="rounded-xl border border-amber-100 bg-amber-50/80 p-4 text-xs text-slate-700">
    <p class="font-semibold text-amber-950">{{ __('Chính sách giờ') }}</p>
    <p class="mt-1">{{ __('Check-in khuyến nghị') }} {{ $s->policy_check_in_start }}–{{ $s->policy_check_in_end }}; {{ __('check-out ngày sau') }} {{ $s->policy_check_out_start }}–{{ $s->policy_check_out_end }}.</p>
    <p class="mt-1">{{ __('Đến sớm / trễ hoặc trả phòng muộn có thể phát sinh phụ phí (check-out sớm không tính thêm)') }} {{ number_format((float) $s->extra_hour_price, 0, ',', '.') }} ₫/{{ __('giờ') }} ({{ __('sau') }} {{ $s->check_time_grace_minutes }} {{ __('phút kể từ giờ dự kiến') }}).</p>
</div>
<div class="grid gap-4 sm:grid-cols-2">
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-600" for="guest_planned_check_in">{{ __('Giờ đến dự kiến') }}</label>
        <input id="guest_planned_check_in" type="time" step="60" name="guest_planned_check_in" value="{{ old('guest_planned_check_in', $s->policy_check_in_start) }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" />
        @error('guest_planned_check_in')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="mb-1 block text-sm font-medium text-slate-600" for="guest_planned_check_out">{{ __('Giờ trả dự kiến (ngày trả)') }}</label>
        <input id="guest_planned_check_out" type="time" step="60" name="guest_planned_check_out" value="{{ old('guest_planned_check_out', $s->policy_check_out_start) }}" class="mt-1 w-full rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm text-slate-900 shadow-sm focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500" />
        @error('guest_planned_check_out')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
