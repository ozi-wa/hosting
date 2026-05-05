<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\Whmcs\WhmcsProjectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request, WhmcsProjectionService $whmcs): JsonResponse
    {
        $whmcs->syncInvoices($request->user());

        return response()->json(['data' => $request->user()->invoices()->latest()->get()]);
    }

    public function show(Request $request, Invoice $invoice): JsonResponse
    {
        abort_unless($invoice->user_id === $request->user()->id, 403);

        return response()->json(['data' => $invoice->load(['order.product', 'payments'])]);
    }
}
