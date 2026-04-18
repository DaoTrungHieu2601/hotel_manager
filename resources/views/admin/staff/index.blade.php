<x-hotel-layout>
    <x-slot name="header">Tài khoản nhân viên</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-900 shadow-sm">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700 shadow-sm">{{ session('error') }}</div>
    @endif

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('admin.staff.create') }}" class="inline-flex rounded-full bg-gradient-to-r from-violet-700 to-fuchsia-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-900/30 transition hover:-translate-y-0.5">Thêm nhân viên</a>
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50 text-left text-xs font-medium uppercase tracking-wide text-gray-600">
                <tr><th class="px-5 py-4">Họ tên</th><th class="px-5 py-4">Email</th><th class="px-5 py-4">Vai trò</th><th class="px-5 py-4 text-right">Thao tác</th></tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($staff as $u)
                    <tr class="transition hover:bg-gray-50">
                        <td class="px-5 py-4 font-medium text-gray-900">{{ $u->name }}</td>
                        <td class="px-5 py-4 text-gray-600">{{ $u->email }}</td>
                        <td class="px-5 py-4">
                            @php
                                $roleBadge = match($u->role) {
                                    'admin'        => ['cls' => 'bg-violet-100 text-violet-800 ring-violet-200',  'icon' => '🔑', 'lbl' => 'Quản trị viên'],
                                    'receptionist' => ['cls' => 'bg-emerald-100 text-emerald-800 ring-emerald-200','icon' => '🏨', 'lbl' => 'Nhân viên'],
                                    'manager'      => ['cls' => 'bg-sky-100 text-sky-800 ring-sky-200',           'icon' => '👔', 'lbl' => 'Trưởng phòng'],
                                    'accountant'   => ['cls' => 'bg-rose-100 text-rose-800 ring-rose-200',        'icon' => '🧾', 'lbl' => 'Kế toán'],
                                    default        => ['cls' => 'bg-gray-100 text-gray-800 ring-gray-200',     'icon' => '👤', 'lbl' => $u->role],
                                };
                            @endphp
                            <span class="inline-flex items-center gap-1 rounded-full px-3 py-1 text-xs font-semibold ring-1 {{ $roleBadge['cls'] }}">
                                {{ $roleBadge['icon'] }} {{ $roleBadge['lbl'] }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('admin.staff.edit', $u) }}" class="inline-flex rounded-full bg-violet-700 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-violet-600">Sửa</a>
                            <form action="{{ route('admin.staff.destroy', $u) }}" method="post" class="ml-2 inline" onsubmit="return confirm('Xác nhận xóa nhân viên này?');">@csrf @method('DELETE')<button type="submit" class="inline-flex rounded-full bg-rose-600 px-4 py-1.5 text-xs font-semibold text-white transition hover:bg-rose-700">Xóa</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-5">{{ $staff->links() }}</div>
</x-hotel-layout>
