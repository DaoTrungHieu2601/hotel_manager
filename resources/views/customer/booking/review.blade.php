<x-guest-hotel-layout :title="__('Xem lại đơn đặt phòng')">
    <div class="mx-auto max-w-lg px-4 py-12">
        <div class="rounded-3xl bg-white p-8 font-sans text-slate-900 shadow-xl ring-1 ring-stone-200">
            <h1 class="font-display text-2xl font-bold text-amber-950">{{ __('Xem lại đơn đặt phòng') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('Kiểm tra thông tin trước khi gửi cho lễ tân.') }}</p>

            @if (session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
            @endif
            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">{{ session('status') }}</div>
            @endif

            @if (! empty($loginRequiredToConfirm) && auth()->guest())
                <div class="mt-4 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-950">
                    {{ __('Sau khi thanh toán tại VNPAY, phiên đăng nhập đôi khi chưa kịp khôi phục. Hãy đăng nhập đúng tài khoản đã đặt phòng để xác nhận gửi đơn.') }}
                    <a href="{{ route('login') }}" class="ml-1 font-semibold underline">{{ __('Đăng nhập') }}</a>
                </div>
            @endif

            <dl class="mt-6 space-y-2 text-sm">
                @if ($booking->room)
                    <div class="flex justify-between gap-4">
                        <dt class="shrink-0 text-slate-600">{{ __('Phòng') }}</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $booking->room->code }} — {{ $booking->roomType->name }}</dd>
                    </div>
                @else
                    <div class="flex justify-between gap-4">
                        <dt class="shrink-0 text-slate-600">{{ __('Loại phòng') }}</dt>
                        <dd class="text-right font-medium text-slate-900">{{ $booking->roomType->name }}</dd>
                    </div>
                @endif

                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Ngày check-in') }}</dt>
                    <dd class="text-right font-medium tabular-nums text-slate-900">{{ $booking->check_in->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Giờ check-in dự kiến') }}</dt>
                    <dd class="text-right font-semibold tabular-nums text-slate-900">
                        @if ($booking->guest_planned_check_in)
                            {{ $booking->guest_planned_check_in }}
                        @elseif (isset($siteSetting))
                            <span class="font-medium text-slate-700">{{ $siteSetting->policy_check_in_start }} – {{ $siteSetting->policy_check_in_end }}</span>
                            <span class="ml-1 text-xs font-normal text-slate-500">{{ __('theo khung chuẩn') }}</span>
                        @else
                            —
                        @endif
                    </dd>
                </div>

                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Ngày check-out') }}</dt>
                    <dd class="text-right font-medium tabular-nums text-slate-900">{{ $booking->check_out->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Giờ check-out dự kiến') }} <span class="text-xs font-normal text-slate-500">({{ __('ngày trả phòng') }})</span></dt>
                    <dd class="text-right font-semibold tabular-nums text-slate-900">
                        @if ($booking->guest_planned_check_out)
                            {{ $booking->guest_planned_check_out }}
                        @elseif (isset($siteSetting))
                            <span class="font-medium text-slate-700">{{ $siteSetting->policy_check_out_start }} – {{ $siteSetting->policy_check_out_end }}</span>
                            <span class="ml-1 text-xs font-normal text-slate-500">{{ __('theo khung chuẩn') }}</span>
                        @else
                            —
                        @endif
                    </dd>
                </div>

                @isset($siteSetting)
                    <div class="rounded-lg border border-amber-100 bg-amber-50/80 px-3 py-3 text-xs leading-relaxed text-slate-700">
                        <p class="font-semibold text-amber-950">{{ __('Chính sách giờ nhận / trả phòng') }}</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">
                            <li>
                                <strong>{{ __('Check-in') }}</strong>
                                ({{ __('ngày nhận phòng') }} {{ $booking->check_in->format('d/m/Y') }}):
                                {{ $siteSetting->policy_check_in_start }} – {{ $siteSetting->policy_check_in_end }}
                            </li>
                            <li>
                                <strong>{{ __('Check-out') }}</strong>
                                ({{ __('ngày trả phòng') }} {{ $booking->check_out->format('d/m/Y') }}):
                                {{ $siteSetting->policy_check_out_start }} – {{ $siteSetting->policy_check_out_end }}
                            </li>
                        </ul>
                        <p class="mt-2 border-t border-amber-200/80 pt-2 text-slate-600">
                            {{ __('Phụ phí có thể áp dụng nếu đến sớm, đến trễ hoặc trả phòng muộn ngoài khung trên (sau thời gian ân hạn đã cấu hình). Check-out sớm không tính thêm.') }}
                        </p>
                    </div>
                @endisset

                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Số khách') }}</dt>
                    <dd class="text-right font-medium tabular-nums text-slate-900">{{ $booking->guests }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Giá / đêm') }}</dt>
                    <dd class="text-right font-medium tabular-nums text-slate-900">{{ number_format((float) $booking->rate_per_night, 0, ',', '.') }} VND</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="shrink-0 text-slate-600">{{ __('Số đêm') }}</dt>
                    <dd class="text-right font-medium tabular-nums text-slate-900">{{ $booking->nights() }}</dd>
                </div>
                <div class="flex justify-between gap-4 text-lg font-bold text-amber-900">
                    <dt class="shrink-0">{{ __('Tạm tính phòng') }}</dt>
                    <dd class="text-right tabular-nums">{{ number_format((float) $booking->rate_per_night * $booking->nights(), 0, ',', '.') }} VND</dd>
                </div>
                <div class="flex justify-between gap-4 border-t border-stone-200 pt-3">
                    <dt class="shrink-0 text-slate-600">{{ __('Tiền cọc') }}</dt>
                    <dd class="text-right font-semibold tabular-nums text-slate-900">{{ number_format((float) $booking->deposit_amount, 0, ',', '.') }} VND</dd>
                </div>
                @if ($booking->deposit_paid_at)
                    <div class="flex justify-between gap-4 text-sm">
                        <dt class="shrink-0 text-slate-600">{{ __('Thanh toán cọc') }}</dt>
                        <dd class="text-right text-emerald-700">
                            {{ __('Đã thanh toán') }}
                            @if ($booking->payment_method)
                                ({{ strtoupper($booking->payment_method) }})
                            @endif
                            — {{ $booking->deposit_paid_at->format('d/m/Y H:i') }}
                        </dd>
                    </div>
                @endif
                @if ($booking->guest_notes)
                    <div class="pt-2">
                        <dt class="text-slate-600">{{ __('Ghi chú') }}</dt>
                        <dd class="mt-1 whitespace-pre-wrap text-slate-800">{{ $booking->guest_notes }}</dd>
                    </div>
                @endif
            </dl>

            @if (auth()->check() && (int) auth()->id() === (int) $booking->user_id)
                <form method="post" action="{{ route('customer.bookings.confirm-review', $booking) }}" class="mt-8 space-y-3">
                    @csrf
                    <button type="submit" class="w-full rounded-full bg-amber-900 py-3 text-sm font-semibold text-white shadow-lg transition hover:bg-amber-950">
                        {{ __('Xác nhận và gửi đơn') }}
                    </button>
                    <a href="{{ route('guest.search-rooms') }}" class="block w-full rounded-full border border-slate-300 py-3 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        {{ __('Hủy và quay lại tìm phòng') }}
                    </a>
                </form>
            @elseif (! empty($loginRequiredToConfirm))
                <div class="mt-8 space-y-3">
                    <a href="{{ route('login') }}" class="block w-full rounded-full bg-amber-900 py-3 text-center text-sm font-semibold text-white shadow-lg transition hover:bg-amber-950">
                        {{ __('Đăng nhập để xác nhận gửi đơn') }}
                    </a>
                    <a href="{{ route('guest.search-rooms') }}" class="block w-full rounded-full border border-slate-300 py-3 text-center text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        {{ __('Hủy và quay lại tìm phòng') }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-guest-hotel-layout>
