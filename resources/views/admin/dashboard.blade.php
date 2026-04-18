<x-hotel-layout>
    <x-slot name="header">Báo cáo &amp; thống kê</x-slot>

    <div class="mb-6 grid gap-4 md:grid-cols-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-normal uppercase tracking-wide text-gray-600">Tỷ lệ lấp đầy tháng này</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $occupancyPct }}%</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-normal uppercase tracking-wide text-gray-600">Mốc đánh giá</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ now()->format('m/Y') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
            <p class="text-xs font-normal uppercase tracking-wide text-gray-600">Kênh</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">Hóa đơn</p>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Trạng thái đặt phòng (khách)</h2>
                <p class="mt-1 text-sm font-normal text-gray-600">Số lượng đơn theo từng trạng thái trong toàn hệ thống.</p>
            </div>
            <p class="rounded-full bg-gray-100 px-4 py-1.5 text-sm font-semibold text-gray-800">
                Tổng đơn: <span class="tabular-nums">{{ $bookingsTotal }}</span>
            </p>
        </div>

        <div class="mt-6 grid gap-8 lg:grid-cols-2 lg:items-center">
            <div class="overflow-hidden rounded-xl border border-gray-100">
                <table class="min-w-full divide-y divide-gray-100 text-sm">
                    <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                        <tr>
                            <th class="px-4 py-3">Trạng thái</th>
                            <th class="px-4 py-3 text-right">Số đơn</th>
                            <th class="hidden px-4 py-3 text-right sm:table-cell">Tỷ lệ</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($bookingStatusRows as $row)
                            @php
                                $pct = $bookingsTotal > 0 ? round(($row['count'] / $bookingsTotal) * 100, 1) : 0;
                                $barClass = match ($row['key']) {
                                    \App\Models\Booking::STATUS_DRAFT => 'bg-violet-500',
                                    \App\Models\Booking::STATUS_PENDING => 'bg-amber-500',
                                    \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-500',
                                    \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-500',
                                    \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-400',
                                    \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-500',
                                    default => 'bg-gray-400',
                                };
                            @endphp
                            <tr class="hover:bg-gray-50/80">
                                <td class="px-4 py-3">
                                    <span class="inline-flex items-center gap-2 font-medium text-gray-900">
                                        <span class="h-2.5 w-2.5 shrink-0 rounded-full {{ $barClass }}" aria-hidden="true"></span>
                                        {{ $row['label'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right tabular-nums font-semibold text-gray-900">{{ $row['count'] }}</td>
                                <td class="hidden px-4 py-3 text-right tabular-nums text-gray-600 sm:table-cell">{{ $pct }}%</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="flex min-h-[240px] max-w-md items-center justify-center justify-self-center lg:justify-self-end">
                <canvas id="bookingStatusChart" class="max-h-64 w-full max-w-xs"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Doanh thu theo tháng</h2>
        <p class="mt-1 text-sm font-normal text-gray-600">Thống kê 6 tháng gần nhất từ hóa đơn đã phát hành.</p>
        <canvas id="revChart" height="110" class="mt-5"></canvas>

        <div class="mt-6 grid gap-4 md:grid-cols-2">
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4">
                <p class="text-xs font-normal uppercase tracking-wide text-gray-600">Tổng doanh thu năm nay</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($revenueThisYear, 0, ',', '.') }} VND</p>
            </div>
            <div class="rounded-2xl border border-purple-200 bg-purple-50 p-4">
                <p class="text-xs font-normal uppercase tracking-wide text-gray-600">Tổng doanh thu tuần này</p>
                <p class="mt-2 text-3xl font-bold text-gray-900">{{ number_format($revenueThisWeek, 0, ',', '.') }} VND</p>
            </div>
        </div>
    </div>

    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900">Xem lại toàn bộ hóa đơn</h2>
        <p class="mt-1 text-sm font-normal text-gray-600">Danh sách hóa đơn đã phát hành, sắp xếp từ mới nhất đến cũ nhất.</p>

        <div class="mt-4 overflow-x-auto rounded-2xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                    <tr>
                        <th class="px-4 py-3">Mã hóa đơn</th>
                        <th class="px-4 py-3">Khách hàng</th>
                        <th class="px-4 py-3">Ngày lập</th>
                        <th class="px-4 py-3 text-right">Tổng tiền</th>
                        <th class="px-4 py-3 text-right">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $invoice->invoice_number }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $invoice->booking->user->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $invoice->issued_at?->format('d/m/Y H:i') ?? '—' }}</td>
                            <td class="px-4 py-3 text-right tabular-nums text-gray-900">{{ number_format((float) $invoice->total, 0, ',', '.') }} VND</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('invoices.show', $invoice) }}" class="inline-flex rounded-full bg-purple-100 px-3 py-1.5 text-xs font-medium text-purple-700 hover:bg-purple-200">
                                    Xem chi tiết
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm font-normal text-gray-600">Chưa có hóa đơn nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            const statusCtx = document.getElementById('bookingStatusChart');
            if (statusCtx) {
                const statusLabels = @json($bookingStatusRows->pluck('label'));
                const statusData = @json($bookingStatusRows->pluck('count'));
                const statusColors = [
                    'rgba(139, 92, 246, 0.85)',
                    'rgba(245, 158, 11, 0.85)',
                    'rgba(14, 165, 233, 0.85)',
                    'rgba(16, 185, 129, 0.85)',
                    'rgba(148, 163, 184, 0.9)',
                    'rgba(244, 63, 94, 0.85)',
                ];
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: statusLabels,
                        datasets: [{
                            data: statusData,
                            backgroundColor: statusColors,
                            borderWidth: 2,
                            borderColor: '#ffffff',
                            hoverOffset: 6,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    boxWidth: 12,
                                    padding: 12,
                                    color: '#374151',
                                    font: { size: 11 },
                                },
                            },
                        },
                    },
                });
            }

            const ctx = document.getElementById('revChart');
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: @json($labels),
                    datasets: [{
                        label: 'Doanh thu (VND)',
                        data: @json($revenue),
                        backgroundColor: 'rgba(124, 58, 237, 0.75)',
                        borderRadius: 12,
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            ticks: { color: '#4b5563', font: { size: 11 } },
                            grid: { color: 'rgba(229, 231, 235, 0.9)' },
                        },
                        y: {
                            beginAtZero: true,
                            ticks: { color: '#4b5563', font: { size: 11 } },
                            grid: { color: 'rgba(229, 231, 235, 0.9)' },
                        },
                    },
                    plugins: {
                        legend: {
                            labels: {
                                boxWidth: 14,
                                color: '#374151',
                                font: { size: 12, weight: '500' },
                            },
                        },
                    },
                }
            });
        </script>
    @endpush
</x-hotel-layout>
