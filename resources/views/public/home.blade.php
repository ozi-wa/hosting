<x-layouts.app title="Turka Cloud - Premium Hosting">
    <section class="bg-slate-950 text-white">
        <div class="container-page grid min-h-[620px] items-center gap-10 py-16 lg:grid-cols-[1.05fr_.95fr]">
            <div>
                <p class="mb-4 text-sm font-semibold uppercase tracking-wide text-cyan-300">Hosting, VPS, dedicated sunucu ve yayın altyapısı</p>
                <h1 class="max-w-4xl text-5xl font-bold tracking-tight sm:text-6xl">Turka Cloud</h1>
                <p class="mt-6 max-w-2xl text-lg text-slate-300">Ajanslar, yayıncılar, e-ticaret siteleri, yayın kuruluşları ve kurumsal ekipler için öngörülebilir performans ve hızlı destek sunan hosting altyapısı.</p>
                <div class="mt-8 flex flex-wrap gap-3"><a href="{{ route('hosting') }}" class="btn-primary">Paketleri İncele</a><a href="{{ route('contact') }}" class="btn-secondary border-slate-600 text-white">Satış Ekibiyle Görüş</a></div>
                <div class="mt-10 grid max-w-2xl grid-cols-3 gap-4 text-sm">
                    <div><strong class="block text-2xl">99.9%</strong><span class="text-slate-400">Erişilebilirlik hedefi</span></div>
                    <div><strong class="block text-2xl">24/7</strong><span class="text-slate-400">Destek masası</span></div>
                    <div><strong class="block text-2xl">NVMe</strong><span class="text-slate-400">Hızlı depolama</span></div>
                </div>
            </div>
            <div class="rounded-lg border border-slate-800 bg-slate-900 p-4 shadow-2xl">
                <div class="grid gap-3">
                    @foreach(['Web Hosting','WordPress Hosting','VPS / VDS','Dedicated Sunucular','Radyo Yayını','TV Yayını'] as $service)
                        <div class="flex items-center justify-between rounded-md bg-slate-800 p-4"><span>{{ $service }}</span><span class="badge">Hazır</span></div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    <section class="container-page py-16">
        <div class="mb-8 flex items-end justify-between gap-4"><div><h2 class="text-3xl font-bold">Öne Çıkan Paketler</h2><p class="mt-2 text-slate-500">Net fiyatlandırma, ölçeklenebilir kaynaklar ve şeffaf hizmet yapısı.</p></div><a class="btn-secondary" href="{{ route('hosting') }}">Tümünü Karşılaştır</a></div>
        <div class="grid gap-5 md:grid-cols-3">
            @foreach($featuredProducts as $product)
                <article class="panel flex flex-col">
                    <span class="badge w-fit">{{ $product->category->name }}</span>
                    <h3 class="mt-4 text-xl font-bold">{{ $product->name }}</h3>
                    <p class="mt-2 min-h-12 text-sm text-slate-500">{{ $product->short_description }}</p>
                    <p class="mt-6 text-3xl font-bold">{{ $product->monthly_price }} {{ $product->currency }}<span class="text-sm font-medium text-slate-500">/ay</span></p>
                    <ul class="mt-5 grid gap-2 text-sm text-slate-600 dark:text-slate-300">@foreach(($product->features ?? []) as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                    <a href="{{ route('client.orders.create') }}" class="btn-primary mt-6">Hemen Sipariş Ver</a>
                </article>
            @endforeach
        </div>
    </section>
    <section class="bg-white py-16 dark:bg-slate-900"><div class="container-page grid gap-6 md:grid-cols-3">
        @foreach(['Türkiye ve Avrupa odaklı düşük gecikmeli ağ','Güvenli faturalama ve destek süreçleri','WHMCS provisioning yapısına hazır mimari'] as $item)
            <div><h3 class="text-lg font-semibold">{{ $item }}</h3><p class="mt-2 text-sm text-slate-500">Temiz yönetim kontrolleri, WHMCS entegrasyonu ve büyümeye uygun operasyon yapısıyla ticari kullanım için tasarlandı.</p></div>
        @endforeach
    </div></section>
</x-layouts.app>
