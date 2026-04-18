<x-auth-split-layout>
    <div class="mb-8 text-center lg:text-left">
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">{{ __('Đăng ký khách hàng') }}</h2>
        <p class="mt-2 text-sm text-gray-500">{{ __('Tạo tài khoản để đặt phòng trực tuyến và xem lịch sử đặt phòng.') }}</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" :value="__('Họ và tên')" />
            <x-text-input id="name" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="phone" :value="__('Điện thoại (tùy chọn)')" />
            <x-text-input id="phone" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="text" name="phone" :value="old('phone')" autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Mật khẩu')" />
            <x-text-input id="password" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Xác nhận mật khẩu')" />
            <x-text-input id="password_confirmation" class="mt-1 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-8">
            <button
                type="submit"
                class="inline-flex w-full justify-center items-center px-4 py-3.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-semibold shadow-lg shadow-indigo-500/25 transition focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
            >
                {{ __('Đăng ký') }}
            </button>
        </div>

        <p class="mt-8 text-center text-sm text-gray-600">
            <a class="font-semibold text-indigo-600 hover:text-violet-700" href="{{ route('login') }}">{{ __('Đã có tài khoản? Đăng nhập') }}</a>
        </p>
        <p class="mt-3 text-center text-sm text-gray-500">
            <a class="hover:text-gray-800" href="{{ route('home') }}">{{ __('← Về trang chủ') }}</a>
        </p>
    </form>
</x-auth-split-layout>
