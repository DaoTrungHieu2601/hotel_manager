<x-hotel-layout>
    <x-slot name="header">Danh sách phòng</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">{{ session('status') }}</div>
    @endif

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.hotel-rooms.create') }}" class="inline-flex rounded-full bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-900/30 transition hover:-translate-y-0.5">Thêm phòng</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr><th class="px-5 py-4">Mã</th><th class="px-5 py-4">Loại</th><th class="px-5 py-4">Trạng thái</th><th class="px-5 py-4 text-right">Thao tác</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($rooms as $r)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $r->code }}</td>
                        <td class="px-5 py-4 text-gray-800">{{ $r->roomType->name }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ \App\Models\Room::statusLabels()[$r->status] ?? $r->status }}</td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.hotel-rooms.edit', $r) }}" class="inline-flex rounded-full bg-violet-700 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-600">Sửa</a>
                            <form action="{{ route('admin.hotel-rooms.destroy', $r) }}" method="post" class="ml-2 inline" onsubmit="return confirm('Xác nhận xóa phòng này?');">@csrf @method('DELETE')<button type="submit" class="inline-flex rounded-full bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700">Xóa</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $rooms->links() }}</div>
</x-hotel-layout>
