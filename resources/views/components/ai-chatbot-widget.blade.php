@php
    $setting    = \App\Models\SiteSetting::instance();
    $botName    = $setting->chatbot_name ?: 'Trợ lý AI';
    $botAvatar  = $setting->chatbot_avatar_path ? asset('storage/'.$setting->chatbot_avatar_path) : null;
    $hotelName  = $setting->displayName();
    $isAuth     = auth()->check() && auth()->user()->isCustomer();
    $fetchUrl   = $isAuth ? route('guest.chat.messages') : null;
@endphp

<style>
.ai-bubble-bot { background:#f0f0f0; color:#1a1a1a; }
.ai-bubble-bot ul { list-style:disc; padding-left:1.1rem; margin:.25rem 0; }
.ai-bubble-bot ol { list-style:decimal; padding-left:1.1rem; margin:.25rem 0; }
.ai-bubble-bot li { margin:.1rem 0; }
.ai-bubble-bot p  { margin:.2rem 0; }
.ai-bubble-bot strong { font-weight:700; }
.ai-bubble-bot em { font-style:italic; }
.ai-bubble-bot a  { color:#f97316; text-decoration:underline; }
.ai-bubble-admin { background:#ede9fe; color:#1a1a1a; border:1px solid #c4b5fd; }
</style>

<script>
function aiChatbot({ sendUrl, fetchUrl, csrfToken }) {
    return {
        open: false,
        input: '',
        messages: [],   // { id, source:'user'|'ai'|'admin', text }
        loading: false,
        errorMsg: '',
        pollTimer: null,
        lastId: 0,
        suggestions: [
            { icon: '🛏️', label: 'Giá phòng bao nhiêu?' },
            { icon: '🏨', label: 'Phòng nào còn trống?' },
            { icon: '🎁', label: 'Có ưu đãi gì không?' },
            { icon: '📋', label: 'Cách đặt phòng?' },
        ],

        async toggle() {
            this.open = !this.open;
            if (this.open) {
                if (fetchUrl && this.messages.length === 0) await this.loadHistory();
                this.$nextTick(() => this.scrollToBottom());
                this.startPoll();
            } else {
                this.stopPoll();
            }
        },

        /* Load toàn bộ lịch sử từ DB khi mở widget */
        async loadHistory() {
            if (!fetchUrl) return;
            try {
                const res = await fetch(fetchUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const data = await res.json();
                const msgs = data.messages || [];
                this.messages = msgs.map(m => ({
                    id:     m.id,
                    source: m.source,
                    text:   m.body,
                }));
                if (msgs.length > 0) this.lastId = msgs[msgs.length - 1].id;
            } catch {}
        },

        /* Poll tin nhắn mới (admin reply) */
        startPoll() {
            if (!fetchUrl) return;
            this.stopPoll();
            this.pollTimer = setInterval(() => this.pollNew(), 5000);
        },
        stopPoll() {
            if (this.pollTimer !== null) { clearInterval(this.pollTimer); this.pollTimer = null; }
        },
        async pollNew() {
            if (!fetchUrl) return;
            try {
                const res = await fetch(fetchUrl + '?after=' + this.lastId, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                    credentials: 'same-origin'
                });
                if (!res.ok) return;
                const data = await res.json();
                const newMsgs = (data.messages || []).filter(m => m.id > this.lastId && m.source === 'admin');
                newMsgs.forEach(m => {
                    this.messages.push({ id: m.id, source: 'admin', text: m.body });
                    this.lastId = m.id;
                });
                if (newMsgs.length > 0) this.$nextTick(() => this.scrollToBottom());
            } catch {}
        },

        async send() {
            const text = this.input.trim();
            if (!text || this.loading) return;
            this.input = '';
            this.errorMsg = '';
            this.messages.push({ id: null, source: 'user', text });
            this.loading = true;
            this.$nextTick(() => this.scrollToBottom());

            const history = this.messages.slice(0, -1)
                .filter(m => m.source === 'user' || m.source === 'ai')
                .map(m => ({ role: m.source === 'user' ? 'user' : 'model', text: m.text }));

            try {
                const res  = await fetch(sendUrl, {
                    method:  'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body:    JSON.stringify({ message: text, history }),
                });
                const data = await res.json();
                if (!res.ok || data.error) {
                    this.errorMsg = data.error || 'Có lỗi xảy ra.';
                } else {
                    this.messages.push({ id: data.msg_id || null, source: 'ai', text: data.reply });
                    if (data.msg_id) this.lastId = data.msg_id;
                }
            } catch { this.errorMsg = 'Không thể kết nối. Thử lại sau.'; }
            this.loading = false;
            this.$nextTick(() => this.scrollToBottom());
        },

        sendSuggestion(t) { this.input = t; this.send(); },

        scrollToBottom() {
            const el = this.$refs.msgBox;
            if (el) el.scrollTop = el.scrollHeight;
        },

        md(text) {
            if (!text) return '';
            let html = text
                .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
                .replace(/\*\*\*(.+?)\*\*\*/g,'<strong><em>$1</em></strong>')
                .replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>')
                .replace(/__(.+?)__/g,'<strong>$1</strong>')
                .replace(/\*([^*\n]+?)\*/g,'<em>$1</em>')
                .replace(/((?:^\d+\. .+\n?)+)/gm, m => '<ol>'+m.replace(/^\d+\. (.+)$/gm,'<li>$1</li>')+'</ol>')
                .replace(/((?:^[\*\-] .+\n?)+)/gm, m => '<ul>'+m.replace(/^[\*\-] (.+)$/gm,'<li>$1</li>')+'</ul>')
                .replace(/\n{2,}/g,'</p><p>')
                .replace(/\n/g,'<br>');
            return '<p>'+html+'</p>';
        },
    };
}
</script>

<div
    class="fixed bottom-4 right-4 z-50 flex flex-col items-end gap-3 sm:bottom-6 sm:right-6"
    x-data="aiChatbot({
        sendUrl:   @js(route('ai.chat')),
        fetchUrl:  @js($fetchUrl),
        csrfToken: @js(csrf_token())
    })"
    @keydown.escape.window="if(open){ open=false; stopPoll(); }"
>
    {{-- ── Panel ── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-3 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 translate-y-3 scale-95"
        style="display:none; width:320px; height:460px; max-height:calc(100vh - 5rem);"
        class="flex flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
    >
        {{-- ── Header ── --}}
        <div class="flex shrink-0 items-center gap-3 px-4 py-3" style="background:linear-gradient(135deg,#f97316,#ea580c);">
            <div class="relative shrink-0">
                <div class="h-10 w-10 overflow-hidden rounded-full bg-white/20 ring-2 ring-white/40">
                    @if($botAvatar)
                        <img src="{{ $botAvatar }}" alt="{{ $botName }}" class="h-full w-full object-cover"/>
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2a2 2 0 012 2v1h1a3 3 0 013 3v1h.5a1.5 1.5 0 010 3H18v1a3 3 0 01-3 3h-1v1a2 2 0 01-4 0v-1H9a3 3 0 01-3-3v-1h-.5a1.5 1.5 0 010-3H6V8a3 3 0 013-3h1V4a2 2 0 012-2zm-2 9a1 1 0 100 2 1 1 0 000-2zm4 0a1 1 0 100 2 1 1 0 000-2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <span class="absolute bottom-0 right-0 h-2.5 w-2.5 rounded-full border-2 border-orange-500 bg-emerald-400"></span>
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-white">{{ $botName }}</p>
                <p class="flex items-center gap-1 text-[11px] text-orange-100">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                    Gemini AI · Luôn sẵn sàng
                </p>
            </div>
            <button @click="open=false; stopPoll()" class="flex h-7 w-7 items-center justify-center rounded-full bg-white/20 text-white hover:bg-white/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- ── Messages ── --}}
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-1" style="background:#f8f8f8;" x-ref="msgBox">

            {{-- Tin chào tĩnh --}}
            <div class="flex items-end gap-2 mb-2">
                <div class="h-7 w-7 shrink-0 overflow-hidden rounded-full" style="background:#f97316;">
                    @if($botAvatar)
                        <img src="{{ $botAvatar }}" alt="" class="h-full w-full object-cover"/>
                    @else
                        <div class="flex h-full w-full items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2a2 2 0 012 2v1h1a3 3 0 013 3v1h.5a1.5 1.5 0 010 3H18v1a3 3 0 01-3 3h-1v1a2 2 0 01-4 0v-1H9a3 3 0 01-3-3v-1h-.5a1.5 1.5 0 010-3H6V8a3 3 0 013-3h1V4a2 2 0 012-2zm-2 9a1 1 0 100 2 1 1 0 000-2zm4 0a1 1 0 100 2 1 1 0 000-2z"/>
                            </svg>
                        </div>
                    @endif
                </div>
                <div class="ai-bubble-bot max-w-[78%] rounded-2xl rounded-bl-sm px-3 py-2 text-sm leading-relaxed shadow-sm">
                    Xin chào! 👋 Tôi là <strong>{{ $botName }}</strong>.<br>Bạn cần tư vấn gì không?
                </div>
            </div>

            {{-- Gợi ý nhanh (chỉ khi chưa có hội thoại) --}}
            <template x-if="messages.length === 0">
                <div class="flex flex-wrap gap-1.5 pl-9 pb-2">
                    <template x-for="s in suggestions" :key="s.label">
                        <button type="button" @click="sendSuggestion(s.label)"
                            class="inline-flex items-center gap-1 rounded-full border border-orange-200 bg-white px-2.5 py-1 text-xs font-medium text-gray-600 shadow-sm transition hover:bg-orange-50 hover:border-orange-400">
                            <span x-text="s.icon"></span><span x-text="s.label"></span>
                        </button>
                    </template>
                </div>
            </template>

            {{-- Lịch sử hội thoại --}}
            <template x-for="(msg, i) in messages" :key="i">
                <div>
                    {{-- Khách gửi --}}
                    <div x-show="msg.source==='user'" class="flex justify-end mb-1">
                        <div class="max-w-[78%] rounded-2xl rounded-br-sm px-3.5 py-2 text-sm leading-relaxed text-white shadow-sm"
                             style="background:#f97316; word-break:break-word;"
                             x-text="msg.text"></div>
                    </div>

                    {{-- AI trả lời --}}
                    <div x-show="msg.source==='ai'" class="flex items-end gap-2 mb-1">
                        <div class="h-7 w-7 shrink-0 overflow-hidden rounded-full" style="background:#f97316;min-width:1.75rem;">
                            @if($botAvatar)
                                <img src="{{ $botAvatar }}" alt="" class="h-full w-full object-cover"/>
                            @else
                                <div class="flex h-full w-full items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 2a2 2 0 012 2v1h1a3 3 0 013 3v1h.5a1.5 1.5 0 010 3H18v1a3 3 0 01-3 3h-1v1a2 2 0 01-4 0v-1H9a3 3 0 01-3-3v-1h-.5a1.5 1.5 0 010-3H6V8a3 3 0 013-3h1V4a2 2 0 012-2zm-2 9a1 1 0 100 2 1 1 0 000-2zm4 0a1 1 0 100 2 1 1 0 000-2z"/>
                                    </svg>
                                </div>
                            @endif
                        </div>
                        <div class="ai-bubble-bot max-w-[78%] rounded-2xl rounded-bl-sm px-3.5 py-2 text-sm leading-relaxed shadow-sm"
                             style="word-break:break-word;"
                             x-html="md(msg.text)"></div>
                    </div>

                    {{-- Admin reply (màu tím nhạt, có badge "Nhân viên") --}}
                    <div x-show="msg.source==='admin'" class="flex items-end gap-2 mb-1">
                        <div class="h-7 w-7 shrink-0 flex items-center justify-center rounded-full bg-violet-500 min-w-[1.75rem]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" viewBox="0 0 24 24" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM3.751 20.105a8.25 8.25 0 0116.498 0 .75.75 0 01-.437.695A18.683 18.683 0 0112 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 01-.437-.695z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="max-w-[78%]">
                            <p class="mb-0.5 text-[10px] font-semibold text-violet-600">Nhân viên hỗ trợ</p>
                            <div class="ai-bubble-admin rounded-2xl rounded-bl-sm px-3.5 py-2 text-sm leading-relaxed shadow-sm"
                                 style="word-break:break-word;"
                                 x-text="msg.text"></div>
                        </div>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="loading" class="flex items-end gap-2 mb-1">
                <div class="h-7 w-7 shrink-0 rounded-full" style="background:#f97316;min-width:1.75rem;"></div>
                <div class="flex items-center gap-1.5 rounded-2xl rounded-bl-sm bg-gray-200 px-4 py-3 shadow-sm">
                    <span class="h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:-0.3s"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-gray-500" style="animation-delay:-0.15s"></span>
                    <span class="h-2 w-2 animate-bounce rounded-full bg-gray-500"></span>
                </div>
            </div>

            {{-- Lỗi --}}
            <div x-show="errorMsg" class="ml-9 rounded-xl px-3 py-1.5 text-xs"
                 style="background:#fef2f2;color:#dc2626;border:1px solid #fecaca;"
                 x-text="errorMsg"></div>
        </div>

        {{-- ── Input ── --}}
        <form class="flex shrink-0 items-center gap-2 border-t border-gray-200 bg-white px-3 py-2.5" @submit.prevent="send">
            <input type="text" x-model="input" autocomplete="off" :disabled="loading"
                placeholder="Nhập tin nhắn..."
                class="min-w-0 flex-1 rounded-full border border-gray-200 bg-gray-100 px-4 py-2 text-sm text-gray-800 placeholder:text-gray-400 focus:border-orange-300 focus:bg-white focus:outline-none focus:ring-2 focus:ring-orange-100 disabled:opacity-50"/>
            <button type="submit" :disabled="loading || input.trim()===''"
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full text-white shadow transition hover:opacity-90 disabled:opacity-40"
                style="background:linear-gradient(135deg,#f97316,#ea580c);">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z"/>
                </svg>
            </button>
        </form>
    </div>

    {{-- ── Nút toggle ── --}}
    <button type="button" @click="toggle()"
        class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-white shadow-xl transition hover:scale-105 focus:outline-none"
        style="background:linear-gradient(135deg,#f97316,#ea580c);"
        :aria-expanded="open" aria-label="Chat AI">
        <span x-show="!open">
            @if($botAvatar)
                <img src="{{ $botAvatar }}" alt="{{ $botName }}" class="h-10 w-10 rounded-full object-cover"/>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2a2 2 0 012 2v1h1a3 3 0 013 3v1h.5a1.5 1.5 0 010 3H18v1a3 3 0 01-3 3h-1v1a2 2 0 01-4 0v-1H9a3 3 0 01-3-3v-1h-.5a1.5 1.5 0 010-3H6V8a3 3 0 013-3h1V4a2 2 0 012-2zm-2 9a1 1 0 100 2 1 1 0 000-2zm4 0a1 1 0 100 2 1 1 0 000-2z"/>
                </svg>
            @endif
        </span>
        <span x-show="open" class="text-2xl font-light">×</span>
    </button>
</div>
