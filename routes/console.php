<?php

use App\Models\Category;
use App\Models\Product;
use App\Services\Whmcs\WhmcsGateway;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('whmcs:sync-products', function (WhmcsGateway $whmcs) {
    if (! $whmcs->enabled()) {
        $this->error('WHMCS devre dışı. WHMCS_ENABLED=true yapıp API bilgilerini girin.');

        return 1;
    }

    $synced = 0;

    foreach ($whmcs->products() as $whmcsProduct) {
        $gid = (int) ($whmcsProduct['gid'] ?? 0);
        $pid = (int) ($whmcsProduct['pid'] ?? $whmcsProduct['id'] ?? 0);

        if ($pid <= 0) {
            continue;
        }

        DB::transaction(function () use ($whmcsProduct, $gid, $pid, &$synced) {
            $categoryKey = $gid > 0 ? ['whmcs_gid' => $gid] : ['slug' => 'whmcs-uncategorized'];

            $category = Category::updateOrCreate(
                $categoryKey,
                [
                    'name' => $whmcsProduct['groupname'] ?? 'WHMCS Grup '.$gid,
                    'slug' => str($whmcsProduct['groupname'] ?? 'whmcs-group-'.$gid)->slug()->toString(),
                    'description' => 'WHMCS ürün grubundan senkronize edildi: '.$gid,
                    'type' => 'whmcs',
                    'is_active' => true,
                ],
            );

            $pricing = $whmcsProduct['pricing'][config('services.whmcs.sync_currency')] ?? collect($whmcsProduct['pricing'] ?? [])->first() ?? [];
            $monthly = $pricing['monthly'] ?? 0;
            $yearly = $pricing['annually'] ?? null;

            Product::updateOrCreate(
                ['whmcs_product_id' => $pid],
                [
                    'category_id' => $category->id,
                    'whmcs_gid' => $gid ?: null,
                    'name' => $whmcsProduct['name'] ?? 'WHMCS Ürün '.$pid,
                    'slug' => str($whmcsProduct['name'] ?? 'whmcs-product-'.$pid)->slug()->toString(),
                    'sku' => 'WHMCS-'.$pid,
                    'short_description' => str($whmcsProduct['description'] ?? 'WHMCS üzerinden senkronize edilen ürün')->limit(250)->toString(),
                    'description' => $whmcsProduct['description'] ?? null,
                    'monthly_price' => is_numeric($monthly) ? $monthly : 0,
                    'yearly_price' => is_numeric($yearly) ? $yearly : null,
                    'currency' => config('services.whmcs.sync_currency'),
                    'features' => array_values(array_filter(array_map('trim', explode("\n", strip_tags($whmcsProduct['description'] ?? ''))))),
                    'specifications' => ['source' => 'whmcs', 'gid' => $gid, 'pid' => $pid],
                    'is_active' => true,
                ],
            );

            $synced++;
        });
    }

    $this->info("{$synced} WHMCS ürünü senkronize edildi.");

    return 0;
})->purpose('WHMCS ürün kataloğunu Laravel vitrin önbelleğine senkronize eder');
