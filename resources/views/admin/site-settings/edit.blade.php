<x-hotel-layout>
    <x-slot name="header">{{ __('Cài đặt trang web') }}</x-slot>

    @if (session('status'))
        <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <form
        method="post"
        action="{{ route('admin.settings.update') }}"
        enctype="multipart/form-data"
        class="max-w-3xl space-y-8 rounded-xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8"
    >
        @csrf
        @method('PUT')

        <div>
            <h2 class="font-semibold text-gray-900">{{ __('Tên & mô tả hiển thị') }}</h2>
            <div class="mt-4 space-y-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_display_name">{{ __('Tên hiển thị') }}</label>
                    <input id="site_display_name" name="site_display_name" type="text" value="{{ old('site_display_name', $setting->site_display_name) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="{{ config('app.name') }}" />
                    @error('site_display_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_tagline">{{ __('Dòng giới thiệu ngắn') }}</label>
                    <textarea id="site_tagline" name="site_tagline" rows="2" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('site_tagline', $setting->site_tagline) }}</textarea>
                    @error('site_tagline')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8">
            <h2 class="font-semibold text-gray-900">{{ __('Thông tin liên hệ (in trên hóa đơn)') }}</h2>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_address">{{ __('Địa chỉ') }}</label>
                    <input id="site_address" name="site_address" type="text" value="{{ old('site_address', $setting->site_address) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('site_address')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_phone">{{ __('Điện thoại') }}</label>
                    <input id="site_phone" name="site_phone" type="text" value="{{ old('site_phone', $setting->site_phone) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="+84 98 000 0000" />
                    @error('site_phone')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_email">{{ __('Email') }}</label>
                    <input id="site_email" name="site_email" type="email" value="{{ old('site_email', $setting->site_email) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="contact@hotel.com" />
                    @error('site_email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="site_website">{{ __('Website') }}</label>
                    <input id="site_website" name="site_website" type="text" value="{{ old('site_website', $setting->site_website) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" placeholder="www.hotel.com" />
                    @error('site_website')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8">
            <h2 class="font-semibold text-gray-900">{{ __('Chính sách nhận / trả phòng') }}</h2>
            <p class="mt-1 text-sm text-gray-600">{{ __('Khung giờ mặc định hiển thị cho khách; phụ phí theo giờ và hủy khi không đến (no-show) áp dụng trong hệ thống.') }}</p>
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="policy_check_in_start">{{ __('Check-in từ') }}</label>
                    <input id="policy_check_in_start" name="policy_check_in_start" type="time" step="60" value="{{ old('policy_check_in_start', $setting->policy_check_in_start ?? '08:00') }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('policy_check_in_start')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="policy_check_in_end">{{ __('Check-in đến') }}</label>
                    <input id="policy_check_in_end" name="policy_check_in_end" type="time" step="60" value="{{ old('policy_check_in_end', $setting->policy_check_in_end ?? '08:30') }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('policy_check_in_end')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="policy_check_out_start">{{ __('Check-out từ (ngày trả)') }}</label>
                    <input id="policy_check_out_start" name="policy_check_out_start" type="time" step="60" value="{{ old('policy_check_out_start', $setting->policy_check_out_start ?? '10:00') }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('policy_check_out_start')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="policy_check_out_end">{{ __('Check-out đến') }}</label>
                    <input id="policy_check_out_end" name="policy_check_out_end" type="time" step="60" value="{{ old('policy_check_out_end', $setting->policy_check_out_end ?? '11:00') }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('policy_check_out_end')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="no_show_cutoff_time">{{ __('Hủy no-show sau giờ (cùng ngày check-in)') }}</label>
                    <input id="no_show_cutoff_time" name="no_show_cutoff_time" type="time" step="60" value="{{ old('no_show_cutoff_time', $setting->no_show_cutoff_time ?? '23:30') }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('no_show_cutoff_time')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="check_time_grace_minutes">{{ __('Miễn phí thêm phút sau giờ dự kiến') }}</label>
                    <input id="check_time_grace_minutes" name="check_time_grace_minutes" type="number" min="0" max="240" value="{{ old('check_time_grace_minutes', $setting->check_time_grace_minutes ?? 15) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('check_time_grace_minutes')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="extra_hour_price">{{ __('Phụ phí mỗi giờ ngoài khung (VNĐ)') }}</label>
                    <input id="extra_hour_price" name="extra_hour_price" type="number" step="1000" min="0" value="{{ old('extra_hour_price', $setting->extra_hour_price ?? 100000) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    @error('extra_hour_price')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8">
            <h2 class="font-semibold text-gray-900">{{ __('Hình nền trang') }}</h2>
            @php
                $bgFields = [
                    'bg_home' => ['label' => __('Trang chủ'), 'path' => $setting->bg_home_path],
                    'bg_search' => ['label' => __('Tìm phòng'), 'path' => $setting->bg_search_path],
                    'bg_login' => ['label' => __('Đăng nhập'), 'path' => $setting->bg_login_path],
                    'bg_register' => ['label' => __('Đăng ký'), 'path' => $setting->bg_register_path],
                ];
            @endphp

            <div class="mt-6 grid gap-6 sm:grid-cols-2">
                @foreach ($bgFields as $name => $meta)
                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                        <p class="text-sm font-medium text-gray-900">{{ $meta['label'] }}</p>
                        @if ($meta['path'])
                            <div class="mt-3 overflow-hidden rounded-lg border border-gray-200">
                                <img src="{{ asset('storage/'.$meta['path']) }}" alt="" class="h-28 w-full object-cover" />
                            </div>
                            <label class="mt-3 flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" name="remove_{{ $name }}" value="1" class="rounded border-gray-300 bg-white text-purple-600 focus:ring-purple-300" />
                                {{ __('Xóa ảnh hiện tại') }}
                            </label>
                        @endif
                        <p class="mt-3 text-xs font-semibold uppercase tracking-wide text-gray-600">{{ __('Tải ảnh mới') }}</p>
                        <div class="mt-2 flex flex-wrap items-center gap-3">
                            <label class="inline-flex cursor-pointer rounded-full focus-within:outline-none focus-within:ring-2 focus-within:ring-purple-400 focus-within:ring-offset-2 focus-within:ring-offset-white">
                                <span class="inline-flex items-center rounded-full bg-violet-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm ring-1 ring-violet-500/40 transition hover:bg-violet-500 active:bg-violet-700">{{ __('Chọn tệp') }}</span>
                                <input id="{{ $name }}" name="{{ $name }}" type="file" class="sr-only" />
                            </label>
                        </div>
                        @error($name)
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                @endforeach
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8" x-data="{ showKey: false }">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-purple-100 ring-1 ring-purple-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-700" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2a2 2 0 012 2v1h1a3 3 0 013 3v1h.5a1.5 1.5 0 010 3H18v1a3 3 0 01-3 3h-1v1a2 2 0 01-4 0v-1H9a3 3 0 01-3-3v-1h-.5a1.5 1.5 0 010-3H6V8a3 3 0 013-3h1V4a2 2 0 012-2zm-2 9a1 1 0 100 2 1 1 0 000-2zm4 0a1 1 0 100 2 1 1 0 000-2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900">{{ __('Chatbot AI (Gemini)') }}</h2>
                </div>
            </div>

            <div class="mt-5 space-y-5">
                <label class="flex cursor-pointer items-center gap-3">
                    <div class="relative">
                        <input type="hidden" name="chatbot_enabled" value="0" />
                        <input
                            id="chatbot_enabled"
                            type="checkbox"
                            name="chatbot_enabled"
                            value="1"
                            @if(old('chatbot_enabled', $setting->chatbot_enabled)) checked @endif
                            class="peer sr-only"
                        />
                        <div class="h-6 w-11 rounded-full bg-slate-700 transition peer-checked:bg-violet-600 peer-focus:ring-2 peer-focus:ring-violet-500/40"></div>
                        <div class="absolute left-0.5 top-0.5 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ __('Bật chatbot AI trên website') }}</span>
                </label>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="chatbot_name">{{ __('Tên bot') }}</label>
                        <input
                            id="chatbot_name" name="chatbot_name" type="text"
                            value="{{ old('chatbot_name', $setting->chatbot_name) }}"
                            placeholder="Trợ lý AI"
                            class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="chatbot_gemini_api_key">{{ __('Gemini API Key') }}</label>
                        <div class="relative mt-1">
                            <input
                                id="chatbot_gemini_api_key" name="chatbot_gemini_api_key"
                                :type="showKey ? 'text' : 'password'"
                                value="{{ old('chatbot_gemini_api_key', $setting->chatbot_gemini_api_key) }}"
                                placeholder="{{ $setting->chatbot_gemini_api_key ? '••••••••••••••••••••' : 'AIza...' }}"
                                autocomplete="off"
                                class="w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 pr-10 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                            />
                            <button type="button" @click="showKey = !showKey" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-800">
                                <svg x-show="!showKey" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                <svg x-show="showKey" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        @error('chatbot_gemini_api_key')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">{{ __('Avatar bot') }}</p>
                    @if($setting->chatbot_avatar_path)
                        <div class="mt-3 flex items-center gap-3">
                            <img src="{{ asset('storage/'.$setting->chatbot_avatar_path) }}" alt="Bot avatar" class="h-16 w-16 rounded-full object-cover ring-2 ring-violet-500/40" />
                            <label class="flex items-center gap-2 text-xs text-gray-600">
                                <input type="checkbox" name="remove_chatbot_avatar" value="1" class="rounded border-gray-300 bg-white text-purple-600 focus:ring-purple-300" />
                                {{ __('Xóa avatar hiện tại') }}
                            </label>
                        </div>
                    @endif
                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <label class="inline-flex cursor-pointer rounded-full focus-within:outline-none focus-within:ring-2 focus-within:ring-violet-400">
                            <span class="inline-flex items-center rounded-full bg-violet-600/80 px-5 py-2 text-sm font-semibold text-white ring-1 ring-violet-500/40 transition hover:bg-violet-500">{{ __('Chọn ảnh') }}</span>
                            <input name="chatbot_avatar" type="file" accept="image/*" class="sr-only" />
                        </label>
                    </div>
                    @error('chatbot_avatar')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="chatbot_system_prompt">{{ __('Hướng dẫn & huấn luyện bot') }}</label>
                    <textarea
                        id="chatbot_system_prompt" name="chatbot_system_prompt" rows="6"
                        class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                    >{{ old('chatbot_system_prompt', $setting->chatbot_system_prompt) }}</textarea>
                    @error('chatbot_system_prompt')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4">
                    <p class="text-sm font-medium text-gray-900">{{ __('Tệp dữ liệu huấn luyện (.txt)') }}</p>

                    @if($setting->chatbot_training_file_path)
                        <div class="mt-3 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-gray-700">
                            {{ __('Đang dùng:') }} {{ basename($setting->chatbot_training_file_path) }}
                        </div>
                        <label class="mt-3 flex items-center gap-2 text-xs text-gray-600">
                            <input type="checkbox" name="remove_chatbot_training_file" value="1" class="rounded border-gray-300 bg-white text-purple-600 focus:ring-purple-300" />
                            {{ __('Xóa tệp huấn luyện hiện tại') }}
                        </label>
                    @endif

                    <div class="mt-3 flex flex-wrap items-center gap-3">
                        <label class="inline-flex cursor-pointer rounded-full focus-within:outline-none focus-within:ring-2 focus-within:ring-violet-400">
                            <span class="inline-flex items-center rounded-full bg-violet-600/80 px-5 py-2 text-sm font-semibold text-white ring-1 ring-violet-500/40 transition hover:bg-violet-500">{{ __('Chọn file .txt') }}</span>
                            <input name="chatbot_training_file" type="file" accept=".txt,text/plain" class="sr-only" />
                        </label>
                    </div>
                    @error('chatbot_training_file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 border-t border-gray-200 pt-6">
        <div class="border-t border-gray-200 pt-8">
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">{{ __('Nút liên hệ nhanh') }}</h2>
                <label class="relative inline-flex cursor-pointer items-center gap-2">
                    <input type="hidden" name="social_enabled" value="0">
                    <input type="checkbox" name="social_enabled" value="1" class="sr-only peer"
                           {{ old('social_enabled', $setting->social_enabled ?? true) ? 'checked' : '' }}>
                    <div class="peer h-5 w-9 rounded-full bg-slate-700 after:absolute after:left-[2px] after:top-[2px] after:h-4 after:w-4 after:rounded-full after:bg-white after:transition-all peer-checked:bg-violet-600 peer-checked:after:translate-x-4"></div>
                    <span class="text-xs font-medium text-gray-700">{{ __('Hiện nút') }}</span>
                </label>
            </div>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="social_facebook">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.49 0-1.956.93-1.956 1.887v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/></svg>
                            Facebook
                        </span>
                    </label>
                    <input id="social_facebook" name="social_facebook" type="url"
                           value="{{ old('social_facebook', $setting->social_facebook) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                           placeholder="https://facebook.com/yourpage" />
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="social_zalo">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#0068FF]" viewBox="0 0 48 48" fill="currentColor"><path d="M24 4C13 4 4 13 4 24s9 20 20 20 20-9 20-20S35 4 24 4zm8.6 28.5c-.4.4-1 .6-1.7.6-.5 0-1-.1-1.5-.4-1.4-.8-2.8-1.9-4.1-3.3-1.3-1.4-2.3-2.8-3-4.2-.4-.8-.3-1.6.2-2.2l1-1.2c.3-.4.3-.9 0-1.3l-2.5-3.3c-.3-.4-.8-.5-1.2-.3l-.8.4c-1.4.7-2.1 2.2-1.8 3.8.8 3.9 3.2 7.7 6.7 11.1 3.5 3.4 7.3 5.6 11.1 6.2 1.5.2 2.9-.5 3.5-1.9l.3-.8c.2-.5.1-1-.4-1.3l-3.4-2.2c-.4-.3-.9-.2-1.3.1l-1.1.9z"/></svg>
                            Zalo
                        </span>
                    </label>
                    <input id="social_zalo" name="social_zalo" type="url"
                           value="{{ old('social_zalo', $setting->social_zalo) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                           placeholder="https://zalo.me/0912345678" />
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="social_phone">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 5v1.75z"/></svg>
                            Số điện thoại
                        </span>
                    </label>
                    <input id="social_phone" name="social_phone" type="text"
                           value="{{ old('social_phone', $setting->social_phone) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                           placeholder="0912345678" />
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="social_instagram">
                        <span class="inline-flex items-center gap-1.5">
                            <svg class="h-3.5 w-3.5 text-[#E1306C]" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/></svg>
                            Instagram
                        </span>
                    </label>
                    <input id="social_instagram" name="social_instagram" type="url"
                           value="{{ old('social_instagram', $setting->social_instagram) }}"
                           class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 placeholder:text-gray-500 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                           placeholder="https://instagram.com/yourpage" />
                </div>
            </div>
        </div>

        <div class="border-t border-gray-200 pt-8">
            <h2 class="font-semibold text-gray-900">{{ __('Nội dung trang chủ') }}</h2>
            <div class="mt-4 grid gap-4">
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_facilities_title">Tiêu đề tiện ích</label>
                    <input id="home_facilities_title" name="home_facilities_title" type="text" value="{{ old('home_facilities_title', $setting->home_facilities_title) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_facilities_items">Danh sách tiện ích</label>
                    <textarea id="home_facilities_items" name="home_facilities_items" rows="7" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_facilities_items', $setting->home_facilities_items) }}</textarea>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_title">Tiêu đề vị trí</label>
                        <input id="home_location_title" name="home_location_title" type="text" value="{{ old('home_location_title', $setting->home_location_title) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_map_url">Map Embed URL</label>
                        <input id="home_location_map_url" name="home_location_map_url" type="text" value="{{ old('home_location_map_url', $setting->home_location_map_url) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_description">Mô tả vị trí</label>
                    <textarea id="home_location_description" name="home_location_description" rows="3" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_location_description', $setting->home_location_description) }}</textarea>
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_distances">Khoảng cách</label>
                    <textarea id="home_location_distances" name="home_location_distances" rows="4" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_location_distances', $setting->home_location_distances) }}</textarea>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_cta_label">Nhãn nút CTA vị trí</label>
                        <input id="home_location_cta_label" name="home_location_cta_label" type="text" value="{{ old('home_location_cta_label', $setting->home_location_cta_label) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_location_cta_link">Link CTA vị trí</label>
                        <input id="home_location_cta_link" name="home_location_cta_link" type="text" value="{{ old('home_location_cta_link', $setting->home_location_cta_link) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_offers_title">Tiêu đề ưu đãi</label>
                    <input id="home_offers_title" name="home_offers_title" type="text" value="{{ old('home_offers_title', $setting->home_offers_title) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_offers_items">Danh sách ưu đãi</label>
                    <textarea id="home_offers_items" name="home_offers_items" rows="7" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_offers_items', $setting->home_offers_items) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_testimonials_title">Tiêu đề đánh giá</label>
                    <input id="home_testimonials_title" name="home_testimonials_title" type="text" value="{{ old('home_testimonials_title', $setting->home_testimonials_title) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_testimonials_items">Danh sách đánh giá</label>
                    <textarea id="home_testimonials_items" name="home_testimonials_items" rows="7" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_testimonials_items', $setting->home_testimonials_items) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_faq_title">Tiêu đề FAQ</label>
                    <input id="home_faq_title" name="home_faq_title" type="text" value="{{ old('home_faq_title', $setting->home_faq_title) }}" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200" />
                </div>
                <div>
                    <label class="block text-xs font-semibold uppercase tracking-wide text-gray-600" for="home_faq_items">Danh sách FAQ</label>
                    <textarea id="home_faq_items" name="home_faq_items" rows="7" class="mt-1 w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-900 focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200">{{ old('home_faq_items', $setting->home_faq_items) }}</textarea>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-4 border-t border-gray-200 pt-6">
            <button type="submit" class="rounded-full bg-gradient-to-r from-violet-600 to-violet-700 px-8 py-2.5 text-sm font-semibold text-white shadow-lg shadow-violet-900/30 ring-1 ring-violet-400/30 transition hover:from-violet-500 hover:to-violet-600">
                {{ __('Lưu cài đặt') }}
            </button>
            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">{{ __('Hủy') }}</a>
        </div>
    </form>
</x-hotel-layout>
