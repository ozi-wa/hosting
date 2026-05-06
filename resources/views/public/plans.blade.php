<x-layouts.app :title="$title">
    <section class="container-page py-14">
        <div class="max-w-3xl">
            <h1 class="text-4xl font-bold">{{ $title }}</h1>
            @if($category)<p class="mt-4 text-slate-500">{{ $category->description }}</p>@endif
        </div>
        @if($products->isEmpty())
            <div class="mt-10 panel text-slate-500">Yakında yeni paketler eklenecek. Detaylı bilgi için <a class="text-cyan-600" href="{{ route('contact') }}">iletişime geçin</a>.</div>
        @else
            <div class="mt-10 grid gap-5 md:grid-cols-3">
                @foreach($products as $product)
                    <article class="panel flex flex-col">
                        <h2 class="text-xl font-bold">{{ $product->name }}</h2>
                        <p class="mt-2 text-sm text-slate-500">{{ $product->short_description }}</p>
                        <p class="mt-6 text-3xl font-bold">{{ $product->monthly_price }} {{ $product->currency }}<span class="text-sm text-slate-500">/ay</span></p>
                        @if($product->yearly_price)<p class="text-sm text-slate-500">{{ $product->yearly_price }} {{ $product->currency }}/yıl</p>@endif
                        <ul class="mt-5 grid gap-2 text-sm">@foreach(($product->features ?? []) as $feature)<li>{{ $feature }}</li>@endforeach</ul>
                        <a href="{{ config('services.whmcs.client_url') }}/cart.php?a=add&pid={{ $product->whmcs_product_id }}" class="btn-primary mt-auto">Sipariş Ver</a>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
</x-layouts.app>
