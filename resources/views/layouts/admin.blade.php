<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Pentadbir') — Kad Kahwin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f6f3ee] text-stone-900 antialiased">
    <div data-sidebar-overlay class="fixed inset-0 z-30 hidden bg-stone-950/40 md:hidden"></div>
    <aside data-sidebar class="fixed inset-y-0 left-0 z-40 flex w-72 -translate-x-full flex-col bg-[#2c2420] text-stone-100 transition md:translate-x-0">
        <div class="flex items-center justify-between px-6 py-6">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-amber-200/80">Studio Jemputan</p>
                <p class="mt-1 text-lg font-semibold">Kad Kahwin</p>
            </div>
            <button type="button" data-sidebar-close class="rounded-lg px-2 py-1 text-sm text-stone-300 md:hidden">Tutup</button>
        </div>
        <nav class="flex-1 space-y-1 px-3">
            <a href="{{ route('admin.dashboard') }}" class="block rounded-xl px-3 py-2.5 text-sm {{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5' }}">Papan Pemuka</a>
            <a href="{{ route('admin.weddings.index') }}" class="block rounded-xl px-3 py-2.5 text-sm {{ request()->routeIs('admin.weddings.*') ? 'bg-white/10 text-white' : 'text-stone-300 hover:bg-white/5' }}">Kad Kahwin</a>
        </nav>
        <div class="space-y-3 border-t border-white/10 p-4">
            <a href="{{ route('home') }}" class="block px-2 text-sm text-stone-300 hover:text-white">Laman Utama</a>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="btn btn-secondary w-full">Log Keluar</button>
            </form>
        </div>
    </aside>

    <div class="md:pl-72">
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-stone-200/80 bg-[#f6f3ee]/90 px-4 py-3 backdrop-blur md:hidden">
            <p class="font-semibold">Kad Kahwin</p>
            <button type="button" data-sidebar-open class="btn btn-secondary px-3 py-2">Menu</button>
        </header>
        <main class="mx-auto w-full max-w-6xl px-4 py-6 sm:px-6 lg:px-8">
            @include('admin.partials.flash')
            @yield('content')
        </main>
    </div>
</body>
</html>
