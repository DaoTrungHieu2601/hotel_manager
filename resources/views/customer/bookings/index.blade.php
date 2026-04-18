<x-hotel-layout>
    <x-slot name="header">{{ __('Đơn đặt phòng của tôi') }}</x-slot>

    @php
        $statusClass = [
            \App\Models\Booking::STATUS_DRAFT => 'bg-violet-100 text-violet-900 ring-violet-200',
            \App\Models\Booking::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
            \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-100 text-sky-800 ring-sky-200',
            \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-100 text-slate-700 ring-slate-200',
            \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
    @endphp

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-sm text-emerald-700 shadow-sm">
            {{ session('status') }}
        </div>
    @endif

    <section class="mb-6 grid gap-4 sm:grid-cols-3">
        <div class="rounded-xl border border-gray-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Tổng đơn') }}</p>
            <p class="mt-2 text-3xl font-bold text-slate-800">{{ $bookings->total() }}</p>
        </div>
        <div class="rounded-xl border border-amber-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Chờ xác nhận') }}</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $bookings->getCollection()->where('status', \App\Models\Booking::STATUS_PENDING)->count() }}</p>
        </div>
        <div class="rounded-xl border border-emerald-100 bg-white p-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Đang lưu trú') }}</p>
            <p class="mt-2 text-3xl font-bold text-emerald-600">{{ $bookings->getCollection()->where('status', \App\Models\Booking::STATUS_CHECKED_IN)->count() }}</p>
        </div>
    </section>

    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-semibold uppercase text-slate-500">
                <tr>
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">{{ __('Loại phòng') }}</th>
                    <th class="px-5 py-4">{{ __('Ngày ở') }}</th>
                    <th class="px-5 py-4">{{ __('Trạng thái') }}</th>
                    <th class="px-5 py-4 text-right">{{ __('Thao tác') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $b)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-slate-800">{{ $b->id }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-800">{{ $b->roomType->name }}</p>
                            <p class="text-xs text-slate-500">{{ __('Phòng') }}: {{ $b->room?->code ?? '—' }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-600">{{ $b->check_in->format('d/m') }} – {{ $b->check_out->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass[$b->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                {{ \App\Models\Booking::statusLabels()[$b->status] ?? $b->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('customer.bookings.show', $b) }}" class="inline-flex rounded-full bg-violet-600 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-700">
                                {{ __('Chi tiết') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-slate-500">{{ __('Bạn chưa có đơn nào. Hãy đặt phòng để bắt đầu!') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $bookings->links() }}
    </div>
</x-hotel-layout>
