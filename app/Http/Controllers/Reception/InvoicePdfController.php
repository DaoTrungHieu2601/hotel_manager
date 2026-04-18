<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\SiteSetting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class InvoicePdfController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice): Response
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            abort_unless($invoice->booking->user_id === $user->id, 403);
        } else {
            abort_unless($user->isStaff(), 403);
        }

        // Guard: hóa đơn tiền mặt chỉ được in sau khi đã hoàn tất check-out.
        if ($invoice->booking->payment_method === 'cash' && $invoice->booking->status !== \App\Models\Booking::STATUS_CHECKED_OUT) {
            abort(403, __('Cần xác nhận đã thu tiền mặt trước khi in hóa đơn.'));
        }

        $invoice->load(['booking.user', 'booking.roomType', 'booking.room', 'booking.bookingServices.service']);

        $setting = SiteSetting::instance();
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'setting' => $setting,
        ])
            ->setPaper('a4', 'portrait');

        return $pdf->download($invoice->invoice_number.'.pdf');
    }
}
