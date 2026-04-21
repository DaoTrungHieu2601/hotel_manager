<x-hotel-layout>
    <x-slot name="header">Đơn #{{ $booking->id }}</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900 shadow-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="font-semibold text-gray-900">Thông tin đơn</h2>
            <dl class="grid gap-2 text-sm">
                <div class="flex justify-between gap-4"><dt class="text-gray-600">Khách</dt><dd class="text-right font-medium text-gray-900">{{ $booking->user->name }} ({{ $booking->user->email }})</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-600">Loại phòng</dt><dd class="text-right text-gray-900">{{ $booking->roomType->name }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-600">Phòng</dt><dd class="text-right text-gray-900">{{ $booking->room?->code ?? '?' }}</dd></div>
                <div class="flex justify-between gap-4"><dt class="text-gray-600">Trạng thái</dt><dd class="text-right font-medium text-gray-900">{{ \App\Models\Booking::statusLabels()[$booking->status] ?? $booking->status }}</dd></div>
                @if($booking->guest_planned_check_in || $booking->guest_planned_check_out)
                    <div class="flex justify-between gap-4"><dt class="text-gray-600">Giờ dự kiến (khách)</dt><dd class="text-right text-gray-900">{{ $booking->guest_planned_check_in ?? '—' }} / {{ $booking->guest_planned_check_out ?? '—' }}</dd></div>
                @endif
            </dl>

            @if($booking->status === \App\Models\Booking::STATUS_PENDING)
                <form method="post" action="{{ route('reception.reservations.confirm', $booking) }}" class="space-y-3 border-t border-gray-200 pt-4">
                    @csrf
                    @if($booking->room_id)
                        <p class="text-sm text-gray-700">Khách đã chọn phòng {{ $booking->room?->code ?? '?' }}. Bấm xác nhận để duyệt.</p>
                        <button type="submit" class="rounded-full bg-purple-600 px-6 py-2 text-sm font-semibold text-white hover:bg-purple-700">Xác nhận đơn</button>
                    @else
                        <label class="text-xs font-semibold text-gray-600">Gán phòng</label>
                        <select name="room_id" class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-gray-900 shadow-sm" required>
                            <option value="">-- Chọn phòng trống --</option>
                            @foreach($assignableRooms as $rm)
                                <option value="{{ $rm->id }}">{{ $rm->code }} ({{ $rm->roomType->name }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-full bg-purple-600 px-6 py-2 text-sm font-semibold text-white hover:bg-purple-700">Xác nhận đơn</button>
                    @endif
                </form>
            @endif

            @if($booking->status === \App\Models\Booking::STATUS_CONFIRMED)
                <a href="{{ route('reception.stays.check-in-form', $booking) }}" class="inline-flex rounded-full bg-emerald-600 px-6 py-2 text-sm font-semibold text-white hover:bg-emerald-700">Check-in</a>
            @endif

            @if($booking->status === \App\Models\Booking::STATUS_CHECKED_IN)
                <a href="{{ route('reception.stays.checkout-payment', $booking) }}" class="inline-flex rounded-full bg-rose-600 px-6 py-2 text-sm font-semibold text-white hover:bg-rose-700">Check-out &amp; chọn thanh toán</a>
            @endif

            @if(! in_array($booking->status, [\App\Models\Booking::STATUS_CHECKED_OUT, \App\Models\Booking::STATUS_CANCELLED], true))
                <form method="post" action="{{ route('reception.reservations.cancel', $booking) }}" onsubmit="return confirm('Xác nhận hủy đơn đặt phòng này?');" class="pt-2">@csrf
                    <button type="submit" class="text-sm font-medium text-rose-700 hover:underline">Hủy đặt phòng</button>
                </form>
            @endif
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-gray-900">Dịch vụ phát sinh</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    @forelse($booking->bookingServices as $line)
                        <li class="flex justify-between border-b border-gray-100 py-1.5 text-gray-900">
                            <span>{{ $line->service->name }} x {{ $line->quantity }}</span>
                            <span class="font-semibold tabular-nums text-amber-700">{{ number_format((float) $line->lineTotal(), 0, ',', '.') }} VND</span>
                        </li>
                    @empty
                        <li class="text-gray-600">Chưa có</li>
                    @endforelse
                </ul>

                @if($booking->status === \App\Models\Booking::STATUS_CHECKED_IN)
                    <form method="post" action="{{ route('reception.reservation-services.store', $booking) }}" class="mt-4 border-t border-gray-200 pt-4">
                        @csrf
                        <div class="overflow-x-auto rounded-xl border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200 text-sm">
                                <thead class="bg-gray-50 text-left text-xs font-medium uppercase text-gray-600">
                                    <tr>
                                        <th class="px-3 py-2">Chọn</th>
                                        <th class="px-3 py-2">Dịch vụ</th>
                                        <th class="px-3 py-2 text-right">Đơn giá</th>
                                        <th class="px-3 py-2 text-right">Số lượng</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach($services as $svc)
                                        @php($checked = isset($selectedQuantities[$svc->id]))
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-3 py-2">
                                                <input
                                                    type="checkbox"
                                                    name="services[]"
                                                    value="{{ $svc->id }}"
                                                    class="rounded border-gray-400 bg-white text-purple-600 focus:ring-purple-300"
                                                    @checked($checked)
                                                />
                                            </td>
                                            <td class="px-3 py-2 text-gray-900">{{ $svc->name }}</td>
                                            <td class="px-3 py-2 text-right tabular-nums text-gray-800">{{ number_format((float) $svc->price, 0, ',', '.') }} VND</td>
                                            <td class="px-3 py-2 text-right">
                                                <input
                                                    type="number"
                                                    name="quantities[{{ $svc->id }}]"
                                                    value="{{ $selectedQuantities[$svc->id] ?? 1 }}"
                                                    min="0"
                                                    class="w-20 rounded-lg border border-gray-300 bg-white text-right text-sm text-gray-900"
                                                />
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <p class="mt-2 text-xs text-gray-600">Tick dịch vụ đã sử dụng, sửa số lượng, rồi bấm cập nhật.</p>
                        <button type="submit" class="mt-3 rounded-full bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-800">Cập nhật dịch vụ</button>
                    </form>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="font-semibold text-gray-900">Tạm tính check-out</h2>
                <ul class="mt-3 space-y-2 text-sm">
                    <li class="flex justify-between gap-4">
                        <span class="text-gray-600">Tiền phòng ({{ $booking->nights() }} đêm)</span>
                        <span class="tabular-nums font-medium text-gray-900">{{ number_format($roomSubtotal, 0, ',', '.') }} VND</span>
                    </li>
                    <li class="flex justify-between gap-4">
                        <span class="text-gray-600">Dịch vụ</span>
                        <span class="tabular-nums font-medium text-gray-900">{{ number_format($servicesSubtotal, 0, ',', '.') }} VND</span>
                    </li>
                    @if(($earlyLateSubtotal ?? 0) > 0)
                    <li class="flex justify-between gap-4">
                        <span class="text-gray-600">Phụ phí ngoài giờ (sớm/muộn, {{ $earlyLateHours ?? 0 }} giờ tính phí)</span>
                        <span class="tabular-nums font-medium text-gray-900">{{ number_format($earlyLateSubtotal, 0, ',', '.') }} VND</span>
                    </li>
                    @endif
                    <li class="flex justify-between gap-4">
                        <span class="text-gray-600">Tiền cọc</span>
                        <span class="tabular-nums font-medium text-gray-900">- {{ number_format($deposit, 0, ',', '.') }} VND</span>
                    </li>
                    <li class="flex justify-between gap-4 border-t border-gray-200 pt-2 text-base font-semibold">
                        <span class="text-gray-900">Tổng thu khi check-out</span>
                        <span class="tabular-nums text-amber-700">{{ number_format($estimatedTotal, 0, ',', '.') }} VND</span>
                    </li>
                </ul>
                @if($booking->invoice)
                    <p class="mt-3 text-xs font-medium text-emerald-800">Đơn đã check-out. Tổng tiền chính thức theo hóa đơn.</p>
                @endif
            </div>

            @if($booking->invoice)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="font-semibold text-gray-900">Hóa đơn</h2>
                    <p class="mt-2 text-sm text-gray-800">{{ $booking->invoice->invoice_number }} - <span class="font-semibold text-amber-700">{{ number_format($booking->invoice->total, 0, ',', '.') }} VND</span></p>
                    <a href="{{ route('invoices.pdf', $booking->invoice) }}" class="mt-3 inline-flex rounded-full bg-purple-600 px-4 py-2 text-xs font-semibold text-white hover:bg-purple-700">In hóa đơn</a>
                </div>
            @endif
        </div>
    </div>
</x-hotel-layout>
