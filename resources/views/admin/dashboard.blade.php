@extends('layouts.admin')

@section('title', 'Papan Pemuka')

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-stone-500">Selamat datang kembali</p>
            <h1 class="text-3xl font-semibold tracking-tight">Papan Pemuka</h1>
        </div>
        <a href="{{ route('admin.weddings.create') }}" class="btn btn-primary">Tambah Kad Kahwin</a>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <x-admin.stat label="Jumlah Keseluruhan Pasangan" :value="$stats['weddings']" />
        <x-admin.stat label="Jemputan Aktif" :value="$stats['active']" />
        <x-admin.stat label="Jemputan Tidak Aktif" :value="$stats['inactive']" />
        <x-admin.stat label="Jumlah RSVP" :value="$stats['rsvps']" />
        <x-admin.stat label="Jumlah Tetamu Hadir" :value="$stats['hadir']" />
        <x-admin.stat label="Jumlah Tetamu Tidak Hadir" :value="$stats['tidak_hadir']" />
        <x-admin.stat label="Jumlah Keseluruhan Pax" :value="$stats['pax']" />
        <x-admin.stat label="Jumlah Ucapan" :value="$stats['wishes']" />
    </section>

    <section class="mt-6 grid gap-4 lg:grid-cols-4">
        <a href="{{ route('admin.weddings.create') }}" class="card hover:ring-stone-300">Tambah Kad Kahwin</a>
        <a href="{{ route('admin.weddings.index') }}" class="card hover:ring-stone-300">Lihat Semua Kad</a>
        <a href="{{ route('admin.weddings.index') }}" class="card hover:ring-stone-300">Urus RSVP</a>
        <a href="{{ route('admin.weddings.index') }}" class="card hover:ring-stone-300">Urus Ucapan</a>
    </section>

    <section class="mt-8 grid gap-6 lg:grid-cols-2">
        <div class="card">
            <h2 class="text-lg font-semibold">Kad terbaharu</h2>
            @if ($recent->isEmpty())
                <p class="mt-4 text-sm text-stone-500">Belum ada kad kahwin. Cipta kad pertama anda.</p>
            @else
                <ul class="mt-4 divide-y divide-stone-100">
                    @foreach ($recent as $wedding)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="font-medium">{{ $wedding->coupleLabel() }}</p>
                                <p class="text-sm text-stone-500">{{ $wedding->malayDate() }}</p>
                            </div>
                            <span class="badge {{ $wedding->is_active ? 'badge-aktif' : 'badge-tidak' }}">{{ $wedding->statusLabel() }}</span>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
        <div class="card">
            <h2 class="text-lg font-semibold">Majlis akan datang</h2>
            @if ($upcoming->isEmpty())
                <p class="mt-4 text-sm text-stone-500">Tiada majlis akan datang.</p>
            @else
                <ul class="mt-4 divide-y divide-stone-100">
                    @foreach ($upcoming as $wedding)
                        <li class="flex items-center justify-between gap-3 py-3">
                            <div>
                                <p class="font-medium">{{ $wedding->coupleLabel() }}</p>
                                <p class="text-sm text-stone-500">{{ $wedding->location_name }}</p>
                            </div>
                            <p class="text-sm text-stone-600">{{ $wedding->malayDate() }}</p>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </section>
@endsection
