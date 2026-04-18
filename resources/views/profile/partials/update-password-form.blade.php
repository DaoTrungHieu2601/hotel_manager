<section>
    @php($isReceptionist = auth()->user()?->isReceptionist())
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ $isReceptionist ? __('Yêu cầu đổi mật khẩu') : __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ $isReceptionist
                ? __('Lễ tân không thể tự đổi mật khẩu. Vui lòng gửi yêu cầu để admin phê duyệt.')
                : __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ $isReceptionist ? route('profile.password-change-request.store') : route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @unless($isReceptionist)
            @method('put')
        @endunless

        <div>
            <x-input-label for="update_password_current_password" :value="__('Current Password')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('New Password')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ $isReceptionist ? __('Gửi yêu cầu') : __('Save') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600"
                >{{ __('Saved.') }}</p>
            @endif

            @if (session('status') === 'password-change-requested')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2500)"
                    class="text-sm text-emerald-600"
                >{{ __('Đã gửi yêu cầu đổi mật khẩu. Vui lòng chờ admin phê duyệt.') }}</p>
            @endif
        </div>
    </form>
</section>
