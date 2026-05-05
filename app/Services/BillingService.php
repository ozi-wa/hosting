<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class BillingService
{
    public function createInvoiceForOrder(Order $order, array $whmcsOrder = []): Invoice
    {
        return Invoice::create([
            'number' => $this->nextNumber('INV'),
            'whmcs_invoice_id' => $whmcsOrder['invoiceid'] ?? null,
            'user_id' => $order->user_id,
            'order_id' => $order->id,
            'status' => 'unpaid',
            'subtotal' => $order->subtotal,
            'tax_total' => $order->tax_total,
            'total' => $order->total,
            'currency' => $order->currency,
            'issued_at' => now()->toDateString(),
            'due_at' => now()->addDays(7)->toDateString(),
            'line_items' => [[
                'description' => $order->product->name,
                'billing_cycle' => $order->billing_cycle,
                'quantity' => 1,
                'unit_price' => $order->subtotal,
                'total' => $order->total,
            ]],
        ]);
    }

    public function markPaid(Invoice $invoice, string $provider = 'manual', ?string $transactionId = null): Payment
    {
        return DB::transaction(function () use ($invoice, $provider, $transactionId): Payment {
            $payment = Payment::create([
                'invoice_id' => $invoice->id,
                'user_id' => $invoice->user_id,
                'provider' => $provider,
                'transaction_id' => $transactionId,
                'status' => 'paid',
                'amount' => $invoice->total,
                'currency' => $invoice->currency,
                'paid_at' => now(),
            ]);

            $invoice->update([
                'status' => 'paid',
                'paid_total' => $invoice->total,
                'paid_at' => now(),
            ]);

            $invoice->order?->service?->update([
                'status' => 'active',
                'activated_at' => now(),
            ]);

            $invoice->order?->update(['status' => 'active']);

            return $payment;
        });
    }

    private function nextNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
