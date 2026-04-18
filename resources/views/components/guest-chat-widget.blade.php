<div
    class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-2 sm:bottom-6 sm:right-6"
    x-data="customerChat({
        fetchUrl: @js(route('guest.chat.messages')),
        sendUrl: @js(route('guest.chat.store')),
        errLoad: @js(__('Không tải được tin nhắn.')),
        errSend: @js(__('Không gửi được. Thử lại sau.')),
    })"
    @keydown.escape.window="if (open) { open = false; stopPoll(); }"
>
    <div
        id="guest-chat-panel"
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        class="flex h-[min(28rem,calc(100vh-6rem))] w-[min(22rem,calc(100vw-2rem))] flex-col overflow-hidden rounded-2xl border border-white/15 bg-slate-900/95 shadow-2xl shadow-black/50 backdrop-blur-xl"
    >
        <div class="border-b border-white/10 bg-gradient-to-r from-blue-900/50 to-slate-900/80 px-4 py-3">
            <p class="text-sm font-semibold text-white">{{ __('Chat với khách sạn') }}</p>
            <p class="text-xs text-slate-300">{{ __('Đăng nhập không bắt buộc. Chúng tôi sẽ trả lời sớm nhất có thể.') }}</p>
        </div>
        <div class="flex-1 space-y-2 overflow-y-auto px-3 py-3" id="guest-chat-scroll">
            <template x-if="messages.length === 0">
                <p class="text-center text-xs text-slate-500">{{ __('Chưa có tin nhắn. Hãy bắt đầu cuộc trò chuyện.') }}</p>
            </template>
            <template x-for="m in messages" :key="m.id">
                <div :class="m.is_admin ? 'flex justify-start' : 'flex justify-end'">
                    <div
                        class="max-w-[85%] rounded-2xl px-3 py-2 text-sm leading-snug"
                        :class="m.is_admin ? 'bg-white/10 text-slate-100 ring-1 ring-white/10' : 'bg-amber-600/90 text-amber-50'"
                        x-text="m.body"
                    ></div>
                </div>
            </template>
        </div>
        <template x-if="error">
            <p class="px-3 pb-1 text-xs text-rose-400" x-text="error"></p>
        </template>
        <form class="border-t border-white/10 p-3" @submit.prevent="send">
            <div class="flex gap-2">
                <label class="sr-only" for="guest-chat-input">{{ __('Tin nhắn') }}</label>
                <input
                    id="guest-chat-input"
                    type="text"
                    x-model="body"
                    autocomplete="off"
                    class="min-w-0 flex-1 rounded-xl border border-white/10 bg-black/30 px-3 py-2 text-sm text-white placeholder:text-slate-500 focus:border-amber-400/50 focus:outline-none focus:ring-1 focus:ring-amber-400/40"
                    placeholder="{{ __('Nhập tin nhắn…') }}"
                    :disabled="sending"
                />
                <button
                    type="submit"
                    class="shrink-0 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-amber-900/30 transition hover:bg-amber-500 disabled:opacity-50"
                    :disabled="sending"
                >
                    {{ __('Gửi') }}
                </button>
            </div>
        </form>
    </div>

    <button
        type="button"
        @click="toggle()"
        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-900/35 ring-2 ring-blue-400/30 transition hover:scale-105 hover:bg-blue-500 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-300"
        :aria-expanded="open"
        aria-controls="guest-chat-panel"
        :aria-label="open ? @js(__('Đóng chat')) : @js(__('Mở chat'))"
    >
        <span x-show="!open" x-cloak class="flex items-center justify-center" aria-hidden="true">
            <svg class="h-8 w-8" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M8 10c0-1.66 1.34-3 3-3h10c1.66 0 3 1.34 3 3v6c0 1.66-1.34 3-3 3h-3.2L12 22v-3H11c-1.66 0-3-1.34-3-3v-6z" stroke="currentColor" stroke-width="2" stroke-linejoin="round" fill="none"/>
                <circle cx="12" cy="13" r="1.25" fill="currentColor"/>
                <circle cx="16" cy="13" r="1.25" fill="currentColor"/>
                <circle cx="20" cy="13" r="1.25" fill="currentColor"/>
            </svg>
        </span>
        <span x-show="open" x-cloak class="text-xl font-light leading-none" aria-hidden="true">×</span>
    </button>
</div>
