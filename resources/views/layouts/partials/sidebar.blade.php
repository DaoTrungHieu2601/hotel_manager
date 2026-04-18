@php
    $isAdmin = auth()->user()->isAdmin();
@endphp

<aside class="w-60 shrink-0 bg-gradient-to-b from-slate-900 via-indigo-950 to-slate-900 text-slate-100 min-h-[calc(100vh-3.5rem)] border-r border-white/5 shadow-xl">
    <nav class="p-4 space-y-1">
        @if ($isAdmin)
            <p class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-emerald-400/90">{{ __('Khu vực Admin') }}</p>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.dashboard') ? 'bg-gradient-to-r from-emerald-500/25 to-teal-500/20 text-white border-l-4 border-emerald-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">📊</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.employees.*') ? 'bg-gradient-to-r from-emerald-500/25 to-teal-500/20 text-white border-l-4 border-emerald-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">👥</span> {{ __('Nhân viên') }}
            </a>
            <a href="{{ route('admin.leaves.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.leaves.*') ? 'bg-gradient-to-r from-emerald-500/25 to-teal-500/20 text-white border-l-4 border-emerald-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">📋</span> {{ __('Đơn nghỉ phép') }}
            </a>
            <a href="{{ route('admin.payroll.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('admin.payroll.*') ? 'bg-gradient-to-r from-emerald-500/25 to-teal-500/20 text-white border-l-4 border-emerald-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">💰</span> {{ __('Bảng lương') }}
            </a>
        @else
            <p class="px-3 py-2 text-xs font-bold uppercase tracking-wider text-sky-400/90">{{ __('Nhân viên') }}</p>
            <a href="{{ route('employee.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('employee.dashboard') ? 'bg-gradient-to-r from-sky-500/25 to-indigo-500/20 text-white border-l-4 border-sky-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">🏠</span> {{ __('Dashboard') }}
            </a>
            <a href="{{ route('employee.attendance') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('employee.attendance*') ? 'bg-gradient-to-r from-sky-500/25 to-indigo-500/20 text-white border-l-4 border-sky-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">⏱️</span> {{ __('Chấm công') }}
            </a>
            <a href="{{ route('employee.leaves.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('employee.leaves.index') ? 'bg-gradient-to-r from-sky-500/25 to-indigo-500/20 text-white border-l-4 border-sky-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">📝</span> {{ __('Đơn của tôi') }}
            </a>
            <a href="{{ route('employee.leaves.create') }}" class="flex items-center gap-2 rounded-xl px-3 py-2.5 text-sm font-medium transition {{ request()->routeIs('employee.leaves.create') ? 'bg-gradient-to-r from-sky-500/25 to-indigo-500/20 text-white border-l-4 border-sky-400 pl-2 shadow-inner' : 'text-slate-200 hover:bg-white/10 hover:text-white' }}">
                <span class="text-lg" aria-hidden="true">✈️</span> {{ __('Xin nghỉ phép') }}
            </a>
        @endif
    </nav>
</aside>
