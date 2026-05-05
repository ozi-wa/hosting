<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\BillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(): View
    {
        return view('admin.invoices.index', ['invoices' => Invoice::with('user')->latest()->paginate(20)]);
    }

    public function show(Invoice $invoice): View
    {
        return view('admin.invoices.show', ['invoice' => $invoice->load(['user', 'order.product', 'payments'])]);
    }

    public function markPaid(Invoice $invoice, BillingService $billing): RedirectResponse
    {
        abort_if($invoice->status === 'paid', 422);
        $billing->markPaid($invoice);

        return back()->with('status', 'Fatura ödendi olarak işaretlendi.');
    }
}
