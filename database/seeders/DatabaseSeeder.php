<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\OrderService;
use App\Services\SupportService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['email' => 'admin@turkacloud.test'],
            ['name' => 'Turka Yönetici', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $client = User::updateOrCreate(
            ['email' => 'client@turkacloud.test'],
            ['name' => 'Örnek Müşteri', 'password' => Hash::make('password'), 'role' => 'client', 'company_name' => 'Örnek Ltd', 'email_verified_at' => now()]
        );

        $categories = collect([
            ['Web Hosting', 'web-hosting', 'hosting', 'Kurumsal web siteleri ve e-posta destekli projeler için hızlı paylaşımlı hosting.'],
            ['WordPress Hosting', 'wordpress-hosting', 'hosting', 'Önbellekleme, güvenlik ve performans için optimize edilmiş WordPress paketleri.'],
            ['Kurumsal Hosting', 'kurumsal-hosting', 'hosting', 'Kurumsal iş yükleri için daha yüksek izolasyon ve destek önceliği.'],
            ['VPS / VDS', 'vps-vds', 'server', 'Root erişimli, NVMe depolamalı ve ölçeklenebilir sanal sunucular.'],
            ['Dedicated Sunucu', 'dedicated-server', 'server', 'Yoğun üretim sistemleri için fiziksel sunucu kaynakları.'],
            ['Radyo Hosting', 'radio-hosting', 'streaming', 'Yayıncılar ve online radyo istasyonları için güvenilir ses yayını altyapısı.'],
            ['TV Hosting', 'tv-hosting', 'streaming', 'Canlı ve planlı yayın kanalları için video streaming altyapısı.'],
        ])->mapWithKeys(fn (array $item) => [
            $item[1] => Category::updateOrCreate(['slug' => $item[1]], [
                'name' => $item[0],
                'type' => $item[2],
                'description' => $item[3],
                'is_active' => true,
            ]),
        ]);

        $plans = [
            ['web-hosting', 'Başlangıç Web', 'WEB-STARTER', 79, 790, ['10 GB NVMe', '10 e-posta hesabı', 'Ücretsiz SSL', 'Günlük yedekleme']],
            ['web-hosting', 'Business Web', 'WEB-BUSINESS', 149, 1490, ['30 GB NVMe', 'Esnek trafik politikası', 'Ücretsiz SSL', 'Öncelikli taşıma']],
            ['wordpress-hosting', 'WP Performans', 'WP-PERF', 189, 1890, ['LiteSpeed cache', 'Staging desteği', 'Zararlı yazılım taraması', 'Günlük yedekleme']],
            ['kurumsal-hosting', 'Kurumsal Plus', 'CORP-PLUS', 349, 3490, ['Ayrılmış kaynaklar', 'Öncelikli destek', 'Gelişmiş yedekleme', 'SLA izleme']],
            ['vps-vds', 'VDS 2 Core', 'VDS-2C', 499, 4990, ['2 vCPU', '4 GB RAM', '80 GB NVMe', 'Root erişimi']],
            ['vps-vds', 'VDS 4 Core', 'VDS-4C', 899, 8990, ['4 vCPU', '8 GB RAM', '160 GB NVMe', 'DDoS filtreleme']],
            ['dedicated-server', 'Bare Metal E3', 'DED-E3', 2499, 24990, ['Xeon CPU', '32 GB RAM', '2x SSD', 'Uzaktan müdahale']],
            ['radio-hosting', 'Radyo 500', 'RAD-500', 249, 2490, ['500 dinleyici', 'Auto DJ', 'SSL stream', 'İstatistik paneli']],
            ['tv-hosting', 'TV Stream Pro', 'TV-PRO', 1199, 11990, ['Uyarlanabilir bitrate', 'Canlı playlist', 'Token korumalı linkler', 'Analitik']],
        ];

        foreach ($plans as $index => [$slug, $name, $sku, $monthly, $yearly, $features]) {
            Product::updateOrCreate(['sku' => $sku], [
                'category_id' => $categories[$slug]->id,
                'name' => $name,
                'slug' => str($name)->slug()->toString(),
                'short_description' => 'Büyüyen ekipler için üretime hazır '.$categories[$slug]->name.' paketi.',
                'description' => 'Ticari kullanım, şeffaf faturalama, destek ve yükseltme süreçleri için tasarlandı.',
                'monthly_price' => $monthly,
                'yearly_price' => $yearly,
                'currency' => 'TRY',
                'features' => $features,
                'specifications' => ['destek' => '24/7', 'depolama' => 'NVMe'],
                'is_featured' => in_array($sku, ['WEB-BUSINESS', 'VDS-4C', 'RAD-500'], true),
                'is_active' => true,
                'sort_order' => $index,
            ]);
        }

        Setting::updateOrCreate(['key' => 'site_name'], ['group' => 'site', 'value' => 'Turka Cloud']);
        Setting::updateOrCreate(['key' => 'seo_description'], ['group' => 'site', 'value' => 'Kurumsal hosting, VPS, dedicated sunucu ve yayın hosting hizmetleri.']);

        BlogPost::updateOrCreate(['slug' => 'hosting-buying-guide'], [
            'user_id' => $admin->id,
            'title' => 'Üretim siteleri için doğru hosting paketi nasıl seçilir?',
            'excerpt' => 'Trafik, destek, yedekleme ve büyüme ihtiyaçlarını doğru hosting paketiyle eşleştirmek için pratik kontrol listesi.',
            'body' => "Önce trafik, yazılım ve kaynak ihtiyaçlarını belirleyin.\n\nSatın almadan önce yedekleme, SSL, destek saatleri ve yükseltme seçeneklerini mutlaka doğrulayın.",
            'meta_title' => 'Hosting paketi seçme rehberi',
            'meta_description' => 'Üretim web siteleri için doğru hosting paketini seçin.',
            'tags' => ['hosting', 'rehber'],
            'status' => 'published',
            'published_at' => now(),
        ]);

        if ($client->orders()->doesntExist()) {
            app(OrderService::class)->place($client, Product::where('sku', 'WEB-BUSINESS')->firstOrFail(), 'monthly', ['domain' => 'example.com']);
            app(SupportService::class)->openTicket($client, [
                'subject' => 'Taşıma planlaması',
                'department' => 'support',
                'priority' => 'normal',
                'message' => 'Mevcut web sitemizi minimum kesintiyle taşımak istiyoruz.',
            ]);
        }
    }
}
