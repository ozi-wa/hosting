<?php

namespace App\Services\Whmcs;

use App\Models\Invoice;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Carbon;

class WhmcsProjectionService
{
    public function __construct(private readonly WhmcsGateway $whmcs) {}

    public function syncClient(User $user): void
    {
        if (! $this->whmcs->enabled()) {
            return;
        }

        $this->syncServices($user);
        $this->syncInvoices($user);
    }

    public function syncServices(User $user): void
    {
        foreach ($this->whmcs->clientProducts($user) as $remoteService) {
            $serviceId = $this->whmcs->firstId($remoteService['id'] ?? null);
            $product = Product::where('whmcs_product_id', $remoteService['pid'] ?? null)->first();

            if (! $serviceId || ! $product) {
                continue;
            }

            Service::updateOrCreate(
                ['whmcs_service_id' => $serviceId],
                [
                    'number' => 'WHMCS-SRV-'.$serviceId,
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'status' => strtolower($remoteService['status'] ?? 'unknown'),
                    'domain' => $remoteService['domain'] ?? null,
                    'server_hostname' => $remoteService['serverhostname'] ?? null,
                    'server_ip' => $remoteService['dedicatedip'] ?? null,
                    'next_due_at' => $this->dateOrNull($remoteService['nextduedate'] ?? null),
                    'metadata' => ['source' => 'whmcs', 'remote' => $remoteService],
                ],
            );
        }
    }

    public function syncInvoices(User $user): void
    {
        foreach ($this->whmcs->clientInvoices($user) as $remoteInvoice) {
            $invoiceId = $this->whmcs->firstId($remoteInvoice['id'] ?? null);

            if (! $invoiceId) {
                continue;
            }

            $total = (float) ($remoteInvoice['total'] ?? 0);
            $status = strtolower($remoteInvoice['status'] ?? 'unpaid');

            Invoice::updateOrCreate(
                ['whmcs_invoice_id' => $invoiceId],
                [
                    'number' => $remoteInvoice['invoicenum'] ?: 'WHMCS-INV-'.$invoiceId,
                    'user_id' => $user->id,
                    'status' => $status,
                    'subtotal' => $total,
                    'tax_total' => 0,
                    'total' => $total,
                    'paid_total' => $status === 'paid' ? $total : 0,
                    'currency' => config('services.whmcs.sync_currency'),
                    'issued_at' => $this->dateOrNull($remoteInvoice['date'] ?? null) ?: now()->toDateString(),
                    'due_at' => $this->dateOrNull($remoteInvoice['duedate'] ?? null) ?: now()->toDateString(),
                    'paid_at' => $status === 'paid' ? now() : null,
                    'line_items' => [['description' => 'WHMCS invoice '.$invoiceId, 'total' => $total]],
                ],
            );
        }
    }

    private function dateOrNull(?string $date): ?string
    {
        if (! $date || $date === '0000-00-00') {
            return null;
        }

        return Carbon::parse($date)->toDateString();
    }
}
