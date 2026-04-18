<x-hotel-layout>
    <x-slot name="header">{{ __('Tin nhắn khách hàng') }}</x-slot>

    @if (session('status'))
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-900">
            {{ session('status') }}
        </div>
    @endif

    <div class="flex min-h-[32rem] flex-col gap-4 lg:flex-row">
        {{-- Sidebar: danh sách cuộc trò chuyện --}}
        <aside class="w-full shrink-0 rounded-2xl border border-gray-200 bg-white shadow-sm lg:w-80">
            <div class="border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-gray-900">{{ __('Cuộc trò chuyện') }}</h2>
                    <p class="text-xs text-gray-600">{{ __('Chọn khách để xem và trả lời.') }}</p>
                </div>
                <span id="new-badge" class="hidden h-2 w-2 animate-pulse rounded-full bg-amber-500"></span>
            </div>
            <ul class="max-h-80 overflow-y-auto lg:max-h-none">
                @forelse ($conversations as $c)
                    <li>
                        <a
                            href="{{ route('admin.messages.index', ['conversation' => $c->id]) }}"
                            class="flex items-start gap-3 border-b border-gray-100 px-4 py-3 text-left transition hover:bg-gray-50 {{ $selected && $selected->is($c) ? 'bg-purple-50 ring-1 ring-inset ring-purple-200' : '' }}"
                        >
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-medium text-gray-900">{{ $c->user?->name ?? __('Khách (chưa đăng nhập)') }}</p>
                                <p class="truncate text-xs text-gray-600">
                                    @if ($c->user)
                                        {{ $c->user->email }}
                                    @elseif ($c->guest_key)
                                        {{ __('Phiên') }}: {{ \Illuminate\Support\Str::limit($c->guest_key, 12, '…') }}
                                    @endif
                                </p>
                            </div>
                            @if ($c->unread_from_customer_count > 0)
                                <span class="mt-0.5 inline-flex h-5 min-w-5 shrink-0 items-center justify-center rounded-full bg-amber-500 px-1.5 text-[10px] font-bold text-amber-950">{{ $c->unread_from_customer_count }}</span>
                            @endif
                        </a>
                    </li>
                @empty
                    <li class="px-4 py-8 text-center text-sm text-gray-600">{{ __('Chưa có tin nhắn nào.') }}</li>
                @endforelse
            </ul>
        </aside>

        {{-- Panel: nội dung cuộc trò chuyện --}}
        <section class="flex min-w-0 flex-1 flex-col rounded-2xl border border-gray-200 bg-white shadow-sm">
            @if ($selected)
                <div class="border-b border-gray-200 px-4 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-semibold text-gray-900">{{ $selected->user?->name ?? __('Khách (chưa đăng nhập)') }}</p>
                        <p class="text-xs text-gray-600">
                            @if ($selected->user)
                                {{ $selected->user->email }}
                            @elseif ($selected->guest_key)
                                {{ __('Phiên khách') }}: {{ \Illuminate\Support\Str::limit($selected->guest_key, 16, '…') }}
                            @endif
                        </p>
                    </div>
                    <span class="text-[10px] font-medium text-gray-500 italic" id="live-indicator">{{ __('Đang tải…') }}</span>
                </div>

                <div class="flex flex-1 flex-col overflow-hidden">
                    <div id="admin-msg-scroll" class="flex-1 space-y-3 overflow-y-auto bg-gray-50/80 p-4">
                        @forelse ($messages as $m)
                            @php
                                $isAi    = $m->is_admin && $m->sender_id === null;
                                $isAdmin = $m->is_admin && $m->sender_id !== null;
                            @endphp
                            <div class="flex {{ $m->is_admin ? 'justify-end' : 'justify-start' }}" data-msg-id="{{ $m->id }}">
                                <div class="max-w-[80%]">
                                    @if($isAi)
                                        <p class="mb-0.5 text-right text-[10px] font-medium text-amber-800">🤖 AI · {{ $m->created_at->format('d/m H:i') }}</p>
                                    @elseif($isAdmin)
                                        <p class="mb-0.5 text-right text-[10px] font-medium text-purple-800">👤 Nhân viên · {{ $m->created_at->format('d/m H:i') }}</p>
                                    @endif
                                    <div class="rounded-2xl px-3 py-2 text-sm leading-relaxed
                                        @if($isAi) bg-amber-100 text-amber-950 ring-1 ring-amber-200
                                        @elseif($isAdmin) bg-purple-100 text-purple-950 ring-1 ring-purple-200
                                        @else bg-white text-gray-900 shadow-sm ring-1 ring-gray-200
                                        @endif">
                                        <p class="whitespace-pre-wrap">{{ $m->body }}</p>
                                        @if(!$m->is_admin)
                                            <p class="mt-1 text-[10px] text-gray-600">{{ __('Khách') }} · {{ $m->created_at->format('d/m H:i') }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p id="empty-msg" class="text-center text-sm text-gray-600">{{ __('Khách chưa gửi tin nhắn nào.') }}</p>
                        @endforelse
                    </div>

                    <form method="post" action="{{ route('admin.messages.reply', $selected) }}" class="border-t border-gray-200 bg-white p-4">
                        @csrf
                        <label class="sr-only" for="admin-reply-body">{{ __('Trả lời') }}</label>
                        <textarea
                            id="admin-reply-body"
                            name="body"
                            rows="3"
                            required
                            class="w-full rounded-xl border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 placeholder:text-gray-500 shadow-sm focus:border-purple-400 focus:outline-none focus:ring-2 focus:ring-purple-200"
                            placeholder="{{ __('Nhập nội dung trả lời…') }}"
                        ></textarea>
                        <div class="mt-3 flex justify-end">
                            <button type="submit" class="rounded-xl bg-purple-600 px-5 py-2 text-sm font-semibold text-white shadow transition hover:bg-purple-700">
                                {{ __('Gửi trả lời') }}
                            </button>
                        </div>
                    </form>
                </div>

                @push('scripts')
                <script>
                (function () {
                    var pollUrl  = @js(route('admin.messages.json', $selected));
                    var msgScroll = document.getElementById('admin-msg-scroll');
                    var indicator = document.getElementById('live-indicator');
                    var pollTimer = null;

                    function lastId() {
                        var nodes = msgScroll.querySelectorAll('[data-msg-id]');
                        if (!nodes.length) return 0;
                        return parseInt(nodes[nodes.length - 1].getAttribute('data-msg-id')) || 0;
                    }

                    function escHtml(t) {
                        return (t || '').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
                    }

                    function appendMessage(m) {
                        var empty = document.getElementById('empty-msg');
                        if (empty) empty.remove();

                        var isAi    = m.is_admin && m.sender_id === null;
                        var isAdmin = m.is_admin && m.sender_id !== null;
                        var side    = m.is_admin ? 'justify-end' : 'justify-start';
                        var bubble  = isAi
                            ? 'bg-amber-100 text-amber-950 ring-1 ring-amber-200'
                            : (isAdmin ? 'bg-purple-100 text-purple-950 ring-1 ring-purple-200' : 'bg-white text-gray-900 shadow-sm ring-1 ring-gray-200');
                        var label   = isAi ? '🤖 AI · ' + m.created_at
                                    : (isAdmin ? '👤 Nhân viên · ' + m.created_at : '');
                        var labelHtml = label
                            ? '<p class="mb-0.5 text-right text-[10px] font-medium ' + (isAi ? 'text-amber-800' : 'text-purple-800') + '">' + label + '</p>'
                            : '';
                        var timestamp = !m.is_admin
                            ? '<p class="mt-1 text-[10px] text-gray-600">Khách · ' + m.created_at + '</p>'
                            : '';

                        var div = document.createElement('div');
                        div.className = 'flex ' + side;
                        div.setAttribute('data-msg-id', m.id);
                        div.innerHTML =
                            '<div class="max-w-[80%]">' +
                                labelHtml +
                                '<div class="rounded-2xl px-3 py-2 text-sm leading-relaxed ' + bubble + '">' +
                                    '<p class="whitespace-pre-wrap">' + escHtml(m.body) + '</p>' +
                                    timestamp +
                                '</div>' +
                            '</div>';
                        msgScroll.appendChild(div);
                        msgScroll.scrollTop = msgScroll.scrollHeight;
                    }

                    function poll() {
                        var after = lastId();
                        fetch(pollUrl + '?after=' + after, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
                            credentials: 'same-origin'
                        })
                        .then(function(r) { return r.ok ? r.json() : null; })
                        .then(function(data) {
                            if (!data) return;
                            var msgs = (data.messages || []).filter(function(m) { return m.id > after; });
                            msgs.forEach(appendMessage);
                            if (indicator) indicator.textContent = 'Trực tiếp ✓';
                        })
                        .catch(function() {
                            if (indicator) indicator.textContent = 'Lỗi kết nối';
                        });
                    }

                    msgScroll.scrollTop = msgScroll.scrollHeight;
                    if (indicator) indicator.textContent = 'Tự động cập nhật ✓';

                    pollTimer = setInterval(poll, 4000);

                    document.addEventListener('visibilitychange', function() {
                        if (document.hidden) {
                            clearInterval(pollTimer);
                        } else {
                            poll();
                            pollTimer = setInterval(poll, 4000);
                        }
                    });
                })();
                </script>
                @endpush
            @else
                <div class="flex flex-1 items-center justify-center p-8 text-center text-sm text-gray-600">
                    {{ __('Chưa có cuộc trò chuyện nào từ khách.') }}
                </div>
            @endif
        </section>
    </div>
</x-hotel-layout>
