@extends('layouts.auth')

@section('title', 'Log Masuk Pentadbir')

@section('content')
    <div class="card">
        <p class="text-xs uppercase tracking-[0.22em] text-amber-800/80">Studio Jemputan</p>
        <h1 class="mt-2 text-2xl font-semibold">Log Masuk Pentadbir</h1>
        <p class="mt-2 text-sm text-stone-500">Masuk untuk mengurus kad kahwin pelanggan anda.</p>

        <form method="POST" action="{{ route('admin.login.store') }}" class="mt-6 space-y-4" data-loading>
            @csrf
            <div>
                <label class="label" for="email">Emel</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="username" required autofocus class="field">
                @error('email')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="password">Kata Laluan</label>
                <input id="password" name="password" type="password" autocomplete="current-password" required class="field">
                @error('password')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm text-stone-600">
                <input type="checkbox" name="remember" value="1" class="rounded border-stone-300" @checked(old('remember'))>
                Ingat saya
            </label>
            <button type="submit" class="btn btn-primary w-full">Log Masuk</button>
        </form>
    </div>
@endsection
