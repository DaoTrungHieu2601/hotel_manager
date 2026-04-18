<x-hotel-layout>
    <x-slot name="header">Đơn đặt phòng (Admin)</x-slot>

    @php
        $statusClass = [
            \App\Models\Booking::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
            \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-100 text-sky-800 ring-sky-200',
            \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-100 text-slate-700 ring-slate-200',
            \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">Khách</th>
                    <th class="px-5 py-4">Loại / Phòng</th>
                    <th class="px-5 py-4">Ngày</th>
                    <th class="px-5 py-4">Trạng thái</th>
                    <th class="px-5 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $b)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $b->id }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $b->user->name }}</td>
                        <td class="px-5 py-4 text-gray-800">{{ $b->roomType->name }} @if($b->room) · {{ $b->room->code }} @endif</td>
                        <td class="px-5 py-4 text-xs text-gray-600">{{ $b->check_in->format('d/m') }} - {{ $b->check_out->format('d/m/Y') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass[$b->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">
                                {{ \App\Models\Booking::statusLabels()[$b->status] ?? $b->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('reception.reservations.show', $b) }}" class="inline-flex rounded-full bg-violet-700 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-600">Chi tiết</a>
                            @if($b->status === \App\Models\Booking::STATUS_CONFIRMED && $b->room_id)
                                <a href="{{ route('reception.stays.check-in-form', $b) }}" class="ml-2 inline-flex rounded-full bg-emerald-600 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-emerald-700">Check-in</a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-600">Chưa có đơn.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
</x-hotel-layout>
