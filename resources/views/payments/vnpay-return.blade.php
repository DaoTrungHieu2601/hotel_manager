<x-guest-hotel-layout :title="__('Kết quả thanh toán VNPAY')">
    <div class="mx-auto max-w-xl px-4 py-12">
        <div class="rounded-3xl bg-white p-8 font-sans text-slate-900 shadow-xl ring-1 ring-stone-200">
            <h1 class="font-display text-2xl font-bold {{ $success ? 'text-emerald-700' : 'text-rose-700' }}">
                {{ $success ? __('Thanh toán thành công') : __('Thanh toán không thành công') }}
            </h1>

            <div class="mt-5 space-y-2 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">{{ __('Mã đơn') }}</dt>
                    <dd class="font-semibold tabular-nums text-slate-900">{{ $txnRef ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">{{ __('Số tiền') }}</dt>
                    <dd class="font-semibold tabular-nums text-slate-900">
                        @if ($amount !== null)
                            {{ number_format((float) $amount, 0, ',', '.') }} ₫
                        @else
                            —
                        @endif
                    </dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">{{ __('Mã phản hồi') }}</dt>
                    <dd class="font-semibold tabular-nums text-slate-900">{{ $responseCode ?: '—' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-slate-600">{{ __('Chữ ký hợp lệ') }}</dt>
                    <dd class="font-semibold {{ $isValidSignature ? 'text-emerald-700' : 'text-rose-700' }}">
                        {{ $isValidSignature ? __('Có') : __('Không') }}
                    </dd>
                </div>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('home') }}" class="inline-flex flex-1 items-center justify-center rounded-full bg-slate-900 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-950">
                    {{ __('Về trang chủ') }}
                </a>
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex flex-1 items-center justify-center rounded-full border border-slate-300 px-6 py-3 text-sm font-semibold text-slate-800 transition hover:bg-slate-50">
                        {{ __('Bảng điều khiển') }}
                    </a>
                @endauth
            </div>

            @if (! $success)
                <p class="mt-6 text-xs leading-relaxed text-slate-500">
                    {{ __('Nếu bạn đã bị trừ tiền nhưng hệ thống báo thất bại, vui lòng liên hệ hỗ trợ và gửi kèm mã đơn + ảnh biên lai.') }}
                </p>
            @endif
        </div>
    </div>
</x-guest-hotel-layout>

