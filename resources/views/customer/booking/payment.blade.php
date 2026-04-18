<x-guest-hotel-layout :title="__('Thanh toán cọc')">
    <div class="mx-auto max-w-lg px-4 py-12">
        <div class="rounded-3xl bg-white p-8 font-sans text-slate-900 shadow-xl ring-1 ring-stone-200">
            <h1 class="font-display text-2xl font-bold text-amber-950">{{ __('Thanh toán cọc') }}</h1>
            <p class="mt-2 text-sm text-slate-600">{{ __('Chọn cổng thanh toán. Sau khi hoàn tất, bạn sẽ được chuyển đến trang xem lại đơn.') }}</p>

            @if (session('error'))
                <div class="mt-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">{{ session('error') }}</div>
            @endif

            <div class="mt-6 rounded-2xl border border-amber-200 bg-amber-50/80 p-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-800">{{ __('Số tiền cọc') }}</p>
                <p class="mt-1 text-2xl font-bold tabular-nums text-amber-950">{{ number_format((float) $booking->deposit_amount, 0, ',', '.') }} ₫</p>
                @if ($booking->room)
                    <p class="mt-2 text-sm text-amber-900/80">{{ __('Phòng') }} {{ $booking->room->code }} — {{ $booking->roomType->name }}</p>
                @else
                    <p class="mt-2 text-sm text-amber-900/80">{{ $booking->roomType->name }}</p>
                @endif
            </div>

            <p class="mt-4 text-xs leading-relaxed text-slate-500">
                {{ __('Tích hợp VNPAY/MOMO thật cần cấu hình merchant và khóa API trên máy chủ. Hiện tại luồng dưới đây là mô phỏng để bạn nối API sau.') }}
            </p>

            <div class="mt-6 grid gap-3">
                <form method="post" action="{{ route('vnpay.create-payment') }}">
                    @csrf
                    <input type="hidden" name="amount" value="{{ (int) $booking->deposit_amount }}" />
                    <input type="hidden" name="order_id" value="{{ (string) $booking->id }}" />
                    <input type="hidden" name="order_info" value="{{ __('Thanh toán tiền cọc cho đơn #:id', ['id' => $booking->id]) }}" />
                    <button
                        type="submit"
                        class="flex w-full items-center justify-center gap-3 rounded-2xl bg-blue-700 px-5 py-4 text-sm font-bold text-white shadow-lg transition hover:bg-blue-800"
                    >
                        <span class="rounded bg-white/15 px-2 py-0.5 text-xs">VNPAY</span>
                        {{ __('Thanh toán qua VNPAY') }}
                    </button>
                </form>
                <a
                    href="{{ route('customer.bookings.payment.gateway', [$booking, 'momo']) }}"
                    class="flex items-center justify-center gap-3 rounded-2xl bg-fuchsia-600 px-5 py-4 text-sm font-bold text-white shadow-lg transition hover:bg-fuchsia-700"
                >
                    <span class="rounded bg-white/20 px-2 py-0.5 text-xs">MoMo</span>
                    {{ __('Thanh toán qua MoMo') }}
                </a>
            </div>

            <a href="{{ route('customer.bookings.review', $booking) }}" class="mt-6 block text-center text-sm text-slate-500 underline decoration-slate-300 hover:text-slate-800">
                {{ __('Quay lại xem lại đơn (sau khi đã thanh toán)') }}
            </a>
        </div>
    </div>
</x-guest-hotel-layout>
