<x-hotel-layout>
    <x-slot name="header">Dịch vụ</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">{{ session('status') }}</div>
    @endif

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.services.create') }}" class="inline-flex rounded-full bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-900/30 transition hover:-translate-y-0.5">Thêm dịch vụ</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr><th class="px-5 py-4">Tên</th><th class="px-5 py-4">Giá</th><th class="px-5 py-4">Trạng thái</th><th class="px-5 py-4 text-right">Thao tác</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($services as $s)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $s->name }}</td>
                        <td class="px-5 py-4 font-semibold tabular-nums text-amber-700">{{ number_format($s->price, 0, ',', '.') }} VND</td>
                        <td class="px-5 py-4">
                            <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $s->is_active ? 'bg-emerald-100 text-emerald-800 ring-emerald-200' : 'bg-gray-100 text-gray-600 ring-gray-200' }}">{{ $s->is_active ? 'Đang bật' : 'Đang tắt' }}</span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.services.edit', $s) }}" class="inline-flex rounded-full bg-violet-700 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-600">Sửa</a>
                            <form action="{{ route('admin.services.destroy', $s) }}" method="post" class="ml-2 inline" onsubmit="return confirm('Xác nhận xóa dịch vụ này?');">@csrf @method('DELETE')<button type="submit" class="inline-flex rounded-full bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700">Xóa</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $services->links() }}</div>
</x-hotel-layout>
