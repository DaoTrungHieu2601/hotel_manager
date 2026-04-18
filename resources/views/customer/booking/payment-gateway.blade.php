<x-guest-hotel-layout :title="$gateway === 'vnpay' ? 'VNPAY' : 'MoMo'">
    <div class="mx-auto max-w-md px-4 py-12">
        <div class="rounded-3xl bg-white p-8 font-sans text-slate-900 shadow-xl ring-1 ring-stone-200">
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-xl px-3 py-1 text-xs font-bold text-white {{ $gateway === 'vnpay' ? 'bg-blue-700' : 'bg-fuchsia-600' }}">
                    {{ strtoupper($gateway) }}
                </span>
                <h1 class="font-display text-xl font-bold text-slate-900">{{ __('Cổng thanh toán (mô phỏng)') }}</h1>
            </div>
            <p class="mt-4 text-sm leading-relaxed text-slate-600">
                {{ __('Trên môi trường thật, khách sẽ được chuyển sang trang VNPAY hoặc MoMo. Sau khi thanh toán thành công, cổng sẽ gọi webhook của website và chuyển hướng khách về đây.') }}
            </p>
            <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 p-4 text-sm">
                <p><span class="text-slate-500">{{ __('Số tiền') }}:</span> <strong class="tabular-nums">{{ number_format((float) $booking->deposit_amount, 0, ',', '.') }} ₫</strong></p>
                <p class="mt-1"><span class="text-slate-500">{{ __('Mã tham chiếu') }}:</span> <strong>#{{ $booking->id }}</strong></p>
            </div>

            <form method="post" action="{{ route('customer.bookings.payment.complete', $booking) }}" class="mt-8 space-y-3">
                @csrf
                <input type="hidden" name="gateway" value="{{ $gateway }}" />
                <button type="submit" class="w-full rounded-full py-3 text-sm font-semibold text-white shadow-lg transition {{ $gateway === 'vnpay' ? 'bg-blue-700 hover:bg-blue-800' : 'bg-fuchsia-600 hover:bg-fuchsia-700' }}">
                    {{ __('Giả lập: thanh toán thành công') }}
                </button>
                <a href="{{ route('customer.bookings.payment', $booking) }}" class="block w-full rounded-full border border-slate-300 py-3 text-center text-sm font-medium text-slate-700 hover:bg-slate-50">
                    {{ __('Quay lại chọn cổng khác') }}
                </a>
            </form>
        </div>
    </div>
</x-guest-hotel-layout>
