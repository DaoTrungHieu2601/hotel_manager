<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Họ tên') }}</label>
    <input name="name" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" value="{{ old('name', $user?->name) }}" required />
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Email') }}</label>
    <input type="email" name="email" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" value="{{ old('email', $user?->email) }}" required />
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Điện thoại') }}</label>
    <input name="phone" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900 placeholder:text-slate-400" value="{{ old('phone', $user?->phone) }}" />
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Vai trò') }}</label>
    <select name="role" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" required>
        <option value="{{ \App\Models\User::ROLE_RECEPTIONIST }}" @selected(old('role', $user?->role) === \App\Models\User::ROLE_RECEPTIONIST)>🏨 {{ __('Nhân viên') }}</option>
        <option value="{{ \App\Models\User::ROLE_MANAGER }}"      @selected(old('role', $user?->role) === \App\Models\User::ROLE_MANAGER)>👔 {{ __('Trưởng phòng') }}</option>
        <option value="{{ \App\Models\User::ROLE_ACCOUNTANT }}"   @selected(old('role', $user?->role) === \App\Models\User::ROLE_ACCOUNTANT)>🧾 {{ __('Kế toán') }}</option>
        <option value="{{ \App\Models\User::ROLE_ADMIN }}"        @selected(old('role', $user?->role) === \App\Models\User::ROLE_ADMIN)>🔑 Admin</option>
    </select>
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Mật khẩu') }} {{ $user ? __('(để trống nếu giữ)') : '' }}</label>
    <input type="password" name="password" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" {{ $user ? '' : 'required' }} autocomplete="new-password" />
</div>
<div>
    <label class="block text-xs font-semibold text-slate-700">{{ __('Xác nhận mật khẩu') }}</label>
    <input type="password" name="password_confirmation" class="mt-1 w-full rounded-xl border-slate-300 bg-white text-slate-900" autocomplete="new-password" />
</div>
