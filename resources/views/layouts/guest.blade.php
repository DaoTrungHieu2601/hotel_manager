<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=roboto:400,500,600,700|rubik:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased min-h-screen relative overflow-x-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600 via-violet-600 to-fuchsia-500"></div>
        <div class="absolute inset-0 opacity-30 pointer-events-none" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.15\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl -translate-y-1/2 translate-x-1/3 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-fuchsia-400/20 rounded-full blur-3xl translate-y-1/2 -translate-x-1/4 pointer-events-none"></div>

        <div class="relative min-h-screen flex flex-col sm:justify-center items-center px-4 py-10 sm:py-12">
            <div class="w-full max-w-md">
                <div class="text-center mb-8">
                    <a href="{{ url('/') }}" class="inline-flex flex-col items-center gap-2 group">
                        <span class="inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-white/20 backdrop-blur shadow-lg ring-2 ring-white/30 group-hover:bg-white/30 transition">
                            <x-application-logo class="h-10 w-auto fill-current text-white" />
                        </span>
                        <span class="text-2xl font-bold text-white tracking-tight drop-shadow-md">{{ config('app.name') }}</span>
                        <span class="text-sm font-medium text-indigo-100">{{ __('Quản lý nhân sự nội bộ') }}</span>
                    </a>
                </div>

                <div class="rounded-2xl bg-white/95 backdrop-blur-sm shadow-2xl shadow-indigo-900/20 ring-1 ring-white/50 p-8 sm:p-10">
                    {{ $slot }}
                </div>

                <p class="mt-8 text-center text-xs text-indigo-100/90">
                    {{ __('Laravel') }} · Breeze · MySQL
                </p>
            </div>
        </div>
    </body>
</html>
