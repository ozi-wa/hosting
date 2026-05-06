<x-layouts.panel :title="$category->exists ? 'Kategori Düzenle' : 'Yeni Kategori'">
<form method="POST"
      action="{{ $category->exists ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
      class="panel grid gap-4 max-w-2xl">
    @csrf
    @if($category->exists) @method('PUT') @endif

    <h1 class="text-2xl font-bold">{{ $category->exists ? 'Kategori Düzenle' : 'Yeni Kategori' }}</h1>

    <label>
        <span class="label">Ad</span>
        <input class="field" name="name" value="{{ old('name', $category->name) }}" required>
    </label>

    <label>
        <span class="label">Slug (URL)</span>
        <input class="field font-mono" name="slug" value="{{ old('slug', $category->slug) }}" required
               placeholder="web-hosting">
        <span class="text-xs text-slate-400 mt-1 block">Sayfa URL'siyle eşleşmeli — örn. /hosting-plans sayfası için <strong>web-hosting</strong></span>
    </label>

    <label>
        <span class="label">Açıklama</span>
        <textarea class="field" name="description" rows="3">{{ old('description', $category->description) }}</textarea>
    </label>

    <label>
        <span class="label">Sıralama</span>
        <input class="field" name="sort_order" type="number" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
    </label>

    <label class="flex items-center gap-2">
        <input type="hidden" name="is_active" value="0">
        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $category->is_active ?? true) ? 'checked' : '' }}>
        <span class="text-sm">Aktif (sitede görünsün)</span>
    </label>

    <div class="flex gap-3">
        <button class="btn-primary">Kaydet</button>
        <a class="btn-secondary" href="{{ route('admin.categories.index') }}">İptal</a>
    </div>
</form>
</x-layouts.panel>
