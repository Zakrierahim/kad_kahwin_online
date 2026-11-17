@extends('layouts.admin')

@section('title', $wedding->exists ? 'Sunting Kad Kahwin' : 'Tambah Kad Kahwin')

@section('content')
    @php
        use App\Support\InvitationTheme;
        $settings = $wedding->decoration_settings ?? [];
        $themeKey = old('theme', $settings['theme'] ?? 'elegant_cream');
        if (! isset(InvitationTheme::THEMES[$themeKey])) {
            $themeKey = 'elegant_cream';
        }
        $preset = InvitationTheme::THEMES[$themeKey];
        $primary = old('primary_color', $settings['primary_color'] ?? $preset['primary_color']);
        $secondary = old('secondary_color', $settings['secondary_color'] ?? $preset['secondary_color']);
        $background = old('background_color', $settings['background_color'] ?? $preset['background_color']);
        $font = old('font', $settings['font'] ?? $preset['font']);
        $floral = old('floral_style', $settings['floral_style'] ?? $preset['floral_style']);
        $contacts = old('contacts', $wedding->family_contacts ?: [['name' => '', 'phone' => '']]);
        if (! is_array($contacts) || $contacts === []) {
            $contacts = [['name' => '', 'phone' => '']];
        }
        $musicEnabled = (bool) old('music_enabled', $settings['music_enabled'] ?? false);
        $active = (bool) old('is_active', $wedding->is_active ?? true);
    @endphp

    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-semibold tracking-tight">{{ $wedding->exists ? 'Sunting Kad Kahwin' : 'Tambah Kad Kahwin' }}</h1>
            <p class="mt-1 text-sm text-stone-500">Lengkapkan maklumat majlis, hiasan dan penerbitan.</p>
        </div>
        @if ($wedding->exists)
            <a href="{{ route('admin.weddings.preview', $wedding) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Pratonton</a>
        @endif
    </div>

    <form method="POST" action="{{ $wedding->exists ? route('admin.weddings.update', $wedding) : route('admin.weddings.store') }}" enctype="multipart/form-data" class="mt-6 space-y-6" data-loading>
        @csrf
        @if ($wedding->exists)
            @method('PUT')
        @endif

        <section class="card space-y-4">
            <h2 class="text-lg font-semibold">A. Maklumat Pengantin</h2>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="groom_short_name">Nama ringkas pengantin lelaki</label>
                    <input id="groom_short_name" name="groom_short_name" value="{{ old('groom_short_name', $wedding->groom_short_name) }}" class="field" required maxlength="80">
                    @error('groom_short_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="bride_short_name">Nama ringkas pengantin perempuan</label>
                    <input id="bride_short_name" name="bride_short_name" value="{{ old('bride_short_name', $wedding->bride_short_name) }}" class="field" required maxlength="80">
                    @error('bride_short_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="groom_full_name">Nama penuh pengantin lelaki</label>
                    <input id="groom_full_name" name="groom_full_name" value="{{ old('groom_full_name', $wedding->groom_full_name) }}" class="field" required maxlength="160">
                    @error('groom_full_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="bride_full_name">Nama penuh pengantin perempuan</label>
                    <input id="bride_full_name" name="bride_full_name" value="{{ old('bride_full_name', $wedding->bride_full_name) }}" class="field" required maxlength="160">
                    @error('bride_full_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="label" for="parents_names">Nama ibu bapa / keluarga</label>
                <textarea id="parents_names" name="parents_names" rows="3" class="field" maxlength="2000">{{ old('parents_names', $wedding->parents_names) }}</textarea>
                @error('parents_names')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="hashtag">Hashtag majlis</label>
                <input id="hashtag" name="hashtag" value="{{ old('hashtag', $wedding->hashtag) }}" class="field" maxlength="40" placeholder="#imandalamsyifa" autocapitalize="off" spellcheck="false">
                <p class="mt-1 text-xs text-stone-500">Contoh: #imandalamsyifa atau #syedsaidyestosakinah. Tanda # ditambah jika ditinggalkan.</p>
                @error('hashtag')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
        </section>

        <section class="card space-y-4">
            <h2 class="text-lg font-semibold">B. Maklumat Majlis</h2>
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="label" for="date">Tarikh majlis</label>
                    <input id="date" type="date" name="date" value="{{ old('date', $wedding->date?->format('Y-m-d')) }}" class="field" required>
                    @error('date')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="time_start">Masa mula</label>
                    <input id="time_start" type="time" name="time_start" value="{{ old('time_start', $wedding->timeForInput('time_start')) }}" class="field" required>
                    @error('time_start')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="time_end">Masa tamat</label>
                    <input id="time_end" type="time" name="time_end" value="{{ old('time_end', $wedding->timeForInput('time_end')) }}" class="field" required>
                    @error('time_end')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
            <div>
                <label class="label" for="location_name">Nama lokasi</label>
                <input id="location_name" name="location_name" value="{{ old('location_name', $wedding->location_name) }}" class="field" required maxlength="160">
                @error('location_name')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="location_address">Alamat penuh</label>
                <textarea id="location_address" name="location_address" rows="3" class="field" required maxlength="2000">{{ old('location_address', $wedding->location_address) }}</textarea>
                @error('location_address')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="maps_url">Google Maps URL</label>
                    <input id="maps_url" type="url" name="maps_url" value="{{ old('maps_url', $wedding->maps_url) }}" class="field" placeholder="https://">
                    @error('maps_url')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="label" for="waze_url">Waze URL</label>
                    <input id="waze_url" type="url" name="waze_url" value="{{ old('waze_url', $wedding->waze_url) }}" class="field" placeholder="https://">
                    @error('waze_url')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                </div>
            </div>
        </section>

        <section class="card space-y-4">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-semibold">C. Hubungan Keluarga</h2>
                <button type="button" class="btn btn-secondary" data-add-contact>Tambah kenalan</button>
            </div>
            <div class="space-y-3" data-contact-list>
                @foreach ($contacts as $contact)
                    <div data-contact-row class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                        <input data-contact-name name="contacts[{{ $loop->index }}][name]" value="{{ $contact['name'] ?? '' }}" placeholder="Nama" class="field mt-0" maxlength="100">
                        <input data-contact-phone name="contacts[{{ $loop->index }}][phone]" value="{{ $contact['phone'] ?? '' }}" placeholder="Nombor telefon" class="field mt-0" maxlength="20">
                        <button type="button" class="btn btn-danger" data-remove-contact>Buang</button>
                    </div>
                @endforeach
            </div>
            @error('contacts.*.name')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
            @error('contacts.*.phone')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
            <template id="contact-template">
                <div data-contact-row class="grid gap-3 sm:grid-cols-[1fr_1fr_auto]">
                    <input data-contact-name placeholder="Nama" class="field mt-0" maxlength="100">
                    <input data-contact-phone placeholder="Nombor telefon" class="field mt-0" maxlength="20">
                    <button type="button" class="btn btn-danger" data-remove-contact>Buang</button>
                </div>
            </template>
        </section>

        <section class="card space-y-4">
            <h2 class="text-lg font-semibold">D. Hiasan Kad Kahwin</h2>
            <script type="application/json" id="theme-presets">@json(InvitationTheme::presets())</script>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach (InvitationTheme::THEMES as $key => $theme)
                    <label class="flex cursor-pointer items-center justify-between gap-3 rounded-2xl border px-3 py-3 {{ $themeKey === $key ? 'border-stone-900' : 'border-stone-200' }}">
                        <span class="flex items-center gap-3">
                            <input type="radio" name="theme" value="{{ $key }}" @checked($themeKey === $key)>
                            <span class="text-sm font-medium">{{ $theme['label'] }}</span>
                        </span>
                        <span class="flex gap-1" aria-hidden="true">
                            <span class="h-4 w-4 rounded-full border border-black/10" style="background: {{ $theme['primary_color'] }}"></span>
                            <span class="h-4 w-4 rounded-full border border-black/10" style="background: {{ $theme['secondary_color'] }}"></span>
                            <span class="h-4 w-4 rounded-full border border-black/10" style="background: {{ $theme['background_color'] }}"></span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('theme')<p class="text-sm text-red-700">{{ $message }}</p>@enderror
            <p class="text-sm text-stone-500">Menukar tema akan mengisi warna, tipografi dan hiasan pratetap. Anda masih boleh mengubahnya.</p>
            <div class="grid gap-4 sm:grid-cols-3">
                @foreach (['primary_color' => 'Warna utama', 'secondary_color' => 'Warna kedua', 'background_color' => 'Warna latar'] as $name => $label)
                    @php $value = $name === 'primary_color' ? $primary : ($name === 'secondary_color' ? $secondary : $background); @endphp
                    <div>
                        <label class="label" for="{{ $name }}">{{ $label }}</label>
                        <div class="mt-1 flex gap-2">
                            <input id="{{ $name }}_picker" type="color" value="{{ $value }}" class="h-11 w-14 cursor-pointer rounded-xl border border-stone-200 bg-white">
                            <input id="{{ $name }}" name="{{ $name }}" value="{{ $value }}" class="field mt-0" maxlength="7" required>
                        </div>
                        @error($name)<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                    </div>
                @endforeach
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="label" for="font">Tipografi</label>
                    <select id="font" name="font" class="field">
                        @foreach (InvitationTheme::FONTS as $key => $label)
                            <option value="{{ $key }}" @selected($font === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="label" for="floral_style">Hiasan bunga</label>
                    <select id="floral_style" name="floral_style" class="field">
                        @foreach (InvitationTheme::FLORALS as $key => $label)
                            <option value="{{ $key }}" @selected($floral === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="label" for="cover_image">Gambar muka depan</label>
                <input id="cover_image" type="file" name="cover_image" accept="image/jpeg,image/png,image/webp" class="mt-2 block w-full text-sm">
                <p class="mt-1 text-xs text-stone-500">JPG, PNG atau WEBP. Maksimum 2MB.</p>
                @error('cover_image')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                @if ($wedding->coverImageUrl())
                    <img src="{{ $wedding->coverImageUrl() }}" alt="Gambar muka depan semasa" class="mt-3 h-28 w-24 rounded-2xl object-cover">
                    <label class="mt-2 flex items-center gap-2 text-sm text-stone-600">
                        <input type="checkbox" name="remove_cover" value="1"> Buang gambar semasa
                    </label>
                @endif
            </div>
        </section>

        <section class="card space-y-4">
            <h2 class="text-lg font-semibold">E. Muzik</h2>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="music_enabled" value="1" @checked($musicEnabled)> Hidupkan muzik latar
            </label>
            <div>
                <label class="label" for="bg_music_url">URL muzik https</label>
                <input id="bg_music_url" type="url" name="bg_music_url" value="{{ old('bg_music_url', $wedding->bg_music_url) }}" class="field" placeholder="https://www.youtube.com/watch?v=...">
                <p class="mt-1 text-xs text-stone-500">Pautan YouTube, atau pautan terus fail MP3. Halaman YouTube bukan fail audio.</p>
                @error('bg_music_url')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="label" for="music_file">Atau muat naik audio</label>
                <input id="music_file" type="file" name="music_file" accept="audio/mpeg,audio/wav,audio/ogg,audio/mp4,.mp3,.wav,.ogg,.m4a" class="mt-2 block w-full text-sm">
                <p class="mt-1 text-xs text-stone-500">MP3, WAV, OGG atau M4A. Maksimum 2MB pada pelayan semasa.</p>
                @error('music_file')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
                @if ($wedding->youtubeVideoId())
                    <p class="mt-3 text-sm text-stone-600">Pautan YouTube dikesan. Lagu dimainkan pada kad jemputan selepas tetamu mengetik halaman.</p>
                @elseif ($wedding->musicUrl())
                    <audio controls preload="none" src="{{ $wedding->musicUrl() }}" class="mt-3 w-full"></audio>
                @endif
                @if ($wedding->exists && ($wedding->bg_music_path || $wedding->bg_music_url))
                    <label class="mt-2 flex items-center gap-2 text-sm text-stone-600">
                        <input type="checkbox" name="remove_music" value="1"> Buang muzik semasa
                    </label>
                @endif
            </div>
        </section>

        <section class="card space-y-4">
            <h2 class="text-lg font-semibold">F. Penerbitan</h2>
            <div>
                <label class="label" for="slug">Slug pautan</label>
                <input id="slug" name="slug" value="{{ old('slug', $wedding->slug) }}" data-manual="{{ $wedding->exists ? '1' : '0' }}" class="field" maxlength="120" placeholder="ali-dan-fatimah">
                <p class="mt-1 text-xs text-stone-500">Dijana daripada nama ringkas. Boleh diubah. Huruf kecil, nombor dan tanda sempang sahaja.</p>
                @error('slug')<p class="mt-1 text-sm text-red-700">{{ $message }}</p>@enderror
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_active" value="1" @checked($active)> Jemputan aktif dan boleh dibuka oleh tetamu
            </label>
            @if ($wedding->exists)
                <div class="rounded-xl bg-stone-50 p-3 text-sm text-stone-600">
                    <p class="break-all">{{ $wedding->publicUrl() }}</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        <button type="button" class="btn btn-secondary" data-copy="{{ $wedding->publicUrl() }}">Salin Pautan</button>
                        <a href="{{ $wedding->whatsappShareUrl() }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Kongsi WhatsApp</a>
                    </div>
                </div>
            @else
                <p class="text-sm text-stone-500">Simpan dahulu untuk pratonton kad sebenar sebelum diaktifkan.</p>
            @endif
        </section>

        <div class="sticky bottom-3 flex flex-wrap gap-2 rounded-2xl border border-stone-200 bg-white/95 p-3 shadow-lg">
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('admin.weddings.index') }}" class="btn btn-secondary">Kembali</a>
        </div>
    </form>
@endsection
