<x-guest-hotel-layout>
    <div class="relative w-full overflow-x-hidden overflow-y-visible">
        <div class="pointer-events-none absolute inset-0 z-0 bg-cover bg-center bg-no-repeat" style="background-image: url('{{ $homeHeroBgUrl }}')"></div>
        <div class="pointer-events-none absolute inset-0 z-0 bg-gradient-to-br from-amber-950/55 via-stone-900/65 to-amber-950/60"></div>
        <div class="pointer-events-none absolute inset-0 z-0 opacity-30" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.05\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <section class="relative z-30 flex min-h-[min(100dvh,56rem)] flex-col text-white">
        <div class="relative mx-auto flex w-full max-w-7xl flex-1 flex-col justify-center px-4 pb-36 pt-20 sm:px-6 sm:pb-44 lg:flex-row lg:items-center lg:gap-16 lg:px-8 lg:pb-52 lg:pt-28">
            <div class="max-w-xl">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-200/90">{{ __('Trải nghiệm lưu trú') }}</p>
                <h1 class="mt-4 font-display text-4xl font-bold leading-tight sm:text-5xl">{{ __('Khách sạn của bạn, một chạm để đặt phòng') }}</h1>
                <p class="mt-6 text-lg text-stone-200/90">{{ __('Chọn ngày, số khách — hiển thị phòng trống thật. Hóa đơn PDF khi trả phòng.') }}</p>
                <form
                    method="get"
                    action="{{ route('guest.search-rooms') }}"
                    class="mt-10 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-end"
                    x-data="{
                        checkIn: {{ \Illuminate\Support\Js::from(now()->addDay()->toDateString()) }},
                        checkOut: {{ \Illuminate\Support\Js::from(now()->addDays(2)->toDateString()) }},
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
                    <div class="min-w-[140px] flex-1">
                        <label class="block text-xs font-semibold text-amber-100/90">{{ __('Ngày nhận phòng') }}</label>
                        <input type="date" name="check_in" x-model="checkIn" @change="syncCheckoutAfterCheckIn()" class="mt-1 w-full rounded-xl border-0 bg-white/95 px-3 py-2.5 text-slate-900 shadow-sm" required />
                    </div>
                    <div class="min-w-[140px] flex-1">
                        <label class="block text-xs font-semibold text-amber-100/90">{{ __('Ngày trả phòng') }}</label>
                        <input type="date" name="check_out" x-model="checkOut" :min="minCheckout()" class="mt-1 w-full rounded-xl border-0 bg-white/95 px-3 py-2.5 text-slate-900 shadow-sm" required />
                    </div>
                    <div class="min-w-[100px] w-full sm:w-28">
                        <label class="block text-xs font-semibold text-amber-100/90">{{ __('Số người') }}</label>
                        <input type="number" name="guests" min="1" value="2" class="mt-1 w-full rounded-xl border-0 bg-white/95 px-3 py-2.5 text-slate-900 shadow-sm" required />
                    </div>
                    <button type="submit" class="inline-flex w-full justify-center rounded-full bg-white px-8 py-3.5 text-sm font-semibold text-amber-950 shadow-lg transition hover:bg-amber-100 sm:w-auto">
                        {{ __('Tìm phòng ngay') }}
                    </button>
                </form>
            </div>
            <div class="mt-14 min-h-0 flex-1 overflow-visible lg:mt-0">
                <div class="overflow-visible rounded-3xl border border-white/10 bg-white/5 p-6 shadow-2xl backdrop-blur-xl">
                    <div
                        class="overflow-visible"
                        x-data="{ openId: null, tab: 'desc' }"
                        @mouseleave="openId = null"
                    >
                    <p class="text-sm font-medium text-amber-100">{{ __('Nổi bật') }}</p>
                    <div class="mt-4 grid min-w-0 items-start gap-4 overflow-visible sm:grid-cols-3">
                        @foreach($featuredTypes as $type)
                            <div
                                @mouseenter="openId = {{ $type->id }}; tab = 'desc'"
                                class="guest-home-lift min-w-0 overflow-visible rounded-2xl border border-transparent bg-black/20 text-left ring-1 ring-white/10 transition-all duration-200 ease-out"
                                :class="openId === {{ $type->id }} ? 'relative z-30 shadow-2xl shadow-black/50 ring-amber-400/40' : ''"
                            >
                                <div class="rounded-t-2xl p-4">
                                    <p class="font-display text-lg font-semibold">{{ $type->name }}</p>
                                    <p class="mt-1 text-2xl font-bold tabular-nums leading-none text-amber-200">
                                        <span class="inline-block whitespace-nowrap">{{ number_format($type->default_price, 0, ',', '.') }}&nbsp;đ</span>
                                    </p>
                                    <p class="mt-2 text-xs text-stone-300">{{ __('Tối đa :n người', ['n' => $type->max_occupancy]) }}</p>
                                </div>
                                <div x-show="openId === {{ $type->id }}" x-cloak x-transition class="relative overflow-visible border-t border-white/10 px-4 pb-3 pt-2">
                                    <x-room-type-detail-tabs :type="$type" variant="home" />
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-slate-950/75 py-14 text-slate-100 backdrop-blur-md sm:py-18">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400/90">{{ __('Tiện ích & Dịch vụ') }}</p>
                <h2 class="mt-2 font-display text-3xl font-bold text-white sm:text-4xl">{{ $facilitiesTitle }}</h2>
            </div>
            <div class="mt-10 grid gap-5 md:grid-cols-2">
                @foreach($facilitiesItems as $item)
                    <article class="group relative min-h-[260px] overflow-hidden rounded-2xl border border-white/15 bg-slate-900/70 p-6 shadow-xl shadow-black/20">
                        <div class="absolute inset-0 bg-cover bg-center transition duration-500 group-hover:scale-105" style="background-image: url('{{ $item['image'] ?? '' }}');"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-slate-950/90 via-slate-950/45 to-slate-900/20"></div>
                        <div class="relative z-10">
                            <h3 class="text-xl font-semibold text-white drop-shadow">{{ $item['title'] ?? '' }}</h3>
                            <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-100/95">{{ $item['desc'] ?? '' }}</p>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-gradient-to-b from-slate-950/70 to-stone-950/70 py-14 backdrop-blur-md sm:py-18">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400/90">{{ __('Vị trí & Khám phá') }}</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">{{ $locationTitle }}</h2>
                <p class="mt-4 text-slate-300">{{ $locationDescription }}</p>
            </div>
            <div class="mt-10 grid gap-5 lg:grid-cols-2">
                <div class="overflow-hidden rounded-2xl border border-white/10 bg-slate-900/60">
                    <iframe
                        title="EAUT Hotel location"
                        src="{{ $locationMapUrl }}"
                        style="width:100%;height:360px;border:0;display:block;filter:grayscale(40%) contrast(120%);"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                    ></iframe>
                </div>
                <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-6">
                    <h3 class="text-lg font-semibold text-white">{{ __('Khoảng cách tham khảo') }}</h3>
                    <ul class="mt-4 space-y-3 text-sm text-slate-200">
                        @foreach($locationDistances as $distance)
                            <li class="rounded-xl border border-white/10 bg-white/[0.03] px-4 py-3">{{ $distance }}</li>
                        @endforeach
                    </ul>
                    <a href="{{ $locationCtaLink }}" class="mt-6 inline-flex rounded-full bg-gradient-to-r from-amber-600 to-amber-800 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-amber-900/40 ring-1 ring-amber-500/30 transition hover:from-amber-500 hover:to-amber-700">
                        {{ $locationCtaLabel }}
                    </a>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-slate-950/80 py-14 backdrop-blur-md sm:py-18">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mx-auto max-w-3xl text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400/90">{{ __('Offers & Packages') }}</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">{{ $offersTitle }}</h2>
            </div>
            <div class="mt-10 grid gap-5 lg:grid-cols-3">
                @foreach($offersItems as $offer)
                    <article class="rounded-2xl border border-white/10 bg-white/[0.03] p-6">
                        <h3 class="text-xl font-semibold text-white">{{ $offer['title'] ?? '' }}</h3>
                        <p class="mt-3 text-sm text-slate-300">{{ $offer['subtitle'] ?? '' }}</p>
                        <p class="mt-4 text-sm leading-relaxed text-slate-200">{{ $offer['benefits'] ?? '' }}</p>
                        <a href="{{ route('guest.search-rooms') }}" class="mt-6 inline-flex rounded-full bg-amber-500 px-5 py-2.5 text-sm font-semibold text-amber-950 transition hover:bg-amber-400">{{ __('Nhận ưu đãi') }}</a>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-stone-950/75 py-14 backdrop-blur-md sm:py-18" x-data="{ i: 0, items: @js($testimonialsItems) }" x-init="setInterval(() => i = (i + 1) % items.length, 5000)">
        <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400/90">{{ __('Testimonials') }}</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">{{ $testimonialsTitle }}</h2>
            </div>
            <div class="mt-10 rounded-2xl border border-white/10 bg-white/[0.03] p-8 text-center">
                <p class="text-amber-300">★★★★★</p>
                <p class="mt-4 text-base leading-relaxed text-slate-200" x-text="items[i].quote"></p>
                <p class="mt-5 text-sm font-semibold text-white" x-text="items[i].author"></p>
                <p class="text-xs text-slate-400" x-text="items[i].role"></p>
                <div class="mt-5 flex justify-center gap-2">
                    <template x-for="(item, idx) in items" :key="idx">
                        <button type="button" class="h-2.5 w-2.5 rounded-full transition" :class="idx === i ? 'bg-amber-400' : 'bg-slate-600'" @click="i = idx"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-slate-950/75 py-14 backdrop-blur-md sm:py-18" x-data="{ open: 0 }">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-amber-400/90">{{ __('FAQ') }}</p>
                <h2 class="mt-3 font-display text-3xl font-bold text-white sm:text-4xl">{{ $faqTitle }}</h2>
            </div>
            <div class="mt-10 space-y-3">
                @foreach($faqItems as $idx => $faq)
                    <div class="rounded-2xl border border-white/10 bg-white/[0.03]">
                        <button type="button" class="flex w-full items-center justify-between px-5 py-4 text-left text-sm font-semibold text-white" @click="open = open === {{ $idx }} ? -1 : {{ $idx }}">
                            <span>{{ $faq['q'] ?? '' }}</span><span class="text-amber-300" x-text="open === {{ $idx }} ? '−' : '+'"></span>
                        </button>
                        <div x-show="open === {{ $idx }}" x-transition class="px-5 pb-4 text-sm leading-relaxed text-slate-300">{{ $faq['a'] ?? '' }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="relative z-10 border-t border-white/10 bg-stone-950/75 py-16 backdrop-blur-md sm:py-20">
        <div class="mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8">
            <h2 class="font-display text-2xl font-bold text-white sm:text-3xl">{{ __('Sẵn sàng cho chuyến đi của bạn?') }}</h2>
            <p class="mt-3 text-slate-400">{{ __('Tìm phòng phù hợp với ngày và số khách — chỉ vài bước.') }}</p>
            <a
                href="{{ route('guest.search-rooms') }}"
                class="mt-8 inline-flex rounded-full bg-gradient-to-r from-amber-600 to-amber-800 px-10 py-3.5 text-sm font-semibold text-white shadow-lg shadow-amber-900/40 ring-1 ring-amber-500/30 transition hover:from-amber-500 hover:to-amber-700"
            >
                {{ __('Tìm phòng ngay') }}
            </a>
        </div>
    </section>
    </div>
</x-guest-hotel-layout>
