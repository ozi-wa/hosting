<!doctype html>
<html lang="tr" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Turka Cloud - Hosting Platformu' }}</title>
    <meta name="description" content="{{ $description ?? 'İşletmeler için web hosting, VPS, dedicated sunucu ve yayın altyapısı.' }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="sticky top-0 z-40 border-b border-slate-200/70 bg-white/90 backdrop-blur dark:border-slate-800 dark:bg-slate-950/90">
    <nav class="container-page flex h-16 items-center justify-between gap-6">
        <a href="{{ route('home') }}" class="text-lg font-bold tracking-tight">Turka <span class="text-cyan-500">Cloud</span></a>
        <div class="hidden items-center gap-5 text-sm font-medium lg:flex">
            <a href="{{ route('hosting') }}">Hosting</a>
            <a href="{{ route('vps') }}">VPS</a>
            <a href="{{ route('dedicated') }}">Dedicated Sunucu</a>
            <a href="{{ route('radio') }}">Radio</a>
            <a href="{{ route('tv') }}">TV</a>
            <a href="{{ route('blog') }}">Blog</a>
        </div>
        <div class="flex items-center gap-3">
            @auth
                <a class="btn-secondary" href="{{ route('client.dashboard') }}">Panel</a>
                @if(auth()->user()->isAdmin()) <a class="btn-primary" href="{{ route('admin.dashboard') }}">Yönetim</a> @endif
            @else
                <a class="btn-secondary" href="{{ route('login') }}">Giriş</a>
                <a class="btn-primary" href="{{ route('register') }}">Başla</a>
            @endauth
        </div>
    </nav>
</header>
<main>
    @if (session('status'))
        <div class="container-page pt-4"><div class="panel border-cyan-200 text-sm text-cyan-800 dark:border-cyan-800 dark:text-cyan-200">{{ session('status') }}</div></div>
    @endif
    @if ($errors->any())
        <div class="container-page pt-4"><div class="panel border-red-200 text-sm text-red-700 dark:border-red-900 dark:text-red-200">{{ $errors->first() }}</div></div>
    @endif
    {{ $slot }}
</main>
<footer class="mt-20 border-t border-slate-200 py-10 dark:border-slate-800">
    <div class="container-page flex flex-col justify-between gap-4 text-sm text-slate-500 md:flex-row">
        <p>© {{ date('Y') }} Turka Cloud. Kurumsal hosting altyapısı.</p>
        <div class="flex gap-4"><a href="{{ route('faq') }}">SSS</a><a href="{{ route('contact') }}">İletişim</a><a href="{{ route('about') }}">Hakkımızda</a></div>
    </div>
</footer>
</body>
</html>
