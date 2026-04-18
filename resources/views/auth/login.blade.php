<x-auth-split-layout>
    <div class="mb-8 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Đăng nhập') }}</h2>
        <p class="mt-2 text-sm text-gray-500">{{ __('Nhập email và mật khẩu để vào hệ thống') }}</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                </span>
                <x-text-input
                    id="email"
                    class="block w-full rounded-xl border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5">
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <div class="relative mt-1">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 00-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </span>
                <x-text-input
                    id="password"
                    class="block w-full rounded-xl border-gray-300 pl-10 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-5 flex items-center justify-between gap-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ms-2 text-sm text-gray-600">{{ __('Ghi nhớ đăng nhập') }}</span>
            </label>
            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 hover:text-indigo-800 whitespace-nowrap" href="{{ route('password.request') }}">
                    {{ __('Quên mật khẩu?') }}
                </a>
            @endif
        </div>

        <div class="mt-8">
            <button
                type="submit"
                class="inline-flex w-full justify-center items-center px-4 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-semibold shadow-lg shadow-indigo-500/25 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                {{ __('Đăng nhập') }}
            </button>
        </div>
    </form>

    @if (Route::has('register'))
        <p class="mt-10 text-center text-sm text-gray-600">
            {{ __('Chưa có tài khoản?') }}
            <a href="{{ route('register') }}" class="font-semibold text-indigo-600 hover:text-violet-700">{{ __('Đăng ký khách') }}</a>
        </p>
    @endif
</x-auth-split-layout>
