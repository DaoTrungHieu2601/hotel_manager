<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ ($title ?? null) !== null && ($title ?? '') !== '' ? $title.' — ' : '' }}{{ $displaySiteName }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>@keyframes spin{from{transform:rotate(0)}to{transform:rotate(360deg)}}</style>
</head>
<body class="guest-theme min-h-screen bg-white font-sans text-slate-900 antialiased">
    <div class="relative min-h-screen overflow-x-hidden overflow-y-visible">
        <div class="pointer-events-none absolute inset-x-0 top-0 h-72 bg-gradient-to-r from-slate-100 via-white to-slate-100"></div>
        <div class="pointer-events-none absolute -left-24 top-20 h-72 w-72 rounded-full bg-blue-100/80 blur-3xl"></div>
        <div class="pointer-events-none absolute -right-16 top-24 h-72 w-72 rounded-full bg-amber-100/80 blur-3xl"></div>

        <div class="relative z-10 flex min-h-screen flex-col">
            {{-- Header: nền đen / gần đen, chữ sáng (đúng yêu cầu) --}}
            <header class="sticky top-0 z-40 border-b border-white/10 bg-neutral-950 text-white shadow-md">
                <div class="mx-auto flex max-w-7xl flex-wrap items-center gap-3 px-4 py-3 sm:flex-nowrap sm:px-6 lg:px-8">

                    {{-- Logo + icon khách sạn --}}
                    <div class="flex min-w-0 shrink-0 items-center gap-3">
                        <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 rounded-lg outline-none ring-0 transition hover:bg-white/5 focus-visible:ring-2 focus-visible:ring-amber-400/80" aria-label="{{ $displaySiteName }}">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl shadow-lg ring-1 ring-white/15" style="background: linear-gradient(145deg, #d97706, #92400e);" aria-hidden="true">
                                <svg style="width:22px;height:22px;flex-shrink:0" class="text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" />
                                </svg>
                            </span>
                            <span class="min-w-0">
                                <span class="font-display block text-lg font-bold leading-tight tracking-tight text-white">{{ $displaySiteName }}</span>
                                @if ($siteTagline)
                                    <span class="mt-0.5 block max-w-[12rem] truncate text-xs text-slate-400">{{ $siteTagline }}</span>
                                @endif
                            </span>
                        </a>
                    </div>

                    {{-- Search: icon size in px so it never blows up if CSS is stale --}}
                    <div class="order-3 w-full min-w-0 sm:order-none sm:mx-auto sm:max-w-md sm:flex-1"
                         x-data="{
                            q: '',
                            results: [],
                            loading: false,
                            open: false,
                            timer: null,
                            async search() {
                                const val = this.q.trim();
                                if (val.length < 1) { this.results = []; this.open = false; return; }
                                clearTimeout(this.timer);
                                this.timer = setTimeout(async () => {
                                    this.loading = true;
                                    try {
                                        const r = await fetch(@js(route('guest.search.suggest')) + '?q=' + encodeURIComponent(val), {
                                            headers: { 'Accept': 'application/json' }
                                        });
                                        const d = await r.json();
                                        this.results = d.results || [];
                                        this.open = this.results.length > 0;
                                    } catch { this.results = []; }
                                    this.loading = false;
                                }, 280);
                            },
                            go(url) { this.open = false; this.q = ''; window.location.href = url; }
                         }"
                         @keydown.escape="open = false"
                         @click.outside="open = false">

                        <div class="relative w-full">
                            <div class="flex h-10 items-center gap-2 rounded-full border border-white/15 bg-white/10 px-3 transition focus-within:border-amber-400/50 focus-within:bg-white/[0.14] focus-within:ring-2 focus-within:ring-amber-400/25">
                                <svg style="width:16px;height:16px;flex-shrink:0" class="text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                                </svg>
                                <input type="text" x-model="q" @input="search()" @focus="q.trim() && (open = results.length > 0)"
                                       placeholder="Tìm phòng, dịch vụ, trang..."
                                       class="min-w-0 flex-1 border-0 bg-transparent text-sm text-white placeholder:text-slate-400 outline-none"
                                       autocomplete="off"/>
                                <button type="button" x-show="q" @click="q='';results=[];open=false" class="shrink-0 text-slate-400 transition hover:text-white" aria-label="Xóa">
                                    <svg style="width:14px;height:14px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                                <svg x-show="loading" style="width:16px;height:16px;flex-shrink:0;animation:spin 1s linear infinite" class="text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 100 16 8 8 0 01-8-8z"/>
                                </svg>
                            </div>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 class="absolute left-0 right-0 top-[calc(100%+8px)] z-50 overflow-hidden rounded-2xl border border-white/10 bg-neutral-900 shadow-2xl shadow-black/50">

                                <template x-for="(item, idx) in results" :key="idx">
                                    <a @click.prevent="go(item.url)" :href="item.url"
                                       class="flex cursor-pointer items-center gap-3 border-b border-white/5 px-4 py-3 transition last:border-0 hover:bg-white/5">
                                        <span class="shrink-0 text-lg" x-text="item.icon"></span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate text-sm font-medium text-slate-100" x-text="item.label"></p>
                                            <p class="truncate text-xs text-slate-500" x-text="item.sub"></p>
                                        </div>
                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-[10px] font-semibold text-white"
                                              :style="'background:' + item.badge_color + '44; color:' + item.badge_color"
                                              x-text="item.badge"></span>
                                    </a>
                                </template>

                                <div class="px-4 py-2.5 text-center text-xs text-slate-500">
                                    Nhấn Enter hoặc click để xem kết quả
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Nav: chữ sáng trên nền đen --}}
                    <nav class="ml-auto flex shrink-0 flex-wrap items-center justify-end gap-3 text-sm font-medium">
                        <a href="{{ route('guest.search-rooms') }}"
                           class="whitespace-nowrap text-slate-300 transition hover:text-amber-200 {{ request()->routeIs('guest.search-rooms') ? 'font-semibold text-amber-200' : '' }}">
                            {{ __('Tìm phòng') }}
                        </a>
                        @auth
                            @if(auth()->user()->isCustomer())
                                <a href="{{ route('my-bookings') }}"
                                   class="whitespace-nowrap text-slate-300 transition hover:text-amber-200">
                                    {{ __('Đơn của tôi') }}
                                </a>
                            @endif
                            <a href="{{ route('dashboard') }}"
                               class="inline-flex items-center justify-center rounded-full px-[18px] py-2 text-sm font-semibold text-white shadow-lg transition hover:opacity-90"
                               style="background: linear-gradient(to right, #92400e, #78350f); box-shadow: 0 4px 14px rgba(120,53,15,.45);">
                                {{ __('Bảng điều khiển') }}
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                               class="whitespace-nowrap text-slate-300 transition hover:text-amber-200">
                                {{ __('Đăng nhập') }}
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}"
                                   class="inline-flex items-center justify-center rounded-full px-[18px] py-2 text-sm font-semibold text-white shadow-lg transition hover:opacity-90"
                                   style="background: linear-gradient(to right, #92400e, #78350f); box-shadow: 0 4px 14px rgba(120,53,15,.45);">
                                    {{ __('Đăng ký') }}
                                </a>
                            @endif
                        @endauth
                    </nav>
                </div>
            </header>
            @php
                $guestPageBgUrl = null;
                if (request()->routeIs('guest.search-rooms', 'guest.search') && $siteSetting->bg_search_path) {
                    $guestPageBgUrl = asset('storage/'.$siteSetting->bg_search_path);
                }
            @endphp
            <main class="relative flex-1 overflow-x-hidden overflow-y-visible">
                @if ($guestPageBgUrl)
                    <div class="pointer-events-none absolute inset-0 bg-cover bg-center opacity-[0.22]" style="background-image: url('{{ $guestPageBgUrl }}')"></div>
                @endif
                <div class="relative z-10 overflow-x-hidden overflow-y-visible">
                    {{ $slot }}
                </div>
            </main>
            <footer class="relative z-10 mt-auto border-t border-white/10 bg-slate-950/95 py-12 text-sm text-slate-400">
                <div class="mx-auto grid max-w-7xl gap-8 px-4 sm:px-6 lg:grid-cols-4 lg:px-8">
                    <div>
                        <h3 class="font-display text-lg font-semibold text-white">EAUT HOTEL</h3>
                        <p class="mt-3 leading-relaxed text-slate-400">Nơi mang đến không gian lưu trú hiện đại, tiện nghi và dịch vụ tận tâm ngay giữa lòng đô thị.</p>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">Thông Tin Liên Hệ</h4>
                        <ul class="mt-3 space-y-2">
                            <li>Địa chỉ: Số 127 Đường Trung Tâm, Quận Nội Thành, TP Lớn</li>
                            <li>Hotline: 1900 888 127</li>
                            <li>Email: support@eauthotel.vn</li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">Chính Sách & Hỗ Trợ</h4>
                        <ul class="mt-3 space-y-2">
                            <li><a href="#" class="transition hover:text-amber-300">Điều khoản sử dụng</a></li>
                            <li><a href="#" class="transition hover:text-amber-300">Chính sách bảo mật</a></li>
                            <li><a href="#" class="transition hover:text-amber-300">Quy định hoàn/hủy phòng</a></li>
                            <li><a href="#" class="transition hover:text-amber-300">Hướng dẫn đặt phòng</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-white">Phương Thức Thanh Toán</h4>
                        <p class="mt-3 leading-relaxed">Chúng tôi chấp nhận: Thẻ tín dụng (Visa/Mastercard), Chuyển khoản ngân hàng, và thanh toán nội địa an toàn, nhanh chóng qua cổng VNPAY.</p>
                    </div>
                </div>
                <div class="mx-auto mt-10 max-w-7xl border-t border-white/10 px-4 pt-5 text-xs text-slate-500 sm:px-6 lg:px-8">
                    &copy; {{ date('Y') }} {{ $displaySiteName }}
                </div>
            </footer>
        </div>
    </div>
    <x-social-buttons />

    @php $chatbotSetting = \App\Models\SiteSetting::instance(); @endphp
    @if($chatbotSetting->chatbot_enabled)
        <x-ai-chatbot-widget />
    @endif

    @stack('scripts')
</body>
</html>
