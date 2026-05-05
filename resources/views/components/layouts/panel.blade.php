<x-layouts.app :title="$title ?? 'Turka Cloud Panel'">
    <div class="container-page grid gap-6 py-8 lg:grid-cols-[240px_1fr]">
        <aside class="panel h-fit">
            <p class="mb-4 text-xs font-semibold uppercase text-slate-500">{{ auth()->user()->isAdmin() ? 'Yönetim' : 'Müşteri' }} Paneli</p>
            <div class="grid gap-2 text-sm">
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.dashboard') }}">Özet</a>
                    <a href="{{ route('admin.users.index') }}">Kullanıcılar</a>
                    <a href="{{ route('admin.products.index') }}">Ürünler</a>
                    <a href="{{ route('admin.orders.index') }}">Siparişler</a>
                    <a href="{{ route('admin.invoices.index') }}">Faturalar</a>
                    <a href="{{ route('admin.tickets.index') }}">Destek Talepleri</a>
                    <a href="{{ route('admin.blog-posts.index') }}">Blog</a>
                    <a href="{{ route('admin.settings.edit') }}">Ayarlar</a>
                @else
                    <a href="{{ route('client.dashboard') }}">Özet</a>
                    <a href="{{ route('client.orders.index') }}">Siparişler</a>
                    <a href="{{ route('client.invoices.index') }}">Faturalar</a>
                    <a href="{{ route('client.tickets.index') }}">Destek Talepleri</a>
                    <a href="{{ route('client.profile') }}">Profil</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">@csrf <button class="text-left text-red-500">Çıkış</button></form>
            </div>
        </aside>
        <section>{{ $slot }}</section>
    </div>
</x-layouts.app>
