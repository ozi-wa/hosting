<x-layouts.app :title="$title ?? 'Turka Cloud Yönetim'">
    <div class="container-page grid gap-6 py-8 lg:grid-cols-[240px_1fr]">
        <aside class="panel h-fit">
            <p class="mb-4 text-xs font-semibold uppercase text-slate-500">Yönetim Paneli</p>
            <div class="grid gap-2 text-sm">
                <a href="{{ route('admin.dashboard') }}">Özet</a>
                <a href="{{ route('admin.users.index') }}">Kullanıcılar</a>
                <a href="{{ route('admin.blog-posts.index') }}">Blog</a>
                <a href="{{ route('admin.settings.edit') }}">Ayarlar</a>
                <a href="{{ config('services.whmcs.client_url') }}/admin" target="_blank" class="text-cyan-600">WHMCS Paneli ↗</a>
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="text-left text-red-500">Çıkış</button></form>
            </div>
        </aside>
        <section>{{ $slot }}</section>
    </div>
</x-layouts.app>
