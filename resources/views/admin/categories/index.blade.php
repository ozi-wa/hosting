<x-layouts.panel title="Kategoriler">
<div class="mb-4 flex items-center justify-between">
    <h1 class="text-2xl font-bold">Kategoriler</h1>
    <a class="btn-primary" href="{{ route('admin.categories.create') }}">Yeni Kategori</a>
</div>
<div class="panel overflow-x-auto">
    <table class="w-full">
        <thead class="table-head">
            <tr>
                <th class="p-4">Ad</th>
                <th>Slug (URL)</th>
                <th>Ürün</th>
                <th>Sıra</th>
                <th>Durum</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $category)
            <tr>
                <td class="table-cell font-medium">{{ $category->name }}</td>
                <td class="table-cell text-slate-500 text-sm font-mono">{{ $category->slug }}</td>
                <td class="table-cell">{{ $category->products_count }}</td>
                <td class="table-cell">{{ $category->sort_order ?? '—' }}</td>
                <td class="table-cell">
                    @if($category->is_active)<span class="badge">Aktif</span>@else<span class="badge">Pasif</span>@endif
                </td>
                <td class="table-cell text-right">
                    <a class="text-cyan-600 text-sm" href="{{ route('admin.categories.edit', $category) }}">Düzenle</a>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline" onsubmit="return confirm('Silinsin mi?')">
                        @csrf @method('DELETE')
                        <button class="ml-3 text-red-500 text-sm">Sil</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="table-cell text-slate-400">Henüz kategori yok. <code class="text-xs">php artisan whmcs:sync-products</code> ile WHMCS'ten çekin.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="panel mt-4 text-sm text-slate-500">
    <strong>Slug eşleşme tablosu</strong> — Sayfa URL'siyle eşleşen slug'a sahip kategori o sayfada görünür.
    <div class="mt-3 grid gap-1 font-mono text-xs">
        <span>/hosting-plans → <strong>web-hosting</strong></span>
        <span>/wordpress-hosting → <strong>wordpress-hosting</strong></span>
        <span>/kurumsal-hosting → <strong>kurumsal-hosting</strong></span>
        <span>/vps-vds → <strong>vps-vds</strong></span>
        <span>/dedicated-servers → <strong>dedicated-server</strong></span>
        <span>/radio-hosting → <strong>radio-hosting</strong></span>
        <span>/tv-hosting → <strong>tv-hosting</strong></span>
    </div>
</div>
</x-layouts.panel>
