<x-layouts.panel title="Yönetim Özeti">
<h1 class="mb-6 text-2xl font-bold">Yönetim Özeti</h1>
<div class="grid gap-4 md:grid-cols-3">
    @foreach(['Kullanıcılar' => $usersCount, 'Aktif Ürünler' => $productsCount, 'Blog Yazıları' => $blogPostsCount] as $label => $value)
        <div class="panel"><p class="text-sm text-slate-500">{{ $label }}</p><p class="mt-2 text-3xl font-bold">{{ $value }}</p></div>
    @endforeach
</div>
<div class="panel mt-6">
    <h2 class="font-semibold">Fatura, Sipariş & Destek Yönetimi</h2>
    <p class="mt-2 text-sm text-slate-500">Sipariş, fatura, servis ve destek talepleri WHMCS üzerinden yönetilmektedir.</p>
    <a class="btn-primary mt-4 inline-block" href="{{ config('services.whmcs.client_url') }}/admin" target="_blank">WHMCS Yönetim Paneline Git ↗</a>
</div>
</x-layouts.panel>
