<x-mail::message>
# {{ __('Xác nhận đặt phòng') }}

{{ __('Xin chào :name,', ['name' => $booking->user->name]) }}

{{ __('Đơn đặt phòng #:id của bạn đã được xác nhận.', ['id' => $booking->id]) }}

## {{ __('Thông tin khách hàng') }}

- **{{ __('Họ và tên') }}:** {{ $booking->user->name }}
- **{{ __('Email') }}:** {{ $booking->user->email }}
- **{{ __('Số điện thoại') }}:** {{ $booking->user->phone ?: '—' }}
- **{{ __('CCCD') }}:** {{ $booking->user->cccd ?: '—' }}

## {{ __('Thông tin đơn đặt phòng') }}

- **{{ __('Mã đơn') }}:** #{{ $booking->id }}
- **{{ __('Loại phòng') }}:** {{ $booking->roomType->name }}
- **{{ __('Phòng') }}:** {{ $booking->room?->code ?? '—' }}
- **{{ __('Nhận phòng') }}:** {{ $booking->check_in->translatedFormat('d/m/Y') }}
- **{{ __('Trả phòng') }}:** {{ $booking->check_out->translatedFormat('d/m/Y') }}
- **{{ __('Số khách') }}:** {{ $booking->guests }}
- **{{ __('Đơn giá / đêm') }}:** {{ number_format((float) ($booking->rate_per_night ?? $booking->roomType->default_price), 0, ',', '.') }} VND
- **{{ __('Tiền cọc') }}:** {{ number_format((float) $booking->deposit_amount, 0, ',', '.') }} VND
- **{{ __('Ghi chú của khách') }}:** {{ $booking->guest_notes ?: '—' }}

@php
    $nights = $booking->nights();
    $rate = (float) ($booking->rate_per_night ?? $booking->roomType->default_price);
    $roomSubtotal = max(0, $nights * $rate);
@endphp
- **{{ __('Tạm tính tiền phòng') }}:** {{ number_format($roomSubtotal, 0, ',', '.') }} VND

{{ __('Hẹn gặp bạn tại khách sạn!') }}

{{ config('app.name') }}
</x-mail::message>
