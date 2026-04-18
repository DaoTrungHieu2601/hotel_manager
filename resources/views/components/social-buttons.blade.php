@php
    $s    = \App\Models\SiteSetting::instance();
    $show = $s->social_enabled ?? true;

    $facebook  = $s->social_facebook  ?: '';
    $zalo      = $s->social_zalo      ?: '';
    $phone     = $s->social_phone     ?: ($s->site_phone ?: '');
    $instagram = $s->social_instagram ?: '';

    $buttons = [];
    if ($facebook)  $buttons[] = ['href' => $facebook,       'label' => 'Facebook',  'color' => '#1877F2', 'svg' => 'facebook'];
    if ($zalo)      $buttons[] = ['href' => $zalo,           'label' => 'Zalo',      'color' => '#0068FF', 'svg' => 'zalo'];
    if ($phone)     $buttons[] = ['href' => 'tel:'.$phone,   'label' => 'Gọi ngay',  'color' => '#22c55e', 'svg' => 'phone'];
    if ($instagram) $buttons[] = ['href' => $instagram,      'label' => 'Instagram', 'color' => '#E1306C', 'svg' => 'instagram'];
@endphp

@if($show && count($buttons))
<style>
@keyframes soc-pulse {
    0%,100% { box-shadow: 0 0 0 0 var(--sc); }
    55%     { box-shadow: 0 0 0 9px transparent; }
}
.soc-wrap {
    position: fixed;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    z-index: 25;
    display: flex;
    flex-direction: column;
    gap: 10px;
    padding-right: 12px;
}
.soc-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 48px;
    height: 48px;
    border-radius: 14px;
    color: #fff;
    text-decoration: none;
    position: relative;
    animation: soc-pulse 2s ease-in-out infinite;
    transition: transform .2s ease;
    box-shadow: 0 4px 12px rgba(0,0,0,.35);
}
.soc-btn:hover { transform: scale(1.15) translateX(-4px); }
.soc-btn:nth-child(1){ animation-delay: 0s }
.soc-btn:nth-child(2){ animation-delay: .4s }
.soc-btn:nth-child(3){ animation-delay: .8s }
.soc-btn:nth-child(4){ animation-delay: 1.2s }
.soc-tip {
    display: none;
    position: absolute;
    right: calc(100% + 10px);
    top: 50%;
    transform: translateY(-50%);
    white-space: nowrap;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    color: #fff;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,.3);
}
.soc-btn:hover .soc-tip { display: block; }
</style>

<div class="soc-wrap">
    @foreach($buttons as $i => $btn)
    <a href="{{ $btn['href'] }}"
       target="{{ str_starts_with($btn['href'], 'tel:') ? '_self' : '_blank' }}"
       rel="noopener noreferrer"
       class="soc-btn"
       style="background: {{ $btn['color'] }}; --sc: {{ $btn['color'] }}99;"
       title="{{ $btn['label'] }}">

        {{-- Icons --}}
        @if($btn['svg'] === 'facebook')
            <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.49 0-1.956.93-1.956 1.887v2.267h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
            </svg>
        @elseif($btn['svg'] === 'zalo')
            <svg width="26" height="26" viewBox="0 0 64 64" fill="white">
                <path d="M32 6C17.64 6 6 17.64 6 32s11.64 26 26 26 26-11.64 26-26S46.36 6 32 6zm-2 36.5h-4V28h4v14.5zm-2-16.3a2.2 2.2 0 110-4.4 2.2 2.2 0 010 4.4zm18 16.3h-3.7l-6.8-9.3v9.3H31.8V28h3.7l6.8 9.3V28H46v14.5z"/>
            </svg>
        @elseif($btn['svg'] === 'phone')
            <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8a19.79 19.79 0 01-3.07-8.67A2 2 0 012 .18h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 7.91a16 16 0 006 6l1.09-1.09a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 14.92v2z"/>
            </svg>
        @elseif($btn['svg'] === 'instagram')
            <svg width="24" height="24" fill="white" viewBox="0 0 24 24">
                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zM12 0C8.741 0 8.333.014 7.053.072 2.695.272.273 2.69.073 7.052.014 8.333 0 8.741 0 12c0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98C8.333 23.986 8.741 24 12 24c3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98C15.668.014 15.259 0 12 0zm0 5.838a6.162 6.162 0 100 12.324 6.162 6.162 0 000-12.324zM12 16a4 4 0 110-8 4 4 0 010 8zm6.406-11.845a1.44 1.44 0 100 2.881 1.44 1.44 0 000-2.881z"/>
            </svg>
        @endif

        <span class="soc-tip" style="background: {{ $btn['color'] }};">{{ $btn['label'] }}</span>
    </a>
    @endforeach
</div>
@endif
