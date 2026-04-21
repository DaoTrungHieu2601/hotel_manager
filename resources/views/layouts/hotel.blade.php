<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title !== '' ? $title.' — ' : '' }}{{ $displaySiteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme min-h-screen bg-slate-50 font-sans text-gray-900 antialiased">
    @php
        $user = auth()->user();
        $navRow = 'group/nav flex w-full items-center gap-3 rounded-lg px-3 py-2 text-sm transition';
        $navOn = 'bg-purple-100 font-medium text-purple-700';
        $navOff = 'font-normal text-gray-600 hover:bg-purple-50 hover:text-purple-700';
        $navClass = fn (bool $active) => $navRow.' '.($active ? $navOn : $navOff);
        $iconLg = fn (bool $active) => 'h-5 w-5 shrink-0 '.($active ? 'text-purple-700' : 'text-gray-400 group-hover/nav:text-purple-600');
        $subRow = 'group/nav flex w-full items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition';
        $subNavClass = fn (bool $active) => $subRow.' '.($active ? $navOn : $navOff);
        $iconSm = fn (bool $active) => 'h-4 w-4 shrink-0 '.($active ? 'text-purple-700' : 'text-gray-400 group-hover/nav:text-purple-600');
    @endphp
    {{-- SaaS-style depth: light page tint + soft top highlight --}}
    <div class="pointer-events-none fixed inset-x-0 top-0 z-0 h-72 bg-gradient-to-b from-white/80 via-slate-50/50 to-transparent"></div>

    <div class="relative z-10 flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="hidden w-72 shrink-0 flex-col border-r border-gray-200/80 bg-white shadow-sm lg:sticky lg:top-0 lg:flex lg:h-screen lg:overflow-y-auto">
            <div class="border-b border-gray-100 px-5 py-5">
                <a href="{{ route('home') }}" class="flex items-start gap-3 rounded-lg outline-none ring-purple-400/0 transition hover:bg-slate-50 focus-visible:ring-2 focus-visible:ring-purple-400">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl text-white shadow-md ring-1 ring-black/10" style="background: linear-gradient(145deg, #7c3aed, #5b21b6);" aria-hidden="true">
                        <svg style="width:24px;height:24px;flex-shrink:0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                        </svg>
                    </span>
                    <span class="min-w-0 flex-1 pt-0.5">
                        <span class="font-display block text-lg font-bold leading-snug tracking-tight text-gray-900">{{ $displaySiteName }}</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-gray-500">{{ $siteTagline ?: __('Hotel Management Platform') }}</span>
                    </span>
                </a>
            </div>

            <nav class="flex-1 space-y-0.5 px-3 py-3">
                @if($user->isAdmin() || $user->isReceptionist())
                    @if($user->hasPermission(\App\Models\User::PERM_ADMIN_DASHBOARD))
                        @php $a = request()->routeIs('admin.dashboard'); @endphp
                        <a href="{{ route('admin.dashboard') }}" class="{{ $navClass($a) }}">
                            <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg>
                            <span>{{ __('Báo cáo Admin') }}</span>
                        </a>
                    @endif
                    @if($user->hasPermission(\App\Models\User::PERM_RESERVATIONS))
                        @php $a = request()->routeIs('reception.reservations.*', 'admin.bookings.*'); @endphp
                        <a href="{{ route('reception.reservations.index') }}" class="{{ $navClass($a) }}">
                            <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" /></svg>
                            <span>{{ __('Đơn đặt phòng') }}</span>
                        </a>
                    @endif
                    @if($user->hasPermission(\App\Models\User::PERM_MESSAGES))
                        @php $a = request()->routeIs('admin.messages.*'); @endphp
                        <a href="{{ route('admin.messages.index') }}" class="{{ $navClass($a) }}">
                            <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg>
                            <span class="min-w-0 flex-1 text-left">{{ __('Tin nhắn khách') }}</span>
                            @if (($adminUnreadChatCount ?? 0) > 0)
                                <span class="inline-flex min-w-[1.25rem] shrink-0 items-center justify-center rounded-full bg-purple-200 px-1 py-0.5 text-[10px] font-bold text-purple-900">{{ $adminUnreadChatCount }}</span>
                            @endif
                        </a>
                    @endif
                    @php
                        $hasAccountMgmt = $user->hasPermission(\App\Models\User::PERM_PASSWORD_REQUESTS)
                            || $user->hasPermission(\App\Models\User::PERM_PERMISSIONS);
                        $accountMgmtOpen = request()->routeIs('admin.password-change-requests.*', 'admin.permissions.*');
                    @endphp
                    @if($hasAccountMgmt)
                        <details class="group" @if ($accountMgmtOpen) open @endif>
                            @php $accSumActive = $accountMgmtOpen; @endphp
                            <summary class="{{ $navClass($accSumActive) }} list-none cursor-pointer marker:content-none [&::-webkit-details-marker]:hidden">
                                <svg class="{{ $iconLg($accSumActive) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                <span class="min-w-0 flex-1 text-left font-medium">{{ __('Quản lý tài khoản') }}</span>
                                <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-open:rotate-180 group-open:text-purple-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                            </summary>
                            <div class="ml-4 mt-0.5 space-y-0.5 border-l border-gray-200 py-1 pl-3">
                                @if($user->hasPermission(\App\Models\User::PERM_PASSWORD_REQUESTS))
                                    @php $a = request()->routeIs('admin.password-change-requests.*'); @endphp
                                    <a href="{{ route('admin.password-change-requests.index') }}" class="{{ $subNavClass($a) }}">
                                        <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z" /></svg>
                                        <span>{{ __('Yêu cầu đổi mật khẩu') }}</span>
                                    </a>
                                @endif
                                @if($user->hasPermission(\App\Models\User::PERM_PERMISSIONS))
                                    @php $a = request()->routeIs('admin.permissions.*'); @endphp
                                    <a href="{{ route('admin.permissions.index') }}" class="{{ $subNavClass($a) }}">
                                        <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg>
                                        <span>{{ __('Phân quyền') }}</span>
                                    </a>
                                @endif
                            </div>
                        </details>
                    @endif

                    @php
                        $hasSettings = $user->hasPermission(\App\Models\User::PERM_SITE_SETTINGS)
                            || $user->hasPermission(\App\Models\User::PERM_ROOM_TYPES)
                            || $user->hasPermission(\App\Models\User::PERM_HOTEL_ROOMS)
                            || $user->hasPermission(\App\Models\User::PERM_SERVICES)
                            || $user->hasPermission(\App\Models\User::PERM_STAFF_MANAGEMENT)
                            || $user->hasPermission(\App\Models\User::PERM_CUSTOMERS);
                        $adminSettingsOpen = request()->routeIs('admin.settings*', 'admin.room-types.*', 'admin.hotel-rooms.*', 'admin.services.*', 'admin.staff.*', 'admin.customers.*');
                    @endphp
                    @if($hasSettings)
                        <details class="group" @if ($adminSettingsOpen) open @endif>
                        @php $sumActive = $adminSettingsOpen; @endphp
                        <summary class="{{ $navClass($sumActive) }} list-none cursor-pointer marker:content-none [&::-webkit-details-marker]:hidden">
                            <svg class="{{ $iconLg($sumActive) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span class="min-w-0 flex-1 text-left font-medium">{{ __('Cài đặt website') }}</span>
                            <svg class="h-4 w-4 shrink-0 text-gray-400 transition group-open:rotate-180 group-open:text-purple-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.94a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" /></svg>
                        </summary>
                        <div class="ml-4 mt-0.5 space-y-0.5 border-l border-gray-200 py-1 pl-3">
                            @if($user->hasPermission(\App\Models\User::PERM_SITE_SETTINGS))
                                @php $a = request()->routeIs('admin.settings*'); @endphp
                                <a href="{{ route('admin.settings.edit') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3A1.5 1.5 0 001.5 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                    <span>{{ __('Thông tin & hình nền') }}</span>
                                </a>
                            @endif
                            @if($user->hasPermission(\App\Models\User::PERM_ROOM_TYPES))
                                @php $a = request()->routeIs('admin.room-types.*'); @endphp
                                <a href="{{ route('admin.room-types.index') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" /><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" /></svg>
                                    <span>{{ __('Loại phòng & giá') }}</span>
                                </a>
                            @endif
                            @if($user->hasPermission(\App\Models\User::PERM_HOTEL_ROOMS))
                                @php $a = request()->routeIs('admin.hotel-rooms.*'); @endphp
                                <a href="{{ route('admin.hotel-rooms.index') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zM3.75 12h.007v.008H3.75V12zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm-.375 5.25h.007v.008H3.75v-.008zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                                    <span>{{ __('Danh sách phòng') }}</span>
                                </a>
                            @endif
                            @if($user->hasPermission(\App\Models\User::PERM_SERVICES))
                                @php $a = request()->routeIs('admin.services.*'); @endphp
                                <a href="{{ route('admin.services.index') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.741l-4.77-2.749a.563.563 0 00-.586 0l-4.77 2.749a.562.562 0 01-.84-.741l1.285-5.385a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" /></svg>
                                    <span>{{ __('Dịch vụ') }}</span>
                                </a>
                            @endif
                            @if($user->hasPermission(\App\Models\User::PERM_STAFF_MANAGEMENT))
                                @php $a = request()->routeIs('admin.staff.*'); @endphp
                                <a href="{{ route('admin.staff.index') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                    <span>{{ __('Nhân sự / Lễ tân') }}</span>
                                </a>
                            @endif
                            @if($user->hasPermission(\App\Models\User::PERM_CUSTOMERS))
                                @php $a = request()->routeIs('admin.customers.*'); @endphp
                                <a href="{{ route('admin.customers.index') }}" class="{{ $subNavClass($a) }}">
                                    <svg class="{{ $iconSm($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                    <span>{{ __('Quản lý tài khoản khách hàng') }}</span>
                                </a>
                            @endif
                        </div>
                    </details>
                    @endif
                @endif

                @if($user->isAdmin() || $user->isReceptionist())
                    @if($user->hasPermission(\App\Models\User::PERM_ROOM_MAP) || $user->hasPermission(\App\Models\User::PERM_CHECK_IN_OUT))
                    <p class="px-3 pt-4 pb-1 text-[11px] font-semibold uppercase tracking-wider text-gray-400">{{ __('Lễ tân') }}</p>
                    @if($user->hasPermission(\App\Models\User::PERM_ROOM_MAP))
                        @php $a = request()->routeIs('reception.dashboard'); @endphp
                        <a href="{{ route('reception.dashboard') }}" class="{{ $navClass($a) }}">
                            <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" /></svg>
                            <span>{{ __('Sơ đồ phòng') }}</span>
                        </a>
                    @endif
                    @if($user->isReceptionist() && $user->hasPermission(\App\Models\User::PERM_RESERVATIONS))
                        @php $a = request()->routeIs('reception.reservations.*'); @endphp
                        <a href="{{ route('reception.reservations.index') }}" class="{{ $navClass($a) }}">
                            <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" /></svg>
                            <span>{{ __('Đơn đặt phòng') }}</span>
                        </a>
                    @endif
                    @endif
                @endif

                @if($user->isCustomer())
                    @php $a = request()->routeIs('customer.bookings.*'); @endphp
                    <a href="{{ route('customer.bookings.index') }}" class="{{ $navClass($a) }}">
                        <svg class="{{ $iconLg($a) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                        <span>{{ __('Đơn của tôi') }}</span>
                    </a>
                    <a href="{{ route('guest.search-rooms') }}" class="{{ $navClass(false) }}">
                        <svg class="{{ $iconLg(false) }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" /></svg>
                        <span>{{ __('Đặt phòng mới') }}</span>
                    </a>
                @endif
            </nav>
        </aside>

            <div class="flex min-w-0 flex-1 flex-col">
                <header class="sticky top-0 z-30 border-b border-white/10 bg-neutral-950 px-4 py-3 shadow-md lg:px-8">
                    <div class="flex items-center justify-between gap-4">
                        <h1 class="font-display text-xl font-semibold text-white">{{ $header ?? __('Bảng điều khiển') }}</h1>
                        <div class="flex items-center gap-3">

                            {{-- Notification Bell --}}
                            <div
                                class="relative"
                                x-data="{ open: false }"
                                @click.outside="open = false"
                            >
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="relative flex h-8 w-8 items-center justify-center rounded-full bg-white/10 text-slate-200 ring-1 ring-white/15 transition hover:bg-white/15 hover:text-white"
                                    title="Thông báo"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                    @if(($unreadNotifCount ?? 0) > 0)
                                        <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-500 text-[9px] font-bold text-white">
                                            {{ $unreadNotifCount > 9 ? '9+' : $unreadNotifCount }}
                                        </span>
                                    @endif
                                </button>

                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                    x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                    class="absolute right-0 top-full z-50 mt-2 w-80 rounded-xl border border-white/10 bg-slate-900/95 shadow-2xl backdrop-blur-xl"
                                >
                                    {{-- Header --}}
                                    <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                                        <span class="text-sm font-semibold text-white">Thông báo</span>
                                        @if(($unreadNotifCount ?? 0) > 0)
                                            <form method="POST" action="{{ route('notifications.read-all') }}">
                                                @csrf
                                                <button type="submit" class="text-[11px] font-medium text-violet-400 transition hover:text-violet-300">
                                                    Đọc tất cả
                                                </button>
                                            </form>
                                        @endif
                                    </div>

                                    {{-- Notification list --}}
                                    <div class="max-h-80 divide-y divide-white/5 overflow-y-auto">
                                        @forelse($unreadNotifications ?? [] as $notif)
                                            @php
                                                $data = $notif->data;
                                                $iconType = $data['type'] ?? 'default';
                                                $isRead = $notif->read_at !== null;
                                            @endphp
                                            <form method="POST" action="{{ route('notifications.read', $notif->id) }}">
                                                @csrf
                                                <button type="submit" class="flex w-full items-start gap-3 px-4 py-3 text-left transition hover:bg-white/5 {{ $isRead ? 'opacity-50' : '' }}">
                                                    {{-- Icon --}}
                                                    <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                                                        @if($iconType === 'booking_pending') bg-amber-500/20 text-amber-400
                                                        @elseif($iconType === 'booking_confirmed') bg-emerald-500/20 text-emerald-400
                                                        @elseif($iconType === 'booking_cancelled') bg-rose-500/20 text-rose-400
                                                        @elseif($iconType === 'checked_in') bg-blue-500/20 text-blue-400
                                                        @elseif($iconType === 'checked_out') bg-violet-500/20 text-violet-400
                                                        @else bg-slate-700 text-slate-400
                                                        @endif
                                                    ">
                                                        @if($iconType === 'booking_pending')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                                        @elseif($iconType === 'booking_confirmed')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        @elseif($iconType === 'booking_cancelled')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        @elseif($iconType === 'checked_in')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" /></svg>
                                                        @elseif($iconType === 'checked_out')
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" /></svg>
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                        @endif
                                                    </span>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="truncate text-xs font-semibold {{ $isRead ? 'text-slate-400' : 'text-white' }}">{{ $data['title'] ?? 'Thông báo' }}</p>
                                                        <p class="mt-0.5 line-clamp-2 text-[11px] leading-relaxed text-slate-500">{{ $data['body'] ?? '' }}</p>
                                                        <p class="mt-1 text-[10px] text-slate-600">{{ $notif->created_at->diffForHumans() }}</p>
                                                    </div>
                                                    @if(!$isRead)
                                                        <span class="mt-2 h-2 w-2 shrink-0 rounded-full bg-violet-400"></span>
                                                    @else
                                                        <span class="mt-2 h-2 w-2 shrink-0"></span>
                                                    @endif
                                                </button>
                                            </form>
                                        @empty
                                            <div class="px-4 py-8 text-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2 h-8 w-8 text-slate-700" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                                <p class="text-xs text-slate-500">Chưa có thông báo nào</p>
                                            </div>
                                        @endforelse
                                    </div>

                                </div>
                            </div>

                            {{-- User menu --}}
                            <div
                                class="relative"
                                x-data="{ open: false }"
                                @mouseenter="open = true"
                                @mouseleave="open = false"
                            >
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white ring-1 ring-white/15 transition hover:bg-white/15"
                                >
                                    {{ $user->name }}
                                </button>
                                <div
                                    x-show="open"
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 -translate-y-1"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-100"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-1"
                                    class="absolute right-0 top-full z-40 mt-2 w-40 rounded-xl border border-white/10 bg-slate-900/95 p-1 shadow-xl"
                                >
                                    <a href="{{ route('profile.edit') }}" class="block rounded-lg px-3 py-2 text-xs font-medium text-slate-200 transition hover:bg-white/10">
                                        {{ __('Profile') }}
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full rounded-lg px-3 py-2 text-left text-xs font-medium text-rose-200 transition hover:bg-rose-500/20">
                                            {{ __('Logout') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <a href="{{ route('home') }}" class="text-sm font-medium text-amber-200/90 hover:text-white lg:hidden">{{ __('Website') }}</a>
                        </div>
                    </div>
                </header>

                <main class="flex-1 bg-slate-50 p-4 lg:p-8">
                    {{ $slot }}
                </main>
            </div>
        </div>
    @stack('scripts')
    @auth
        @if (auth()->user()->isCustomer())
            @php $chatbotSetting = \App\Models\SiteSetting::instance(); @endphp
            @if($chatbotSetting->chatbot_enabled)
                <x-ai-chatbot-widget />
            @endif
        @endif
    @endauth
</body>
</html>
