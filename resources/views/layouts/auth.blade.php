<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Log Masuk') — Kad Kahwin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-[#f6f3ee] px-4 text-stone-900 antialiased">
    <main class="w-full max-w-md">
        @yield('content')
    </main>
</body>
</html>
