<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@turkacloud.test'],
            ['name' => 'Turka Yönetici', 'password' => Hash::make('password'), 'role' => 'admin', 'email_verified_at' => now()]
        );

        $rows = [
            ['Web Hosting',       'web-hosting',       'hosting',   0],
            ['WordPress Hosting', 'wordpress-hosting', 'hosting',   1],
            ['Kurumsal Hosting',  'kurumsal-hosting',  'hosting',   2],
            ['VPS / VDS',         'vps-vds',           'server',    3],
            ['Dedicated Sunucu',  'dedicated-server',  'server',    4],
            ['Radyo Hosting',     'radio-hosting',     'streaming', 5],
            ['TV Hosting',        'tv-hosting',        'streaming', 6],
        ];

        foreach ($rows as [$name, $slug, $type, $order]) {
            Category::updateOrCreate(['slug' => $slug], [
                'name'       => $name,
                'type'       => $type,
                'is_active'  => true,
                'sort_order' => $order,
            ]);
        }

        Setting::updateOrCreate(['key' => 'site_name'],       ['group' => 'site', 'value' => 'Turka Cloud']);
        Setting::updateOrCreate(['key' => 'seo_description'], ['group' => 'site', 'value' => 'Kurumsal hosting, VPS, dedicated sunucu ve yayın hosting hizmetleri.']);

        BlogPost::updateOrCreate(['slug' => 'hosting-buying-guide'], [
            'user_id'          => User::where('role', 'admin')->value('id'),
            'title'            => 'Üretim siteleri için doğru hosting paketi nasıl seçilir?',
            'excerpt'          => 'Trafik, destek, yedekleme ve büyüme ihtiyaçlarını doğru hosting paketiyle eşleştirmek için pratik kontrol listesi.',
            'body'             => "Önce trafik, yazılım ve kaynak ihtiyaçlarını belirleyin.\n\nSatın almadan önce yedekleme, SSL, destek saatleri ve yükseltme seçeneklerini mutlaka doğrulayın.",
            'meta_title'       => 'Hosting paketi seçme rehberi',
            'meta_description' => 'Üretim web siteleri için doğru hosting paketini seçin.',
            'tags'             => ['hosting', 'rehber'],
            'status'           => 'published',
            'published_at'     => now(),
        ]);
    }
}
