<x-hotel-layout>
    <x-slot name="header">{{ __('Phân quyền người dùng') }}</x-slot>

    @if (session('status'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">{{ session('status') }}</div>
    @endif
    @if (session('error'))
        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-900">{{ session('error') }}</div>
    @endif

    {{-- Thống kê --}}
    <div class="mb-7 grid gap-4 sm:grid-cols-3 lg:grid-cols-5">
        @php
        $statCards = [
            ['label'=>'Quản trị viên','key'=>'admin',        'count'=>$stats['admin'],        'color'=>'text-violet-700', 'border'=>'border-violet-200', 'bg'=>'bg-violet-50', 'icon'=>'🔑'],
            ['label'=>'Nhân viên',    'key'=>'receptionist', 'count'=>$stats['receptionist'], 'color'=>'text-emerald-700','border'=>'border-emerald-200','bg'=>'bg-emerald-50','icon'=>'🏨'],
            ['label'=>'Trưởng phòng', 'key'=>'manager',      'count'=>$stats['manager'],      'color'=>'text-sky-700',    'border'=>'border-sky-200',    'bg'=>'bg-sky-50',    'icon'=>'👔'],
            ['label'=>'Kế toán',      'key'=>'accountant',   'count'=>$stats['accountant'],   'color'=>'text-rose-700',   'border'=>'border-rose-200',   'bg'=>'bg-rose-50',   'icon'=>'🧾'],
            ['label'=>'Khách hàng',   'key'=>'customer',     'count'=>$stats['customer'],     'color'=>'text-amber-700',  'border'=>'border-amber-200',  'bg'=>'bg-amber-50',  'icon'=>'👤'],
        ];
        @endphp
        @foreach($statCards as $card)
        <a href="{{ route('admin.permissions.index', ['role'=>$card['key']]) }}"
           class="flex items-center gap-4 rounded-2xl border {{ $card['border'] }} {{ $card['bg'] }} p-4 shadow-sm transition hover:shadow-md {{ $roleFilter===$card['key'] ? 'ring-2 ring-purple-400' : '' }}">
            <span class="text-3xl">{{ $card['icon'] }}</span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-600">{{ $card['label'] }}</p>
                <p class="mt-0.5 text-2xl font-bold {{ $card['color'] }}">{{ $card['count'] }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Lọc --}}
    <form method="GET" action="{{ route('admin.permissions.index') }}" class="mb-5 flex flex-wrap items-center gap-3">
        <input type="text" name="search" value="{{ $search }}" placeholder="Tên hoặc email..."
               class="w-64 rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"/>
        <select name="role" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">
            <option value="">Tất cả</option>
            <option value="admin"        {{ $roleFilter==='admin'        ? 'selected':'' }}>🔑 Quản trị viên</option>
            <option value="receptionist" {{ $roleFilter==='receptionist' ? 'selected':'' }}>🏨 Nhân viên</option>
            <option value="manager"      {{ $roleFilter==='manager'      ? 'selected':'' }}>👔 Trưởng phòng</option>
            <option value="accountant"   {{ $roleFilter==='accountant'   ? 'selected':'' }}>🧾 Kế toán</option>
            <option value="customer"     {{ $roleFilter==='customer'     ? 'selected':'' }}>👤 Khách hàng</option>
        </select>
        <button type="submit" class="rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-purple-700">Lọc</button>
        @if($search || $roleFilter)
            <a href="{{ route('admin.permissions.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">✕ Xóa lọc</a>
        @endif
        <span class="ml-auto text-xs text-gray-600">{{ $users->total() }} người dùng</span>
    </form>

    {{-- Group permissions theo nhóm --}}
    @php
    $permGroups = [
        'admin'     => ['label' => 'Chức năng Admin',    'color' => 'text-violet-700'],
        'reception' => ['label' => 'Chức năng Lễ tân',   'color' => 'text-emerald-700'],
        'settings'  => ['label' => 'Cài đặt & Quản lý',  'color' => 'text-sky-700'],
    ];
    $permsByGroup = collect($allPerms)->groupBy('group');
    @endphp

    {{-- Danh sách users --}}
    <div class="space-y-4">
        @forelse($users as $u)
        @php
            $isSelf    = $u->id === auth()->id();
            $isStaff   = !$u->isCustomer();
            $effective = $u->effectivePermissions();
            $isCustom  = $u->hasCustomPermissions();
            $roleColors = [
                'admin'        => 'bg-violet-100 text-violet-800 ring-violet-200',
                'receptionist' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                'manager'      => 'bg-sky-100 text-sky-800 ring-sky-200',
                'accountant'   => 'bg-rose-100 text-rose-800 ring-rose-200',
                'customer'     => 'bg-amber-100 text-amber-800 ring-amber-200',
            ];
            $roleLabels = \App\Models\User::roleLabels();
            $rc = $roleColors[$u->role] ?? 'bg-gray-100 text-gray-800 ring-gray-200';
        @endphp

        <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden"
             x-data="{ expanded: false }">

            {{-- Header row --}}
            <div class="flex flex-wrap items-center gap-3 px-5 py-4">
                {{-- Avatar + info --}}
                <div class="flex items-center gap-3 min-w-0 flex-1">
                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-purple-100 text-sm font-bold text-purple-800">
                        {{ strtoupper(mb_substr($u->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="font-semibold text-gray-900 truncate">
                            {{ $u->name }}
                            @if($isSelf) <span class="ml-1 rounded-full bg-gray-200 px-1.5 py-0.5 text-[10px] font-medium text-gray-700">bạn</span> @endif
                            @if($isCustom && $isStaff) <span class="ml-1 rounded-full bg-purple-100 px-1.5 py-0.5 text-[10px] font-medium text-purple-800">Tùy chỉnh</span> @endif
                        </p>
                        <p class="text-xs text-gray-600 truncate">{{ $u->email }}</p>
                    </div>
                </div>

                {{-- Role badge + đổi role nhanh --}}
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-semibold ring-1 {{ $rc }}">
                        {{ $roleLabels[$u->role] ?? $u->role }}
                    </span>

                    @if(!$isSelf)
                    <form method="POST" action="{{ route('admin.permissions.update-role', $u) }}"
                          x-data
                          @submit.prevent="if(confirm('Đổi role của {{ addslashes($u->name) }}? Quyền tùy chỉnh sẽ bị reset.')) $el.submit()">
                        @csrf @method('PATCH')
                        <div class="flex items-center gap-1">
                            <select name="role" class="rounded-lg border border-gray-300 bg-white px-2 py-1 text-xs text-gray-900 shadow-sm">
                                <option value="admin"        {{ $u->role==='admin'        ? 'selected':'' }}>🔑 Quản trị viên</option>
                                <option value="receptionist" {{ $u->role==='receptionist' ? 'selected':'' }}>🏨 Nhân viên</option>
                                <option value="manager"      {{ $u->role==='manager'      ? 'selected':'' }}>👔 Trưởng phòng</option>
                                <option value="accountant"   {{ $u->role==='accountant'   ? 'selected':'' }}>🧾 Kế toán</option>
                                <option value="customer"     {{ $u->role==='customer'     ? 'selected':'' }}>👤 Khách hàng</option>
                            </select>
                            <button type="submit" class="rounded-lg bg-gray-800 px-2 py-1 text-xs font-medium text-white hover:bg-gray-900">Đổi</button>
                        </div>
                    </form>
                    @endif
                </div>

                {{-- Nút mở rộng (chỉ với staff) --}}
                @if($isStaff && !$isSelf)
                <button type="button" @click="expanded = !expanded"
                    class="ml-auto flex items-center gap-1.5 rounded-xl border border-gray-200 bg-gray-50 px-3 py-1.5 text-xs font-semibold text-gray-800 transition hover:bg-gray-100"
                    :class="expanded ? 'border-purple-300 bg-purple-50 text-purple-800' : ''">
                    <span x-text="expanded ? 'Ẩn quyền' : 'Chỉnh quyền'"></span>
                    <svg class="h-3.5 w-3.5 transition" :class="expanded ? 'rotate-180' : ''" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
                    </svg>
                </button>
                @endif
            </div>

            {{-- Permission panel (mở rộng) --}}
            @if($isStaff && !$isSelf)
            <div x-show="expanded" x-collapse class="border-t border-gray-200 bg-gray-50/80">
                <form method="POST" action="{{ route('admin.permissions.update-perms', $u) }}" class="p-5">
                    @csrf @method('PATCH')

                    <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-gray-900">Chọn chức năng được phép truy cập:</p>
                        <div class="flex flex-wrap gap-2">
                            <button type="button" onclick="toggleAll(this, true)"
                                class="rounded-lg bg-purple-100 px-3 py-1 text-xs font-medium text-purple-800 hover:bg-purple-200">✓ Chọn tất cả</button>
                            <button type="button" onclick="toggleAll(this, false)"
                                class="rounded-lg bg-gray-200 px-3 py-1 text-xs font-medium text-gray-800 hover:bg-gray-300">✗ Bỏ chọn tất cả</button>
                            @if($isCustom)
                            <form method="POST" action="{{ route('admin.permissions.reset', $u) }}" class="inline"
                                  x-data @submit.prevent="if(confirm('Reset về mặc định của role?')) $el.submit()">
                                @csrf @method('DELETE')
                                <button type="submit" class="rounded-lg bg-amber-100 px-3 py-1 text-xs font-medium text-amber-900 hover:bg-amber-200">↺ Reset mặc định</button>
                            </form>
                            @endif
                        </div>
                    </div>

                    @foreach($permGroups as $groupKey => $group)
                    @if($permsByGroup->has($groupKey))
                    <div class="mb-4">
                        <p class="mb-2 text-xs font-bold uppercase tracking-wider {{ $group['color'] }}">{{ $group['label'] }}</p>
                        <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach($permsByGroup[$groupKey] as $permKey => $permInfo)
                            <label class="flex cursor-pointer items-center gap-2.5 rounded-xl border border-gray-200 bg-white px-3 py-2.5 shadow-sm transition hover:border-purple-300 hover:bg-purple-50/50 has-[:checked]:border-purple-400 has-[:checked]:bg-purple-50">
                                <input type="checkbox" name="perms[]" value="{{ $permKey }}"
                                       class="perm-check h-4 w-4 rounded border-gray-400 bg-white text-purple-600 focus:ring-purple-300"
                                       {{ in_array($permKey, $effective) ? 'checked' : '' }}>
                                <span class="text-sm">{{ $permInfo['icon'] }}</span>
                                <span class="text-xs font-medium text-gray-800">{{ $permInfo['label'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                    @endforeach

                    <div class="mt-4 flex items-center gap-3">
                        <button type="submit"
                            class="rounded-xl bg-purple-600 px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-purple-700">
                            💾 Lưu quyền
                        </button>
                        <button type="button" @click="expanded = false" class="text-sm font-medium text-gray-600 hover:text-gray-900">Đóng</button>
                    </div>
                </form>
            </div>
            @endif
        </div>
        @empty
            <div class="rounded-2xl border border-gray-200 bg-white px-4 py-10 text-center text-sm text-gray-600 shadow-sm">
                Không tìm thấy người dùng nào.
            </div>
        @endforelse
    </div>

    @if($users->hasPages())
        <div class="mt-6">{{ $users->links() }}</div>
    @endif

    <script>
    function toggleAll(btn, check) {
        const form = btn.closest('form');
        form.querySelectorAll('.perm-check').forEach(cb => cb.checked = check);
    }
    </script>
</x-hotel-layout>
