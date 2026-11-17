@extends('layouts.admin')

@section('title', 'Kad Kahwin')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">Kad Kahwin</h1>
            <p class="mt-1 text-sm text-stone-500">Urus semua jemputan pelanggan di satu tempat.</p>
        </div>
        <a href="{{ route('admin.weddings.create') }}" class="btn btn-primary">Tambah Kad Kahwin</a>
    </div>

    <form method="GET" class="mt-6 grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama atau slug" class="field mt-0">
        <select name="status" class="field mt-0">
            <option value="">Semua status</option>
            <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
            <option value="tidak_aktif" @selected(request('status') === 'tidak_aktif')>Tidak aktif</option>
        </select>
        <button class="btn btn-secondary" type="submit">Cari</button>
    </form>

    @if ($weddings->isEmpty())
        <div class="card mt-6 text-center">
            <h2 class="text-lg font-semibold">Belum ada kad kahwin</h2>
            <p class="mt-2 text-sm text-stone-500">Cipta kad pertama untuk pelanggan anda.</p>
            <a href="{{ route('admin.weddings.create') }}" class="btn btn-primary mt-4">Tambah Kad Kahwin</a>
        </div>
    @else
        <div class="mt-6 space-y-4">
            @foreach ($weddings as $wedding)
                <article class="card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-semibold">{{ $wedding->coupleLabel() }}</h2>
                                <span class="badge {{ $wedding->is_active ? 'badge-aktif' : 'badge-tidak' }}">{{ $wedding->statusLabel() }}</span>
                            </div>
                            <p class="mt-1 text-sm text-stone-500">{{ $wedding->malayDate() }} · {{ $wedding->location_name }}</p>
                            <p class="mt-2 break-all text-sm text-stone-600">{{ $wedding->publicUrl() }}</p>
                            <p class="mt-2 text-xs text-stone-500">{{ $wedding->rsvps_count }} RSVP · {{ $wedding->wishes_count }} ucapan</p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-wrap gap-2">
                        <a href="{{ route('admin.weddings.edit', $wedding) }}" class="btn btn-secondary">Sunting</a>
                        <a href="{{ route('admin.weddings.preview', $wedding) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Pratonton</a>
                        <button type="button" class="btn btn-secondary" data-copy="{{ $wedding->publicUrl() }}">Salin Pautan</button>
                        <a href="{{ $wedding->whatsappShareUrl() }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Kongsi WhatsApp</a>
                        <a href="{{ route('admin.weddings.rsvps.index', $wedding) }}" class="btn btn-secondary">RSVP</a>
                        <a href="{{ route('admin.weddings.wishes.index', $wedding) }}" class="btn btn-secondary">Ucapan</a>
                        <form method="POST" action="{{ route('admin.weddings.status', $wedding) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="is_active" value="{{ $wedding->is_active ? 0 : 1 }}">
                            <button type="submit" class="btn btn-secondary">{{ $wedding->is_active ? 'Nyahaktif' : 'Aktifkan' }}</button>
                        </form>
                        <form method="POST" action="{{ route('admin.weddings.destroy', $wedding) }}" onsubmit="return confirm('Padam kad kahwin ini? RSVP dan ucapan turut dipadam. Tindakan ini tidak boleh diundurkan.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Padam</button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="text-stone-600">{{ $weddings->links('partials.pagination') }}</div>
    @endif
@endsection
