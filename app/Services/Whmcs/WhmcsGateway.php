<?php

namespace App\Services\Whmcs;

use App\Models\Product;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Arr;

class WhmcsGateway
{
    public function __construct(private readonly WhmcsClient $client) {}

    public function enabled(): bool
    {
        return $this->client->enabled();
    }

    public function ensureClient(User $user): int
    {
        if ($user->whmcs_client_id) {
            return (int) $user->whmcs_client_id;
        }

        try {
            $existingClient = $this->client->call('GetClientsDetails', [
                'email' => $user->email,
            ]);

            $existingClientId = (int) ($existingClient['userid'] ?? $existingClient['client']['id'] ?? 0);

            if ($existingClientId > 0) {
                $user->forceFill(['whmcs_client_id' => $existingClientId])->save();

                return $existingClientId;
            }
        } catch (WhmcsApiException $e) {
            if (empty($e->payload)) {
                throw $e; // Bağlantı/HTTP hatası — müşteri oluşturmaya geçme.
            }
            // API düzeyinde hata (müşteri bulunamadı) — yeni oluşturulacak.
        }

        $nameParts = explode(' ', trim($user->name), 2);
        $response = $this->client->call('AddClient', [
            'firstname' => $nameParts[0] ?: $user->name,
            'lastname' => $nameParts[1] ?? '-',
            'companyname' => $user->company_name,
            'email' => $user->email,
            'address1' => $user->billing_address ?: 'Belirtilmedi',
            'city' => 'Istanbul',
            'state' => 'Istanbul',
            'postcode' => '34000',
            'country' => 'TR',
            'phonenumber' => $user->phone ?: '+900000000000',
            'password2' => bin2hex(random_bytes(12)),
            'skipvalidation' => true,
        ]);

        $clientId = (int) ($response['clientid'] ?? 0);

        if ($clientId <= 0) {
            throw new WhmcsApiException('WHMCS müşteri id döndürmedi.', $response);
        }

        $user->forceFill(['whmcs_client_id' => $clientId])->save();

        return $clientId;
    }

    public function registerClient(array $data): int
    {
        $nameParts = explode(' ', trim($data['name']), 2);
        $response = $this->client->call('AddClient', [
            'firstname' => $nameParts[0] ?: $data['name'],
            'lastname' => $nameParts[1] ?? '-',
            'companyname' => $data['company_name'] ?? null,
            'email' => $data['email'],
            'address1' => $data['billing_address'] ?? 'Belirtilmedi',
            'city' => $data['city'] ?? 'Istanbul',
            'state' => $data['state'] ?? 'Istanbul',
            'postcode' => $data['postcode'] ?? '34000',
            'country' => $data['country'] ?? 'TR',
            'phonenumber' => $data['phone'] ?? '+900000000000',
            'password2' => $data['password'],
            'skipvalidation' => true,
        ]);

        $clientId = (int) ($response['clientid'] ?? 0);

        if ($clientId <= 0) {
            throw new WhmcsApiException('WHMCS müşteri id döndürmedi.', $response);
        }

        return $clientId;
    }

    public function validateLogin(string $email, string $password): int
    {
        $response = $this->client->call('ValidateLogin', [
            'email' => $email,
            'password2' => $password,
        ]);

        $clientId = (int) ($response['userid'] ?? $response['clientid'] ?? 0);

        if ($clientId <= 0) {
            throw new WhmcsApiException('WHMCS giriş bilgileri doğrulanamadı.', $response);
        }

        return $clientId;
    }

    public function createOrder(User $user, Product $product, string $billingCycle, array $metadata = []): array
    {
        if (! $product->whmcs_product_id) {
            throw new WhmcsApiException('Ürün bir WHMCS ürün id ile eşleştirilmemiş.');
        }

        return $this->client->call('AddOrder', [
            'clientid' => $this->ensureClient($user),
            'pid' => [$product->whmcs_product_id],
            'domain' => [$metadata['domain'] ?? ''],
            'billingcycle' => [$this->billingCycle($billingCycle)],
            'paymentmethod' => config('services.whmcs.default_payment_method'),
            'noemail' => false,
        ]);
    }

    public function openTicket(User $user, array $data): array
    {
        return $this->client->call('OpenTicket', [
            'clientid' => $this->ensureClient($user),
            'deptid' => $data['whmcs_department_id'] ?? 1,
            'subject' => $data['subject'],
            'message' => $data['message'],
            'priority' => ucfirst($data['priority'] ?? 'Medium'),
        ]);
    }

    public function replyToTicket(Ticket $ticket, User $user, string $message): array
    {
        return $this->client->call('AddTicketReply', array_filter([
            'ticketid' => $ticket->whmcs_ticket_id,
            'clientid' => $user->isAdmin() ? null : $this->ensureClient($user),
            'message' => $message,
        ]));
    }

    public function products(): array
    {
        return Arr::wrap($this->client->call('GetProducts')['products']['product'] ?? []);
    }

    public function clientProducts(User $user): array
    {
        return Arr::wrap($this->client->call('GetClientsProducts', [
            'clientid' => $this->ensureClient($user),
        ])['products']['product'] ?? []);
    }

    public function clientInvoices(User $user): array
    {
        return Arr::wrap($this->client->call('GetInvoices', [
            'userid' => $this->ensureClient($user),
        ])['invoices']['invoice'] ?? []);
    }

    private function billingCycle(string $billingCycle): string
    {
        return match ($billingCycle) {
            'yearly' => 'annually',
            'monthly' => 'monthly',
            default => config('services.whmcs.default_billing_cycle', 'monthly'),
        };
    }

    public function firstId(mixed $value): ?int
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        if (is_string($value) && str_contains($value, ',')) {
            $value = explode(',', $value)[0];
        }

        $id = (int) $value;

        return $id > 0 ? $id : null;
    }
}
