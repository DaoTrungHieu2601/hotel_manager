<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $displaySiteName }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700|rubik:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased text-gray-900 min-h-screen flex flex-col lg:flex-row">
        {{-- Cột trái: ẩn trên mobile --}}
        @php
            $defaultAuthBg = 'https://images.unsplash.com/photo-1522071820081-009f0129c71c?auto=format&fit=crop&w=2000&q=80';
            $usingCustomAuthBg = false;
            if (request()->routeIs('register') && $siteSetting->bg_register_path) {
                $authSideImage = asset('storage/'.$siteSetting->bg_register_path);
                $usingCustomAuthBg = true;
            } elseif (request()->routeIs('login') && $siteSetting->bg_login_path) {
                $authSideImage = asset('storage/'.$siteSetting->bg_login_path);
                $usingCustomAuthBg = true;
            } else {
                $authSideImage = $defaultAuthBg;
            }
        @endphp
        <div class="hidden lg:flex lg:w-[55%] xl:w-[50%] relative min-h-screen flex-col items-center justify-center overflow-hidden">
            <img
                src="{{ $authSideImage }}"
                alt=""
                class="absolute inset-0 w-full h-full object-cover"
                loading="lazy"
            >
            <div class="absolute inset-0 {{ $usingCustomAuthBg ? 'bg-gradient-to-br from-slate-950/45 via-violet-950/35 to-slate-950/45' : 'bg-gradient-to-br from-indigo-950/85 via-violet-900/75 to-fuchsia-900/80' }}"></div>
            @unless ($usingCustomAuthBg)
                <div class="absolute inset-0 opacity-[0.12] pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'40\' height=\'40\' viewBox=\'0 0 40 40\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M20 20h20v20H20zM0 0h20v20H0z\'/%3E%3C/g%3E%3C/svg%3E'); background-size: 40px 40px;"></div>
            @endunless

            <div class="relative z-10 flex flex-col items-center justify-center px-10 py-16 text-center max-w-lg">
                <span class="inline-flex h-20 w-20 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-md ring-2 ring-white/25 shadow-2xl mb-8">
                    <x-application-logo class="h-12 w-auto fill-current text-white" />
                </span>
                <h1 class="text-4xl xl:text-5xl font-bold text-white tracking-tight drop-shadow-sm">
                    {{ $displaySiteName }}
                </h1>
                <p class="mt-5 text-lg xl:text-xl text-indigo-100/95 font-medium leading-relaxed">
                    {{ $siteTagline ?: __('Đặt phòng · Check-in · Hóa đơn — một hệ thống') }}
                </p>
                <p class="mt-8 text-sm text-indigo-200/80 max-w-sm">
                    {{ __('Giao diện khách đặt phòng online; lễ tân xử lý phòng và dịch vụ; admin báo cáo doanh thu.') }}
                </p>
            </div>
        </div>

        {{-- Cột phải: form --}}
        <div class="flex-1 flex flex-col justify-center items-center bg-white min-h-screen px-6 py-10 sm:px-10 lg:px-12">
            <div class="w-full max-w-md">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
