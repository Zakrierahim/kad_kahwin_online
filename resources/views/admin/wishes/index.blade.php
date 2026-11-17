@extends('layouts.admin')

@section('title', 'Ucapan '.$wedding->coupleLabel())

@section('content')
    <div>
        <p class="text-sm text-stone-500"><a href="{{ route('admin.weddings.index') }}" class="underline">Kad Kahwin</a></p>
        <h1 class="text-3xl font-semibold tracking-tight">Ucapan {{ $wedding->coupleLabel() }}</h1>
    </div>

    <form method="GET" class="mt-6 flex flex-wrap gap-3">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama tetamu" class="field mt-0 max-w-sm">
        <button class="btn btn-secondary" type="submit">Cari</button>
    </form>

    @if ($wishes->isEmpty())
        <div class="card mt-6 text-center">
            <h2 class="text-lg font-semibold">Belum ada ucapan</h2>
            <p class="mt-2 text-sm text-stone-500">Ucapan tetamu untuk majlis ini akan dipaparkan di sini.</p>
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($wishes as $wish)
                <article class="card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h2 class="font-semibold">{{ $wish->guest_name }}</h2>
                            <p class="text-xs text-stone-500">{{ $wish->created_at ? \App\Support\MalayFormatter::dateTime($wish->created_at) : '' }}</p>
                        </div>
                        <form method="POST" action="{{ route('admin.weddings.wishes.destroy', [$wedding, $wish]) }}" onsubmit="return confirm('Padam ucapan ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Padam</button>
                        </form>
                    </div>
                    <p class="mt-3 whitespace-pre-line break-words text-sm leading-relaxed text-stone-700">{{ $wish->message }}</p>
                </article>
            @endforeach
        </div>
        <div class="text-stone-600">{{ $wishes->links('partials.pagination') }}</div>
    @endif
@endsection
