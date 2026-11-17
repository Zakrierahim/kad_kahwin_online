<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f6f3ee; color: #2b241f; font-family: Georgia, serif; }
        main { max-width: 28rem; padding: 2rem; text-align: center; }
        a { color: inherit; }
    </style>
</head>
<body>
    <main>
        <p style="letter-spacing: .2em; text-transform: uppercase; font-size: .75rem;">Kad Kahwin</p>
        <h1>{{ $title }}</h1>
        <p>{{ $message }}</p>
        <p><a href="{{ url('/') }}">Kembali ke laman utama</a></p>
    </main>
</body>
</html>
