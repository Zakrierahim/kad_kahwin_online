<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kad Kahwin Digital</title>
    <meta name="description" content="Platform jemputan perkahwinan digital untuk majlis di Malaysia.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f3ee] text-stone-900 antialiased">
    <main class="mx-auto flex min-h-screen max-w-3xl flex-col justify-center px-6 py-16">
        <p class="text-xs uppercase tracking-[0.28em] text-amber-800/80">Studio Jemputan</p>
        <h1 class="mt-4 max-w-xl font-serif text-5xl leading-tight text-stone-900">Kad Kahwin Digital</h1>
        <p class="mt-5 max-w-xl text-lg leading-relaxed text-stone-600">Setiap pasangan menerima pautan jemputan mereka sendiri. Tetamu boleh melihat butiran majlis, membuka peta, mengesahkan kehadiran dan meninggalkan ucapan.</p>
        <p class="mt-8 text-sm text-stone-500">Jemputan hanya boleh dibuka melalui pautan yang diberikan oleh tuan rumah.</p>
        <a href="{{ route('admin.login') }}" class="mt-10 text-sm font-medium text-stone-500 underline decoration-stone-300 underline-offset-4">Pentadbir</a>
    </main>
</body>
</html>
