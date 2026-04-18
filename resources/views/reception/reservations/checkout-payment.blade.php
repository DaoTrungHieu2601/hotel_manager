<x-hotel-layout>
    <x-slot name="header">Chọn phương thức thanh toán - Đơn #{{ $booking->id }}</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900">{{ session('error') }}</div>
    @endif

    <div class="mx-auto max-w-3xl space-y-6">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Tạm tính thanh toán khi check-out</h2>
            <ul class="mt-4 space-y-2 text-sm">
                <li class="flex justify-between gap-4"><span class="text-gray-600">Khách</span><span class="font-medium text-gray-900">{{ $booking->user->name }}</span></li>
                <li class="flex justify-between gap-4"><span class="text-gray-600">Phòng</span><span class="text-gray-900">{{ $booking->room?->code ?? '?' }} - {{ $booking->roomType->name }}</span></li>
                <li class="flex justify-between gap-4"><span class="text-gray-600">Tiền phòng ({{ $nights }} đêm)</span><span class="tabular-nums font-medium text-gray-900">{{ number_format((float) $roomSubtotal, 0, ',', '.') }} VND</span></li>
                <li class="flex justify-between gap-4"><span class="text-gray-600">Dịch vụ</span><span class="tabular-nums font-medium text-gray-900">{{ number_format((float) $servicesSubtotal, 0, ',', '.') }} VND</span></li>
                @if((float) ($earlyLateSubtotal ?? 0) > 0)
                <li class="flex justify-between gap-4"><span class="text-gray-600">Phụ phí ngoài giờ ({{ $earlyLateHours ?? 0 }} giờ)</span><span class="tabular-nums font-medium text-gray-900">{{ number_format((float) $earlyLateSubtotal, 0, ',', '.') }} VND</span></li>
                @endif
                <li class="flex justify-between gap-4"><span class="text-gray-600">Tiền cọc</span><span class="tabular-nums font-medium text-gray-900">- {{ number_format((float) $deposit, 0, ',', '.') }} VND</span></li>
                <li class="flex justify-between gap-4 border-t border-gray-200 pt-2 text-lg font-semibold">
                    <span class="text-gray-900">Tổng cần thu</span>
                    <span class="tabular-nums text-amber-700">{{ number_format((float) $totalPayable, 0, ',', '.') }} VND</span>
                </li>
            </ul>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <form method="post" action="{{ route('reception.stays.check-out-cash', $booking) }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900">Tiền mặt</h3>
                <label class="mt-4 flex items-start gap-2.5 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="cash_received_confirmed"
                        value="1"
                        class="mt-0.5 rounded border-gray-300 text-emerald-600 focus:ring-emerald-300"
                        required
                    >
                    <span>Tôi xác nhận đã thu đủ tiền mặt từ khách.</span>
                </label>
                <button type="submit" class="mt-5 w-full rounded-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-emerald-700">
                    Xác nhận thu tiền mặt và tạo hóa đơn
                </button>
            </form>

            <form method="post" action="{{ route('vnpay.create-payment') }}" class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                @csrf
                <h3 class="text-lg font-semibold text-gray-900">VNPAY</h3>
                <input type="hidden" name="amount" value="{{ (int) round((float) $totalPayable) }}" />
                <input type="hidden" name="order_id" value="RCO-{{ $booking->id }}-{{ now('Asia/Ho_Chi_Minh')->format('YmdHis') }}" />
                <input type="hidden" name="order_info" value="Checkout booking #{{ $booking->id }}" />
                <button type="submit" class="mt-5 w-full rounded-full bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
                    Thanh toán qua VNPAY
                </button>
            </form>
        </div>
    </div>
</x-hotel-layout>
