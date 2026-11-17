@extends('layouts.admin')

@section('title', 'RSVP '.$wedding->coupleLabel())

@section('content')
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <p class="text-sm text-stone-500"><a href="{{ route('admin.weddings.index') }}" class="underline">Kad Kahwin</a></p>
            <h1 class="text-3xl font-semibold tracking-tight">RSVP {{ $wedding->coupleLabel() }}</h1>
        </div>
        <a href="{{ route('admin.weddings.rsvps.export', array_merge(['wedding' => $wedding], request()->only(['q', 'status']))) }}" class="btn btn-secondary">Eksport CSV</a>
    </div>

    <section class="mt-6 grid gap-4 sm:grid-cols-4">
        <x-admin.stat label="Jumlah RSVP" :value="$stats['total']" />
        <x-admin.stat label="Hadir" :value="$stats['hadir']" />
        <x-admin.stat label="Tidak Hadir" :value="$stats['tidak_hadir']" />
        <x-admin.stat label="Jumlah pax disahkan" :value="$stats['pax']" />
    </section>

    <form method="GET" class="mt-6 grid gap-3 sm:grid-cols-[1fr_12rem_auto]">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama tetamu" class="field mt-0">
        <select name="status" class="field mt-0">
            <option value="">Semua status</option>
            <option value="hadir" @selected(request('status') === 'hadir')>Hadir</option>
            <option value="tidak_hadir" @selected(request('status') === 'tidak_hadir')>Tidak hadir</option>
        </select>
        <button class="btn btn-secondary" type="submit">Cari</button>
    </form>

    @if ($rsvps->isEmpty())
        <div class="card mt-6 text-center">
            <h2 class="text-lg font-semibold">Belum ada RSVP</h2>
            <p class="mt-2 text-sm text-stone-500">Rekod akan muncul di sini selepas tetamu menghantar borang.</p>
        </div>
    @else
        <div class="mt-6 overflow-x-auto rounded-2xl bg-white ring-1 ring-stone-200/80">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-stone-50 text-stone-500">
                    <tr>
                        <th class="px-4 py-3 font-medium">Nama Tetamu</th>
                        <th class="px-4 py-3 font-medium">Nombor Telefon</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Jumlah Pax</th>
                        <th class="px-4 py-3 font-medium">Tarikh RSVP</th>
                        <th class="px-4 py-3 font-medium">Tindakan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-stone-100">
                    @foreach ($rsvps as $rsvp)
                        <tr class="align-top">
                            <td class="px-4 py-3 font-medium">{{ $rsvp->guest_name }}</td>
                            <td class="px-4 py-3">{{ $rsvp->phone_number }}</td>
                            <td class="px-4 py-3"><span class="badge {{ $rsvp->status === 'hadir' ? 'badge-aktif' : 'badge-tidak' }}">{{ $rsvp->statusLabel() }}</span></td>
                            <td class="px-4 py-3">{{ $rsvp->total_pax }}</td>
                            <td class="px-4 py-3">{{ $rsvp->created_at ? \App\Support\MalayFormatter::dateTime($rsvp->created_at) : '' }}</td>
                            <td class="px-4 py-3">
                                <details>
                                    <summary class="cursor-pointer text-sm font-medium">Lihat</summary>
                                    <dl class="mt-2 space-y-1 text-xs text-stone-600">
                                        <div><dt class="inline font-medium">Nama:</dt> <dd class="inline">{{ $rsvp->guest_name }}</dd></div>
                                        <div><dt class="inline font-medium">Telefon:</dt> <dd class="inline">{{ $rsvp->phone_number }}</dd></div>
                                        <div><dt class="inline font-medium">Status:</dt> <dd class="inline">{{ $rsvp->statusLabel() }}</dd></div>
                                        <div><dt class="inline font-medium">Pax:</dt> <dd class="inline">{{ $rsvp->total_pax }}</dd></div>
                                    </dl>
                                </details>
                                <form method="POST" action="{{ route('admin.weddings.rsvps.destroy', [$wedding, $rsvp]) }}" class="mt-2" onsubmit="return confirm('Padam RSVP ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm text-red-700">Padam</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="text-stone-600">{{ $rsvps->links('partials.pagination') }}</div>
    @endif
@endsection
