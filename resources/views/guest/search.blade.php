<x-guest-hotel-layout :title="__('Tìm phòng')">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        @if (session('error'))
            <div class="mb-6 rounded-xl border border-red-500/40 bg-red-950/50 px-4 py-3 text-sm text-red-200 ring-1 ring-red-500/20">{{ session('error') }}</div>
        @endif

        <div class="mx-auto max-w-5xl rounded-3xl border border-white/10 bg-slate-900/70 p-6 shadow-2xl shadow-black/40 ring-1 ring-white/5 backdrop-blur-xl sm:p-10">
            <p class="text-xs font-semibold uppercase tracking-[0.15em] text-violet-300/90">{{ __('Đặt phòng') }}</p>
            <h1 class="mt-2 font-display text-2xl font-bold text-slate-50">{{ __('Tìm phòng trống') }}</h1>
            <p class="mt-2 text-slate-400">{{ __('Chọn ngày nhận/trả phòng và số lượng khách.') }}</p>
            <form
                method="get"
                action="{{ route('guest.search-rooms') }}"
                class="mt-8 flex flex-col gap-4 md:flex-row md:flex-wrap md:items-end"
                x-data="{
                    checkIn: {{ \Illuminate\Support\Js::from($check_in?->toDateString() ?? '') }},
                    checkOut: {{ \Illuminate\Support\Js::from($check_out?->toDateString() ?? '') }},
                    minCheckout() {
                        if (!this.checkIn) return null;
                        const p = this.checkIn.split('-').map(Number);
                        const d = new Date(p[0], p[1] - 1, p[2]);
                        d.setDate(d.getDate() + 1);
                        const y = d.getFullYear();
                        const m = String(d.getMonth() + 1).padStart(2, '0');
                        const day = String(d.getDate()).padStart(2, '0');
                        return `${y}-${m}-${day}`;
                    },
                    syncCheckoutAfterCheckIn() {
                        this.$nextTick(() => {
                            const m = this.minCheckout();
                            if (m && this.checkOut && this.checkOut < m) {
                                this.checkOut = m;
                            }
                        });
                    }
                }"
                x-init="syncCheckoutAfterCheckIn()"
            >
                <div class="min-w-[160px] flex-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Ngày nhận phòng') }}</label>
                    <input type="date" name="check_in" x-model="checkIn" @change="syncCheckoutAfterCheckIn()" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-800/80 px-3 py-2.5 text-slate-100 shadow-inner shadow-black/20 outline-none ring-0 transition focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/30" required />
                </div>
                <div class="min-w-[160px] flex-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Ngày trả phòng') }}</label>
                    <input type="date" name="check_out" x-model="checkOut" :min="minCheckout()" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-800/80 px-3 py-2.5 text-slate-100 shadow-inner shadow-black/20 outline-none ring-0 transition focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/30" required />
                </div>
                <div class="min-w-[120px] flex-1">
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Số lượng người') }}</label>
                    <input type="number" name="guests" min="1" value="{{ $guests }}" class="mt-1 w-full rounded-xl border border-white/10 bg-slate-800/80 px-3 py-2.5 text-slate-100 shadow-inner shadow-black/20 outline-none ring-0 transition focus:border-amber-500/50 focus:ring-2 focus:ring-amber-500/30" required />
                </div>
                <div class="md:pb-0.5">
                    <button type="submit" class="w-full rounded-full bg-gradient-to-r from-amber-700 to-amber-900 px-8 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/40 ring-1 ring-amber-500/30 transition hover:from-amber-600 hover:to-amber-800 md:w-auto">{{ __('Tìm kiếm') }}</button>
                </div>
            </form>
        </div>

        @if(($searchExecuted ?? false) && $check_in && $check_out && $results->isNotEmpty())
            <p class="mt-10 text-center text-xs text-slate-500">{{ __('Chỉ thẻ đang trỏ chuột nổi to hơn. Đưa chuột vào thẻ phòng để xem tab mô tả / tiện nghi / tiện ích; rời khỏi lưới kết quả để đóng.') }}</p>
            <div class="mt-4 grid gap-6 lg:grid-cols-2" x-data="{ openId: null, tab: 'desc' }" @mouseleave="openId = null">
                @foreach($results as $row)
                    @php($type = $row['type'])
                    @php($avail = $row['available'])
                    @php($rooms = $row['rooms'])
                    <article
                        @mouseenter="openId = {{ $type->id }}; tab = 'desc'"
                        class="relative z-0 flex flex-col overflow-hidden rounded-3xl border border-white/10 bg-slate-900/60 shadow-xl shadow-black/30 ring-1 ring-white/5 backdrop-blur-xl transition-all duration-200 ease-out"
                        :class="openId === {{ $type->id }} ? 'relative z-10 -translate-y-2 scale-[1.02] shadow-2xl shadow-black/50 ring-amber-500/30' : ''"
                    >
                        <div class="aspect-[16/9] shrink-0 bg-gradient-to-br from-slate-800 to-slate-900">
                            @if($type->image_path)
                                <img src="{{ asset('storage/'.$type->image_path) }}" alt="" class="pointer-events-none h-full w-full object-cover select-none" />
                            @else
                                <div class="flex h-full items-center justify-center font-display text-2xl text-slate-500">{{ $type->name }}</div>
                            @endif
                        </div>
                        <div class="px-6 pt-6">
                            <h2 class="font-display text-xl font-semibold text-slate-50">{{ $type->name }}</h2>
                            <p x-show="openId !== {{ $type->id }}" class="mt-2 line-clamp-2 text-sm text-slate-400">{{ $type->description ?: '—' }}</p>
                        </div>
                        <div x-show="openId === {{ $type->id }}" x-cloak x-transition class="border-t border-white/10 px-6 pt-4">
                            <x-room-type-detail-tabs :type="$type" variant="search" />
                        </div>
                        <div class="flex flex-1 flex-col p-6 pt-4">
                            <p class="text-sm text-slate-500">{{ __('Còn :n phòng trống', ['n' => $avail]) }} · {{ __(':b giường · tối đa :m người', ['b' => $type->beds, 'm' => $type->max_occupancy]) }}</p>
                            <p class="mt-2 text-2xl font-bold text-amber-300">{{ number_format($type->default_price, 0, ',', '.') }} ₫ <span class="text-sm font-normal text-slate-500">/ {{ __('đêm') }}</span></p>

                            @if($rooms->isNotEmpty())
                                <p class="mt-4 text-xs font-semibold uppercase tracking-wide text-slate-500">{{ __('Phòng khả dụng') }}</p>
                                <ul class="mt-2 space-y-2 text-sm">
                                    @foreach($rooms as $freeRoom)
                                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-white/5 bg-slate-800/60 px-3 py-2 text-slate-200">
                                            <span class="font-medium">{{ __('Phòng') }} {{ $freeRoom->code }}</span>
                                            @auth
                                                @if(auth()->user()->isCustomer())
                                                    <a href="{{ route('customer.bookings.create-room', $freeRoom) }}?{{ http_build_query(['check_in' => $check_in->toDateString(), 'check_out' => $check_out->toDateString(), 'guests' => $guests]) }}" @click.stop class="inline-flex rounded-full bg-gradient-to-r from-amber-700 to-amber-900 px-4 py-1.5 text-xs font-semibold text-white shadow-md shadow-amber-900/30 ring-1 ring-amber-500/25 transition hover:from-amber-600 hover:to-amber-800">{{ __('Đặt ngay') }}</a>
                                                @endif
                                            @else
                                                <a href="{{ route('login') }}" @click.stop class="inline-flex rounded-full border border-amber-500/40 px-4 py-1.5 text-xs font-semibold text-amber-200 transition hover:bg-amber-500/10">{{ __('Đăng nhập để đặt') }}</a>
                                            @endauth
                                        </li>
                                    @endforeach
                                </ul>
                            @endif

                            @auth
                                @if(auth()->user()->isCustomer())
                                    @if($avail > 0)
                                        <a href="{{ route('customer.bookings.create', ['room_type_id' => $type->id, 'check_in' => $check_in->toDateString(), 'check_out' => $check_out->toDateString(), 'guests' => $guests]) }}" @click.stop class="mt-4 inline-flex justify-center rounded-full border border-amber-500/40 px-6 py-2.5 text-center text-sm font-semibold text-amber-100 transition hover:bg-amber-500/10">{{ __('Đặt theo loại (lễ tân gán phòng)') }}</a>
                                    @else
                                        <p class="mt-6 text-sm font-medium text-rose-300">{{ __('Hết phòng trong khoảng thời gian đã chọn') }}</p>
                                    @endif
                                @else
                                    <p class="mt-6 text-sm text-slate-500">{{ __('Đăng nhập tài khoản khách để đặt online.') }}</p>
                                @endif
                            @else
                                <a href="{{ route('login') }}" @click.stop class="mt-6 inline-flex justify-center rounded-full border-2 border-amber-500/50 px-6 py-3 text-sm font-semibold text-amber-100 transition hover:bg-amber-500/10">{{ __('Đăng nhập để đặt') }}</a>
                            @endauth
                        </div>
                    </article>
                @endforeach
            </div>
        @elseif(($searchExecuted ?? false) && $check_in && $check_out)
            <p class="mt-10 text-center text-slate-400">{{ __('Không có loại phòng phù hợp. Thử đổi số khách hoặc ngày.') }}</p>
        @endif
    </div>
</x-guest-hotel-layout>
