<x-mail::message>
# {{ __('Đơn đặt phòng mới chờ xác nhận') }}

{{ __('Khách :name vừa gửi đơn đặt phòng và đang chờ lễ tân xác nhận.', ['name' => $booking->user->name]) }}

## {{ __('Thông tin đơn') }}

- **{{ __('Mã đơn') }}:** #{{ $booking->id }}
- **{{ __('Loại phòng') }}:** {{ $booking->roomType->name }}
- **{{ __('Phòng') }}:** {{ $booking->room?->code ?? '—' }}
- **{{ __('Nhận phòng') }}:** {{ $booking->check_in->translatedFormat('d/m/Y') }}
- **{{ __('Trả phòng') }}:** {{ $booking->check_out->translatedFormat('d/m/Y') }}
- **{{ __('Số khách') }}:** {{ $booking->guests }}
- **{{ __('Email khách') }}:** {{ $booking->user->email }}

<x-mail::button :url="$url">
{{ __('Mở chi tiết đơn') }}
</x-mail::button>

{{ config('app.name') }}
</x-mail::message>
