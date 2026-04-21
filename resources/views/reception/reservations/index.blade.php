<x-hotel-layout>
    <x-slot name="header">Đặt phòng</x-slot>

    @php
        $statusClass = [
            \App\Models\Booking::STATUS_PENDING => 'bg-amber-100 text-amber-800 ring-amber-200',
            \App\Models\Booking::STATUS_CONFIRMED => 'bg-sky-100 text-sky-800 ring-sky-200',
            \App\Models\Booking::STATUS_CHECKED_IN => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            \App\Models\Booking::STATUS_CHECKED_OUT => 'bg-slate-100 text-slate-700 ring-slate-200',
            \App\Models\Booking::STATUS_CANCELLED => 'bg-rose-100 text-rose-700 ring-rose-200',
        ];
    @endphp

    <div class="mb-5 flex flex-wrap gap-2 text-sm">
        <a href="{{ route('reception.reservations.index', ['tab' => 'pending']) }}" class="rounded-full px-4 py-2 transition {{ $tab === 'pending' ? 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-300' : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200' }}">Chờ xác nhận</a>
        <a href="{{ route('reception.reservations.index', ['tab' => 'active']) }}" class="rounded-full px-4 py-2 transition {{ $tab === 'active' ? 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-300' : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200' }}">Đang chạy</a>
        <a href="{{ route('reception.reservations.index', ['tab' => 'past']) }}" class="rounded-full px-4 py-2 transition {{ $tab === 'past' ? 'bg-purple-600 text-white shadow-sm ring-2 ring-purple-300' : 'border border-gray-200 bg-gray-100 text-gray-800 hover:bg-gray-200' }}">Lịch sử</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-5 py-4">#</th>
                    <th class="px-5 py-4">Khách</th>
                    <th class="px-5 py-4">Loại phòng</th>
                    <th class="px-5 py-4">Ngày</th>
                    <th class="px-5 py-4">Trạng thái</th>
                    <th class="px-5 py-4 text-right">Mở</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bookings as $r)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $r->id }}</td>
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $r->user->name }}</td>
                        <td class="px-5 py-4 text-gray-800">{{ $r->roomType->name }}</td>
                        <td class="px-5 py-4 text-xs text-gray-600">{{ $r->check_in->format('d/m') }} - {{ $r->check_out->format('d/m') }}</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $statusClass[$r->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200' }}">{{ \App\Models\Booking::statusLabels()[$r->status] ?? $r->status }}</span>
                        </td>
                        <td class="px-5 py-4 text-right"><a href="{{ route('reception.reservations.show', $r) }}" class="inline-flex rounded-full bg-violet-700 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-600">Mở</a></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-600">Không có dữ liệu.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $bookings->links() }}</div>
</x-hotel-layout>
