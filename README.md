# Turka Cloud - Hosting Platformu

Turka Cloud, WHMCS merkezli çalışan Laravel tabanlı hosting şirketi web sitesi ve yönetim katmanıdır.

Sistem tamamen Türkçe arayüzle hazırlanmıştır. Laravel tarafı vitrin, SEO sayfaları, müşteri deneyimi, yönetim paneli ve API katmanı olarak çalışır. Müşteri, ürün, sipariş, fatura, destek talebi ve hosting provisioning süreçlerinin operasyon merkezi WHMCS'tir.

## Temel Özellikler

- Türkçe public web sitesi
- Hosting, WordPress, kurumsal hosting, VPS/VDS, dedicated, radyo ve TV yayın hosting sayfaları
- Müşteri kayıt/giriş/e-posta doğrulama
- Müşteri paneli
- Yönetim paneli
- WHMCS ürün senkronizasyonu
- WHMCS müşteri kayıt ve giriş doğrulaması
- WHMCS üzerinden sipariş oluşturma
- WHMCS fatura ve servis projection/cache yapısı
- WHMCS üzerinden destek talebi açma ve yanıtlama
- REST API
- Türkçe validasyon ve auth mesajları

## Kurulum

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

## Örnek Giriş Bilgileri

Yönetici:

```text
admin@turkacloud.test
password
```

Müşteri:

```text
client@turkacloud.test
password
```

## WHMCS Ayarları

`.env` içinde:

```env
WHMCS_ENABLED=true
WHMCS_API_URL=https://whmcs-domaininiz.com/includes/api.php
WHMCS_API_IDENTIFIER=identifier
WHMCS_API_SECRET=secret
WHMCS_ACCESS_KEY=
WHMCS_DEFAULT_PAYMENT_METHOD=banktransfer
WHMCS_SYNC_CURRENCY=TRY
```

WHMCS ürünlerini çekmek için:

```bash
php artisan whmcs:sync-products
```

Detaylı kurulum ve mimari notları için:

[docs/INSTALLATION.md](docs/INSTALLATION.md)
