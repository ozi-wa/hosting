# Turka Cloud - Hosting Platformu

WHMCS merkezli çalışan, Laravel tabanlı hosting şirketi web sitesi ve yönetim katmanı.

## Teknoloji

- Laravel 13
- Blade + Tailwind CSS 4 + Vite
- MySQL, MariaDB veya lokal geliştirme için SQLite
- Sipariş, fatura ve destek akışları için servis katmanı
- Mobil uygulama ve otomasyon için REST API

## Klasör Yapısı

- `app/Models`: kullanıcı, kategori, ürün, sipariş, fatura, ödeme, hizmet, destek talebi, ayar, blog ve API token modelleri.
- `app/Services`: `OrderService`, `BillingService`, `SupportService` ve WHMCS entegrasyon servisleri.
- `app/Http/Controllers/PublicPageController.php`: herkese açık vitrin, fiyatlandırma ve blog sayfaları.
- `app/Http/Controllers/Client`: müşteri paneli, siparişler, faturalar ve destek talepleri.
- `app/Http/Controllers/Admin`: yönetim paneli, kullanıcılar, ürünler, siparişler, faturalar, destek talepleri, ayarlar ve blog.
- `app/Http/Controllers/Api`: REST kimlik doğrulama, ürün, sipariş, fatura ve hizmet uçları.
- `database/migrations`: foreign key ve index içeren normalize schema.
- `database/seeders/DatabaseSeeder.php`: Türkçe örnek ürün kataloğu, admin/müşteri hesabı, örnek sipariş, fatura, hizmet, destek talebi ve blog yazısı.
- `resources/views/public`: SEO uyumlu vitrin sayfaları.
- `resources/views/client`: müşteri paneli ekranları.
- `resources/views/admin`: yönetim paneli ekranları.
- `routes/web.php`: public, auth, müşteri ve yönetim rotaları.
- `routes/api.php`: REST API rotaları.

## Kurulum

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
```

MySQL örnek `.env` ayarı:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=turka_cloud
DB_USERNAME=root
DB_PASSWORD=
```

Schema ve örnek veriler:

```bash
php artisan migrate --seed
npm run build
php artisan serve
```

Örnek hesaplar:

- Yönetici: `admin@turkacloud.test` / `password`
- Müşteri: `client@turkacloud.test` / `password`

## Doğrulama

```bash
php artisan route:list --except-vendor
php artisan test
npm run build
```

## Ana Schema

- `users`: müşteri ve yöneticiler, fatura profili, rol ve durum bilgileri.
- `categories`: web hosting, WordPress, kurumsal, VPS/VDS, dedicated ve yayın kategorileri.
- `products`: fiyat, SKU, özellikler, WHMCS ürün eşleşmesi ve aktiflik bilgileri.
- `orders`: WHMCS sipariş id ile eşleşen lokal sipariş kaydı.
- `invoices`: WHMCS fatura id ile eşleşen lokal fatura görünümü.
- `payments`: ödeme olayları için genişletilebilir kayıt alanı.
- `services`: WHMCS servis id ile eşleşen hizmet görünümü.
- `tickets`: WHMCS ticket id ile eşleşen destek talepleri.
- `ticket_messages`: müşteri/yetkili mesajları.
- `settings`: site ayarları.
- `blog_posts`: SEO uyumlu blog yazıları.
- `api_tokens`: mobil uygulama ve otomasyon için bearer token kayıtları.

## REST API

Herkese açık:

- `POST /api/auth/register`
- `POST /api/auth/login`
- `GET /api/products`
- `GET /api/products/{product}`

Bearer token gerekli:

- `GET /api/auth/me`
- `POST /api/auth/logout`
- `GET /api/orders`
- `POST /api/orders`
- `GET /api/invoices`
- `GET /api/invoices/{invoice}`
- `GET /api/services`
- `GET /api/services/{service}`

## WHMCS Modu

Bu proje müşteriler, ürünler, siparişler, faturalar, destek talepleri ve hosting provisioning için WHMCS'i operasyon merkezi kabul eder.

`.env` ayarları:

```env
WHMCS_ENABLED=true
WHMCS_API_URL=https://whmcs-domaininiz.com/includes/api.php
WHMCS_API_IDENTIFIER=identifier
WHMCS_API_SECRET=secret
WHMCS_ACCESS_KEY=
WHMCS_DEFAULT_PAYMENT_METHOD=banktransfer
WHMCS_SYNC_CURRENCY=TRY
```

WHMCS ürünlerini Laravel vitrin önbelleğine çekmek için:

```bash
php artisan whmcs:sync-products
```

Çalışma mantığı:

- Ürünler WHMCS'te oluşturulur, Laravel'e `whmcs:sync-products` ile çekilir.
- Müşteri kaydı WHMCS `AddClient` API çağrısıyla açılır.
- Müşteri girişi WHMCS `ValidateLogin` ile doğrulanır.
- Laravel sadece oturum, Türkçe vitrin ve müşteri deneyimi katmanıdır.
- Siparişler WHMCS `AddOrder` API çağrısıyla açılır.
- WHMCS sipariş, fatura, servis ve ticket id değerleri lokalde saklanır.
- Destek talepleri WHMCS `OpenTicket`, yanıtlar `AddTicketReply` ile işlenir.
- Lokal tablolar panel, API ve SEO sayfaları için okuma modeli/önbellek gibi kullanılır.

## Genişletme Noktaları

- WHMCS webhookları: fatura, servis ve ticket durumlarını lokalde güncellemek için callback rotaları eklenebilir.
- WHM/cPanel provisioning: Laravel yerine WHMCS modülleri üzerinden yürütülmelidir.
- Alan adı: WHMCS registrar modülleri üzerinden satılmalı ve yönetilmelidir.
- Yönetim paneli: Mevcut servis katmanı korunarak Livewire veya Inertia ile zenginleştirilebilir.
