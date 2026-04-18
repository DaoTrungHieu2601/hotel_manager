<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceShowController extends Controller
{
    public function __invoke(Request $request, Invoice $invoice): View
    {
        $user = $request->user();
        if ($user->isCustomer()) {
            abort_unless($invoice->booking->user_id === $user->id, 403);
        } else {
            abort_unless($user->isStaff(), 403);
        }

        $invoice->load(['booking.user', 'booking.roomType', 'booking.room', 'booking.bookingServices.service']);

        return view('invoices.show', compact('invoice'));
    }
}

