<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use App\Services\Whmcs\WhmcsGateway;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private readonly BillingService $billing,
        private readonly WhmcsGateway $whmcs,
    ) {}

    public function place(User $user, Product $product, string $billingCycle, array $metadata = []): Order
    {
        return DB::transaction(function () use ($user, $product, $billingCycle, $metadata): Order {
            $subtotal = (float) $product->priceFor($billingCycle);
            $tax = round($subtotal * 0.20, 2);
            $whmcsOrder = $this->whmcs->enabled()
                ? $this->whmcs->createOrder($user, $product, $billingCycle, $metadata)
                : [];

            $order = Order::create([
                'number' => $this->nextNumber('ORD'),
                'whmcs_order_id' => $whmcsOrder['orderid'] ?? null,
                'user_id' => $user->id,
                'product_id' => $product->id,
                'billing_cycle' => $billingCycle,
                'status' => 'pending_payment',
                'subtotal' => $subtotal,
                'tax_total' => $tax,
                'total' => $subtotal + $tax,
                'currency' => $product->currency,
                'metadata' => $metadata + ['whmcs' => $whmcsOrder],
                'ordered_at' => now(),
            ]);

            Service::create([
                'number' => $this->nextNumber('SRV'),
                'whmcs_service_id' => $this->whmcs->firstId($whmcsOrder['serviceids'] ?? null),
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_id' => $order->id,
                'status' => 'pending',
                'domain' => $metadata['domain'] ?? null,
                'next_due_at' => $billingCycle === 'yearly' ? now()->addYear()->toDateString() : now()->addMonth()->toDateString(),
                'metadata' => [
                    'provisioning' => $this->whmcs->enabled() ? 'whmcs' : 'manual_queue',
                    'whmcs' => $whmcsOrder,
                ],
            ]);

            $this->billing->createInvoiceForOrder($order->load('product'), $whmcsOrder);

            return $order->load(['product', 'invoice', 'service']);
        });
    }

    private function nextNumber(string $prefix): string
    {
        return $prefix.'-'.now()->format('Ymd').'-'.str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
    }
}
