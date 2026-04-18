@props([
    'type',
    'variant' => 'search',
])

@php
    $isHome = $variant === 'home';
    $tabBar = $isHome ? 'flex flex-wrap gap-1 rounded-xl bg-black/35 p-1 ring-1 ring-white/10' : 'flex flex-wrap gap-1 rounded-xl bg-slate-800/70 p-1 ring-1 ring-white/10';
    $tabBtn = 'flex-1 min-w-[5.5rem] rounded-lg px-2 py-2 text-center text-[11px] font-semibold leading-tight transition sm:text-xs';
    $panel = 'mt-3 text-sm leading-relaxed text-slate-300';
    /** Trang chủ: position absolute → không chiếm chỗ trong layout, không đẩy cao hero/nền; max-h chỉ giới hạn khối, dài thì cuộn trong khối */
    $panelHomeFloat = 'absolute left-0 right-0 top-full z-[100] mt-2 max-h-[min(90vh,28rem)] overflow-y-auto overscroll-contain rounded-xl border border-white/15 bg-stone-950/95 p-3 text-sm leading-relaxed text-stone-200/95 shadow-2xl shadow-black/60 ring-1 ring-white/10 backdrop-blur-md [scrollbar-gutter:stable]';
    $empty = __('Chưa có thông tin cho mục này.');
    $facLines = \App\Models\RoomType::linesFromText($type->facilities);
    $amenLines = \App\Models\RoomType::linesFromText($type->amenities);
@endphp

<div {{ $attributes->merge(['class' => $isHome ? 'relative' : '']) }}>
    <div class="{{ $tabBar }}" role="tablist" aria-label="{{ __('Chi tiết loại phòng') }}">
        @if($isHome)
            <span role="tab" aria-selected="false" @mouseenter="tab = 'desc'" :class="tab === 'desc' ? 'bg-amber-500/25 text-amber-50 ring-1 ring-amber-400/35' : 'text-stone-400'" class="{{ $tabBtn }}">{{ __('Mô tả') }}</span>
            <span role="tab" aria-selected="false" @mouseenter="tab = 'facilities'" :class="tab === 'facilities' ? 'bg-amber-500/25 text-amber-50 ring-1 ring-amber-400/35' : 'text-stone-400'" class="{{ $tabBtn }}">{{ __('Tiện nghi') }}</span>
            <span role="tab" aria-selected="false" @mouseenter="tab = 'amenities'" :class="tab === 'amenities' ? 'bg-amber-500/25 text-amber-50 ring-1 ring-amber-400/35' : 'text-stone-400'" class="{{ $tabBtn }}">{{ __('Tiện ích') }}</span>
        @else
            <button type="button" role="tab" @mouseenter="tab = 'desc'" @focus="tab = 'desc'" :aria-selected="tab === 'desc'" :class="tab === 'desc' ? 'bg-violet-500/25 text-violet-100 ring-1 ring-violet-400/35' : 'text-slate-500 hover:text-slate-300'" class="{{ $tabBtn }}">{{ __('Mô tả') }}</button>
            <button type="button" role="tab" @mouseenter="tab = 'facilities'" @focus="tab = 'facilities'" :aria-selected="tab === 'facilities'" :class="tab === 'facilities' ? 'bg-violet-500/25 text-violet-100 ring-1 ring-violet-400/35' : 'text-slate-500 hover:text-slate-300'" class="{{ $tabBtn }}">{{ __('Tiện nghi') }}</button>
            <button type="button" role="tab" @mouseenter="tab = 'amenities'" @focus="tab = 'amenities'" :aria-selected="tab === 'amenities'" :class="tab === 'amenities' ? 'bg-violet-500/25 text-violet-100 ring-1 ring-violet-400/35' : 'text-slate-500 hover:text-slate-300'" class="{{ $tabBtn }}">{{ __('Tiện ích') }}</button>
        @endif
    </div>

    @if($isHome)
        <div class="{{ $panelHomeFloat }}" x-cloak>
            <div x-show="tab === 'desc'" x-transition>
                @if($type->description)
                    <p class="whitespace-pre-line">{{ $type->description }}</p>
                @else
                    <p class="text-stone-500">{{ $empty }}</p>
                @endif
            </div>

            <div x-show="tab === 'facilities'" x-transition>
                @if(count($facLines))
                    <ul class="list-inside list-disc space-y-1.5 marker:text-amber-400/80">
                        @foreach($facLines as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-stone-500">{{ $empty }}</p>
                @endif
            </div>

            <div x-show="tab === 'amenities'" x-transition>
                @if(count($amenLines))
                    <ul class="list-inside list-disc space-y-1.5 marker:text-amber-400/80">
                        @foreach($amenLines as $line)
                            <li>{{ $line }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-stone-500">{{ $empty }}</p>
                @endif
            </div>
        </div>
    @else
        <div class="{{ $panel }}" x-show="tab === 'desc'" x-transition x-cloak>
            @if($type->description)
                <p class="whitespace-pre-line">{{ $type->description }}</p>
            @else
                <p class="text-slate-500">{{ $empty }}</p>
            @endif
        </div>

        <div class="{{ $panel }}" x-show="tab === 'facilities'" x-transition x-cloak>
            @if(count($facLines))
                <ul class="list-inside list-disc space-y-1.5 marker:text-amber-400/80">
                    @foreach($facLines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-500">{{ $empty }}</p>
            @endif
        </div>

        <div class="{{ $panel }}" x-show="tab === 'amenities'" x-transition x-cloak>
            @if(count($amenLines))
                <ul class="list-inside list-disc space-y-1.5 marker:text-amber-400/80">
                    @foreach($amenLines as $line)
                        <li>{{ $line }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-slate-500">{{ $empty }}</p>
            @endif
        </div>
    @endif
</div>
