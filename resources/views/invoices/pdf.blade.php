<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111;
            padding: 32px 40px;
            background: #fff;
        }
        .header-wrap { width: 100%; margin-bottom: 14px; }
        .hotel-name  { font-size: 15px; font-weight: bold; text-align: center; margin-bottom: 3px; }
        .hotel-info  { font-size: 10px; text-align: center; line-height: 1.75; color: #333; }
        .page-title {
            text-align: center;
            font-size: 17px;
            font-weight: bold;
            letter-spacing: 3px;
            margin: 14px 0 10px;
        }
        .meta-row { width: 100%; font-size: 10.5px; margin-bottom: 10px; }
        .meta-row td { padding: 1px 4px 1px 0; }
        .info-table { width: 100%; border-collapse: collapse; margin-top: 4px; }
        .info-table td {
            border: 1px solid #bbb;
            padding: 5px 9px;
            vertical-align: middle;
        }
        .lbl  { font-size: 10px; color: #555; width: 18%; }
        .val  { font-weight: bold; font-size: 11px; width: 32%; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        .items-table th {
            background: #f0f0f0;
            border: 1px solid #bbb;
            padding: 6px 8px;
            text-align: center;
            font-size: 11px;
        }
        .items-table td {
            border: 1px solid #ccc;
            padding: 5px 8px;
            font-size: 11px;
            vertical-align: top;
        }
        .tc  { text-align: center; }
        .tr  { text-align: right; }
        .sub-note { font-size: 9.5px; color: #555; font-style: italic; margin-top: 2px; }
        .totals-table { width: 100%; border-collapse: collapse; }
        .totals-table td {
            border: 1px solid #ccc;
            padding: 5px 10px;
            font-size: 11px;
        }
        .totals-table .t-lbl  { width: 72%; text-align: right; color: #444; }
        .totals-table .t-val  { width: 28%; text-align: right; font-weight: bold; }
        .totals-table .grand  .t-lbl,
        .totals-table .grand  .t-val { font-weight: bold; font-size: 12px; }
        .totals-table .remain .t-lbl,
        .totals-table .remain .t-val { font-weight: bold; }
        .sig-table { width: 100%; margin-top: 44px; }
        .sig-table td { text-align: center; width: 50%; padding-top: 0; }
        .sig-title { font-weight: bold; font-size: 11px; }
        .sig-space { height: 54px; }
        .sig-name  { font-size: 11px; }
        hr { border: none; border-top: 1px solid #bbb; margin: 2px 0; }
    </style>
</head>
<body>

@php
    $booking  = $invoice->booking;
    $siteName = $setting->displayName();
    $pmMap = [
        'bank_transfer' => 'Chuyen khoan',
        'cash'          => 'Tien mat',
        'vnpay'         => 'VNPay',
        'momo'          => 'MoMo',
    ];
    $pmLabel = $pmMap[$booking->payment_method ?? ''] ?? ucfirst($booking->payment_method ?? 'Tien mat');
    $ratePerNight = (float) ($booking->rate_per_night ?? 0);
    if ($ratePerNight == 0 && $invoice->nights > 0) {
        $ratePerNight = (float) $invoice->room_subtotal / $invoice->nights;
    }
    $paid      = (float) $invoice->deposit;
    $remaining = (float) $invoice->total - $paid;
@endphp

<table class="header-wrap" cellpadding="0" cellspacing="0">
    <tr>
        <td style="text-align:center;">
            <div class="hotel-name">{{ $siteName }}</div>
            <div class="hotel-info">
                @if($setting->site_address){{ $setting->site_address }}<br>@endif
                @if($setting->site_phone || $setting->site_email)
                    @if($setting->site_phone)<strong>T</strong> {{ $setting->site_phone }}@endif
                    @if($setting->site_phone && $setting->site_email) &nbsp;&nbsp; @endif
                    @if($setting->site_email)<strong>E</strong> {{ $setting->site_email }}@endif
                    <br>
                @endif
                @if($setting->site_website){{ $setting->site_website }}@endif
            </div>
        </td>
    </tr>
</table>

<hr>

<div class="page-title">HOA DON</div>

<table class="meta-row" cellpadding="0" cellspacing="0">
    <tr>
        <td><strong>So hoa don:</strong> {{ $invoice->invoice_number }}</td>
        <td><strong>Ngay:</strong> {{ $invoice->issued_at->format('d/m/Y H:i') }}</td>
        <td><strong>Ma dat phong:</strong> {{ str_pad($booking->id, 6, '0', STR_PAD_LEFT) }}</td>
    </tr>
</table>

<table class="info-table" cellpadding="0" cellspacing="0">
    <tr>
        <td class="lbl">Khach hang</td>
        <td class="val">{{ $booking->user->name }}</td>
        <td class="lbl">Phong</td>
        <td class="val">{{ $booking->room?->code ?? '—' }}</td>
    </tr>
    <tr>
        <td class="lbl">Ngay nhan phong</td>
        <td class="val">{{ $booking->check_in->format('d/m/Y') }}</td>
        <td class="lbl">Thoi gian den</td>
        <td class="val">{{ $booking->checked_in_at?->format('H:i') ?? ($booking->guest_planned_check_in ?? '—') }}</td>
    </tr>
    <tr>
        <td class="lbl">Ngay tra phong</td>
        <td class="val">{{ $booking->check_out->format('d/m/Y') }}</td>
        <td class="lbl">Thoi gian tra</td>
        <td class="val">{{ $booking->checked_out_at?->format('H:i') ?? ($booking->guest_planned_check_out ?? '—') }}</td>
    </tr>
    <tr>
        <td class="lbl">Loai phong</td>
        <td class="val">{{ $booking->roomType->name }}</td>
        <td class="lbl">So dem luu tru</td>
        <td class="val">{{ $invoice->nights }}</td>
    </tr>
</table>

<table class="items-table" cellpadding="0" cellspacing="0">
    <thead>
        <tr>
            <th style="width:4%">#</th>
            <th style="width:13%">Ngay</th>
            <th style="width:10%">Phong</th>
            <th>Noi dung</th>
            <th style="width:10%">So luong</th>
            <th style="width:16%">Don gia</th>
            <th style="width:16%">Thanh tien</th>
        </tr>
    </thead>
    <tbody>
        @for ($n = 0; $n < $invoice->nights; $n++)
        @php
            $night = $booking->check_in->copy()->addDays($n);
            $rowTotal = $ratePerNight;
        @endphp
        <tr>
            <td class="tc">{{ $n + 1 }}</td>
            <td class="tc">{{ $night->format('d/m/Y') }}</td>
            <td class="tc">{{ $booking->room?->code ?? '—' }}</td>
            <td>
                Tien phong
                @if($booking->roomType->name)<div class="sub-note">{{ $booking->roomType->name }}</div>@endif
            </td>
            <td class="tc">1</td>
            <td class="tr">{{ number_format($rowTotal, 0, ',', '.') }} VND</td>
            <td class="tr">{{ number_format($rowTotal, 0, ',', '.') }} VND</td>
        </tr>
        @endfor

        @foreach($booking->bookingServices as $line)
        <tr>
            <td class="tc">{{ $invoice->nights + $loop->iteration }}</td>
            <td class="tc">—</td>
            <td class="tc">—</td>
            <td>{{ $line->service->name }}</td>
            <td class="tc">{{ $line->quantity }}</td>
            <td class="tr">{{ number_format((float) $line->unit_price, 0, ',', '.') }} VND</td>
            <td class="tr">{{ number_format((float) $line->lineTotal(), 0, ',', '.') }} VND</td>
        </tr>
        @endforeach
        @if((float) ($invoice->early_late_subtotal ?? 0) > 0)
        @php $svcBase = $invoice->nights + $booking->bookingServices->count(); @endphp
        <tr>
            <td class="tc">{{ $svcBase + 1 }}</td>
            <td class="tc">—</td>
            <td class="tc">—</td>
            <td>Phu phi nhan / tra phong ngoai gio</td>
            <td class="tc">1</td>
            <td class="tr">{{ number_format((float) $invoice->early_late_subtotal, 0, ',', '.') }} VND</td>
            <td class="tr">{{ number_format((float) $invoice->early_late_subtotal, 0, ',', '.') }} VND</td>
        </tr>
        @endif
    </tbody>
</table>

<table class="totals-table" cellpadding="0" cellspacing="0">
    <tr class="grand">
        <td class="t-lbl">Tong tien</td>
        <td class="t-val">{{ number_format((float) $invoice->total, 0, ',', '.') }} VND</td>
    </tr>
    @if($paid > 0)
    <tr>
        <td class="t-lbl">Thanh toan #1 <em>({{ $pmLabel }})</em></td>
        <td class="t-val">{{ number_format($paid, 0, ',', '.') }} VND</td>
    </tr>
    @endif
    <tr class="remain">
        <td class="t-lbl">So tien con lai</td>
        <td class="t-val">{{ number_format(max(0, $remaining), 0, ',', '.') }} VND</td>
    </tr>
</table>

<table class="sig-table" cellpadding="0" cellspacing="0">
    <tr>
        <td>
            <div class="sig-title">Le tan</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $booking->room?->responsibleStaff?->name ?? '' }}</div>
        </td>
        <td>
            <div class="sig-title">Khach hang</div>
            <div class="sig-space"></div>
            <div class="sig-name">{{ $booking->user->name }}</div>
        </td>
    </tr>
</table>

</body>
</html>
