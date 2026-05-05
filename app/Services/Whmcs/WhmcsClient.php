<?php

namespace App\Services\Whmcs;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class WhmcsClient
{
    public function enabled(): bool
    {
        return (bool) config('services.whmcs.enabled');
    }

    public function call(string $action, array $parameters = []): array
    {
        if (! $this->enabled()) {
            throw new WhmcsApiException('WHMCS entegrasyonu devre dışı.');
        }

        $apiUrl = config('services.whmcs.api_url');
        $identifier = config('services.whmcs.identifier');
        $secret = config('services.whmcs.secret');

        if (! $apiUrl || ! $identifier || ! $secret) {
            throw new WhmcsApiException('WHMCS API bilgileri yapılandırılmamış.');
        }

        $payload = array_filter([
            'action' => $action,
            'identifier' => $identifier,
            'secret' => $secret,
            'accesskey' => config('services.whmcs.access_key'),
            'responsetype' => 'json',
        ], fn ($value) => $value !== null && $value !== '') + $parameters;

        try {
            $response = Http::asForm()
                ->timeout(20)
                ->retry(2, 250)
                ->post($apiUrl, $payload);
        } catch (ConnectionException $exception) {
            throw new WhmcsApiException('WHMCS bağlantısı kurulamadı: '.$exception->getMessage());
        }

        if (! $response->successful()) {
            throw new WhmcsApiException('WHMCS HTTP hatası: '.$response->status(), ['body' => $response->body()]);
        }

        $data = $response->json();

        if (! is_array($data)) {
            throw new WhmcsApiException('WHMCS geçersiz yanıt döndürdü.', ['body' => $response->body()]);
        }

        if (($data['result'] ?? null) === 'error') {
            throw new WhmcsApiException($data['message'] ?? 'WHMCS API hatası.', $data);
        }

        return $data;
    }
}
