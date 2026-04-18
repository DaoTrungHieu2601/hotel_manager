<x-hotel-layout>
    <x-slot name="header">Quản lý tài khoản khách hàng</x-slot>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr>
                    <th class="px-5 py-4">Họ tên</th>
                    <th class="px-5 py-4">Số CCCD</th>
                    <th class="px-5 py-4">Email</th>
                    <th class="px-5 py-4">Số điện thoại</th>
                    <th class="px-5 py-4">Ngày tạo</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($customers as $u)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $u->cccd ?? '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $u->phone ?: '—' }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $u->created_at?->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-10 text-center text-gray-600">Chưa có tài khoản khách hàng.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-5">
        {{ $customers->links() }}
    </div>
</x-hotel-layout>

