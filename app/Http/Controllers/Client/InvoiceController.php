<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        return view('client.invoices.index', ['invoices' => $request->user()->invoices()->latest()->paginate(15)]);
    }

    public function show(Request $request, Invoice $invoice): View
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        return view('client.invoices.show', ['invoice' => $invoice->load(['order.product', 'payments'])]);
    }
}
