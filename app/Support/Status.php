<?php

namespace App\Support;

class Status
{
    public static function label(?string $status): string
    {
        return [
            'active' => 'Aktif',
            'answered' => 'Yanıtlandı',
            'cancelled' => 'İptal edildi',
            'closed' => 'Kapalı',
            'disabled' => 'Pasif',
            'draft' => 'Taslak',
            'failed' => 'Başarısız',
            'open' => 'Açık',
            'paid' => 'Ödendi',
            'pending' => 'Beklemede',
            'pending_payment' => 'Ödeme bekliyor',
            'processing' => 'İşleniyor',
            'published' => 'Yayında',
            'suspended' => 'Askıya alındı',
            'unpaid' => 'Ödenmedi',
        ][$status] ?? (string) $status;
    }

    public static function role(?string $role): string
    {
        return [
            'admin' => 'Yönetici',
            'client' => 'Müşteri',
        ][$role] ?? (string) $role;
    }
}
