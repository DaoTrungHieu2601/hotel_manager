<x-hotel-layout>
    <x-slot name="header">Hóa đơn {{ $invoice->invoice_number }}</x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">{{ $invoice->invoice_number }}</h2>
                    <p class="mt-1 text-sm text-slate-500">Ngày lập: {{ $invoice->issued_at?->format('d/m/Y H:i') }}</p>
                </div>
                <a href="{{ route('invoices.pdf', $invoice) }}" class="inline-flex rounded-full bg-violet-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-violet-700">
                    In hóa đơn
                </a>
            </div>

            <dl class="mt-6 grid gap-3 text-sm sm:grid-cols-2">
                <div><dt class="text-slate-500">Khách hàng</dt><dd class="mt-1 font-medium text-slate-800">{{ $invoice->booking->user->name }}</dd></div>
                <div><dt class="text-slate-500">Email</dt><dd class="mt-1 text-slate-700">{{ $invoice->booking->user->email }}</dd></div>
                <div><dt class="text-slate-500">Loại phòng</dt><dd class="mt-1 text-slate-700">{{ $invoice->booking->roomType->name }}</dd></div>
                <div><dt class="text-slate-500">Phòng</dt><dd class="mt-1 text-slate-700">{{ $invoice->booking->room?->code ?? '—' }}</dd></div>
                <div><dt class="text-slate-500">Check-in</dt><dd class="mt-1 text-slate-700">{{ $invoice->booking->check_in->format('d/m/Y') }}</dd></div>
                <div><dt class="text-slate-500">Check-out</dt><dd class="mt-1 text-slate-700">{{ $invoice->booking->check_out->format('d/m/Y') }}</dd></div>
            </dl>
        </div>

        <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
            <table class="min-w-full divide-y divide-gray-100 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-slate-500">
                    <tr>
                        <th class="px-5 py-3">Khoản mục</th>
                        <th class="px-5 py-3 text-right">Số tiền</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Tiền phòng ({{ $invoice->nights }} đêm)</td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-800">{{ number_format((float) $invoice->room_subtotal, 0, ',', '.') }} VND</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Dịch vụ phát sinh</td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-800">{{ number_format((float) $invoice->services_subtotal, 0, ',', '.') }} VND</td>
                    </tr>
                    @if((float) ($invoice->early_late_subtotal ?? 0) > 0)
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Phụ phí nhận / trả phòng ngoài giờ</td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-800">{{ number_format((float) $invoice->early_late_subtotal, 0, ',', '.') }} VND</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="px-5 py-3 text-slate-700">Tiền cọc</td>
                        <td class="px-5 py-3 text-right tabular-nums text-slate-800">- {{ number_format((float) $invoice->deposit, 0, ',', '.') }} VND</td>
                    </tr>
                    <tr>
                        <td class="px-5 py-4 text-base font-semibold text-slate-800">Tổng thanh toán</td>
                        <td class="px-5 py-4 text-right text-base font-semibold tabular-nums text-amber-600">{{ number_format((float) $invoice->total, 0, ',', '.') }} VND</td>
                    </tr>
                </tbody>
            </table>
        </div>

        @if($invoice->booking->bookingServices->count())
            <div class="rounded-xl border border-gray-100 bg-white p-6 shadow-sm">
                <h3 class="font-semibold text-slate-800">Chi tiết dịch vụ</h3>
                <ul class="mt-3 space-y-2 text-sm">
                    @foreach($invoice->booking->bookingServices as $line)
                        <li class="flex justify-between border-b border-gray-100 pb-2">
                            <span class="text-slate-700">{{ $line->service->name }} x {{ $line->quantity }}</span>
                            <span class="tabular-nums text-slate-800">{{ number_format((float) $line->lineTotal(), 0, ',', '.') }} VND</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</x-hotel-layout>

