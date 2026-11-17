@php
    $description = $wedding->shareDescription();
    $pageTitle = 'Walimatulurus '.$wedding->groom_short_name.' & '.$wedding->bride_short_name;
    $cover = $wedding->coverImageUrl();
    $music = $wedding->musicUrl();
    $youtubeId = $wedding->youtubeVideoId();
    $playMusic = ($theme['music_enabled'] ?? false) && ($youtubeId || filled($music));
    $maps = $wedding->safeMapsUrl();
    $waze = $wedding->safeWazeUrl();
    $contacts = $wedding->whatsappContacts();
    $showForms = empty($preview) || $wedding->is_active;
    $bodyFont = $theme['font'] === 'sans' ? 'font-inv-sans' : 'font-inv-serif';
    $nameFont = $theme['font'] === 'script' ? 'font-inv-script' : $bodyFont;
    $rsvpStatus = old('form') === 'rsvp' ? old('status', 'hadir') : 'hadir';
    $attending = $rsvpStatus !== 'tidak_hadir';
@endphp
<!DOCTYPE html>
<html lang="ms">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $description }}">
    @if (! empty($preview))
        <meta name="robots" content="noindex, nofollow">
    @endif
    <meta property="og:locale" content="ms_MY">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $wedding->publicUrl() }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $description }}">
    @if ($cover)
        <meta property="og:image" content="{{ $cover }}">
        <meta property="og:image:alt" content="Gambar muka depan {{ $wedding->coupleLabel() }}">
    @endif
    <meta name="theme-color" content="{{ $theme['background_color'] }}">
    <link rel="canonical" href="{{ $wedding->publicUrl() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=cormorant-garamond:400,500,600,700|great-vibes:400|nunito-sans:400,600,700" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="invitation-shell {{ $theme['floral_class'] }} min-h-screen text-stone-800 antialiased" style="--inv-primary: {{ $theme['primary_color'] }}; --inv-secondary: {{ $theme['secondary_color'] }}; --inv-bg: {{ $theme['background_color'] }};" data-theme="{{ $theme['theme'] }}">
    @if (! empty($preview))
        <div class="bg-stone-900 px-4 py-3 text-center text-sm text-white">
            Mod pratonton.
            @if (! $wedding->is_active)
                Jemputan ini belum dipaparkan kepada tetamu.
            @endif
            <a href="{{ route('admin.weddings.edit', $wedding) }}" class="ml-2 underline">Kembali ke suntingan</a>
        </div>
    @endif

    <main class="invitation-card {{ $bodyFont }} relative mx-auto w-full max-w-lg sm:my-8 sm:shadow-[0_24px_60px_rgba(120,90,70,0.16)]">
        <div class="paper-edge paper-edge-top" aria-hidden="true"></div>
        <div class="paper-body relative">
            <header class="rise text-center">
                @if ($cover)
                    <img src="{{ $cover }}" alt="Gambar muka depan {{ $wedding->coupleLabel() }}" class="cover-frame mx-auto mb-8 h-72 w-52 object-cover">
                @endif
                <p class="bismillah font-inv-script text-[#5c3c3c]">Bismillahirrahmanirrahim</p>
                <p class="mx-auto mt-6 max-w-xs text-[13px] uppercase leading-6 tracking-[0.22em] text-stone-500">Assalamualaikum Warahmatullahi Wabarakatuh</p>
                <h1 class="mt-5 text-xs font-semibold uppercase tracking-[0.2em] text-[color:var(--inv-primary)]">Undangan Majlis Kesyukuran Perkahwinan</h1>
            </header>

            <x-invitation.divider />

            <section class="rise text-center" aria-labelledby="pengenalan">
                <h2 id="pengenalan" class="sr-only">Pengenalan</h2>
                @if (filled($wedding->parents_names))
                    <p class="whitespace-pre-line text-lg leading-relaxed">{{ $wedding->parents_names }}</p>
                    <p class="mx-auto mt-4 max-w-sm text-sm leading-7 text-stone-600">dengan penuh kesyukuran mempersilakan Dato' / Datin / Tuan / Puan / Encik / Cik seisi keluarga ke majlis perkahwinan putera dan puteri kami.</p>
                @else
                    <p class="mx-auto max-w-sm text-sm leading-7 text-stone-600">Dengan penuh kesyukuran, kami mempersilakan Dato' / Datin / Tuan / Puan / Encik / Cik seisi keluarga ke majlis perkahwinan ini.</p>
                @endif
            </section>

            <section class="rise mt-10 text-center" aria-labelledby="nama-pengantin">
                <h2 id="nama-pengantin" class="sr-only">Nama pengantin</h2>
                <p class="{{ $nameFont }} text-4xl font-medium leading-tight text-[color:var(--inv-primary)] sm:text-5xl">{{ $wedding->groom_full_name }}</p>
                <p class="my-3 text-sm uppercase tracking-[0.35em] text-stone-500">dengan</p>
                <p class="{{ $nameFont }} text-4xl font-medium leading-tight text-[color:var(--inv-primary)] sm:text-5xl">{{ $wedding->bride_full_name }}</p>
                @if (filled($wedding->hashtag))
                    <p class="mt-6 text-sm tracking-[0.18em] text-[color:var(--inv-primary)]">{{ $wedding->hashtag }}</p>
                @endif
            </section>

            <x-invitation.divider />

            <section class="rise text-center" aria-labelledby="kiraan">
                <h2 id="kiraan" class="text-sm uppercase tracking-[0.22em] text-stone-500">Menanti hari bahagia</h2>
                @if ($wedding->hasStarted())
                    <p class="mt-4 text-sm text-stone-600">Majlis ini sedang berlangsung atau telah pun berlangsung.</p>
                @else
                    <div class="mt-5 grid grid-cols-4" data-countdown="{{ $wedding->countdownTarget() }}">
                        @foreach (['days' => 'Hari', 'hours' => 'Jam', 'minutes' => 'Minit', 'seconds' => 'Saat'] as $unit => $label)
                            <div class="px-1 py-3">
                                <span data-unit="{{ $unit }}" class="block font-inv-serif text-3xl text-[color:var(--inv-primary)]">0</span>
                                <span class="mt-1 block text-[11px] uppercase tracking-[0.16em] text-stone-500">{{ $label }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p data-countdown-expired hidden class="mt-4 text-sm text-stone-600">Majlis ini sedang berlangsung atau telah pun berlangsung.</p>
                @endif
            </section>

            <section class="rise mt-4 text-center" aria-labelledby="maklumat-majlis">
                <h2 id="maklumat-majlis" class="text-sm uppercase tracking-[0.22em] text-[color:var(--inv-primary)]">Maklumat Majlis</h2>
                <p class="mt-4 text-2xl">{{ $wedding->malayDate() }}</p>
                <p class="mt-2 text-stone-600">{{ $wedding->malayTimeStart() }} – {{ $wedding->malayTimeEnd() }}</p>
                <p class="mt-5 text-lg font-semibold">{{ $wedding->location_name }}</p>
                <p class="mt-2 whitespace-pre-line text-sm leading-6 text-stone-600">{{ $wedding->location_address }}</p>
            </section>

            @if ($maps || $waze)
                <section class="rise mt-6 grid gap-3 sm:grid-cols-2" aria-label="Peta dan navigasi">
                    @if ($maps)
                        <a href="{{ $maps }}" target="_blank" rel="noopener noreferrer" class="btn btn-gold">Buka Google Maps</a>
                    @endif
                    @if ($waze)
                        <a href="{{ $waze }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary">Buka Waze</a>
                    @endif
                </section>
            @endif

            @if ($contacts !== [])
                <section class="rise mt-10" aria-labelledby="hubungan">
                    <h2 id="hubungan" class="text-center text-sm uppercase tracking-[0.22em] text-[color:var(--inv-primary)]">Hubungi Keluarga</h2>
                    <div class="mt-4 space-y-3">
                        @foreach ($contacts as $contact)
                            <div class="flex items-center justify-between gap-3 border-b border-[#a68484]/25 px-1 py-3">
                                <div>
                                    <p class="font-medium">{{ $contact['name'] }}</p>
                                    <p class="text-sm text-stone-500">{{ $contact['phone'] }}</p>
                                </div>
                                @if ($contact['url'])
                                    <a href="{{ $contact['url'] }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary px-3 py-2">WhatsApp</a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </section>
            @endif

            <section id="sahkan-kehadiran" class="rise mt-12" aria-labelledby="tajuk-rsvp">
                <h2 id="tajuk-rsvp" class="text-center text-sm uppercase tracking-[0.22em] text-[color:var(--inv-primary)]">Sahkan Kehadiran</h2>
                @if (! $showForms)
                    <p class="mt-4 text-center text-sm text-stone-600">Borang kehadiran dibuka selepas jemputan diaktifkan.</p>
                @else
                    @if (session('rsvp_success'))
                        <p class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-900" role="status">{{ session('rsvp_success') }}</p>
                    @endif
                    @if (old('form') === 'rsvp' && $errors->any())
                        <div data-form-error class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invitation.rsvp', $wedding->slug) }}" class="paper-panel mt-5 space-y-4 px-4 py-5" data-loading>
                        @csrf
                        <input type="hidden" name="form" value="rsvp">
                        <div class="absolute -left-[9999px] h-0 overflow-hidden" aria-hidden="true">
                            <label for="rsvp-website">Laman web</label>
                            <input id="rsvp-website" name="website_url" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <div>
                            <label class="label" for="rsvp_name">Nama Tetamu</label>
                            <input id="rsvp_name" name="rsvp_name" value="{{ old('form') === 'rsvp' ? old('rsvp_name') : '' }}" maxlength="100" required class="field">
                        </div>
                        <div>
                            <label class="label" for="phone_number">Nombor Telefon</label>
                            <input id="phone_number" name="phone_number" value="{{ old('form') === 'rsvp' ? old('phone_number') : '' }}" inputmode="tel" maxlength="20" required class="field">
                        </div>
                        <fieldset>
                            <legend class="label">Status Kehadiran</legend>
                            <div class="mt-2 grid grid-cols-2 gap-2">
                                <label class="border border-[#a68484]/30 bg-white/50 px-3 py-3 text-center text-sm">
                                    <input type="radio" name="status" value="hadir" data-rsvp-status @checked($rsvpStatus === 'hadir')> Hadir
                                </label>
                                <label class="border border-[#a68484]/30 bg-white/50 px-3 py-3 text-center text-sm">
                                    <input type="radio" name="status" value="tidak_hadir" data-rsvp-status @checked($rsvpStatus === 'tidak_hadir')> Tidak Hadir
                                </label>
                            </div>
                        </fieldset>
                        <div data-pax-wrap @unless($attending) hidden @endunless>
                            <label class="label" for="total_pax">Jumlah Pax</label>
                            <select id="total_pax" name="total_pax" data-pax-input class="field" @disabled(! $attending)>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}" @selected((string) old('total_pax', '1') === (string) $i)>{{ $i }}</option>
                                @endfor
                            </select>
                            <p class="mt-1 text-xs text-stone-500">Jika tidak hadir, jumlah pax direkodkan sebagai 0.</p>
                        </div>
                        <button type="submit" class="btn btn-gold w-full">Hantar Kehadiran</button>
                    </form>
                @endif
            </section>

            <section id="buku-ucapan" class="rise mt-12" aria-labelledby="tajuk-ucapan">
                <h2 id="tajuk-ucapan" class="text-center text-sm uppercase tracking-[0.22em] text-[color:var(--inv-primary)]">Buku Ucapan</h2>
                @if (! $showForms)
                    <p class="mt-4 text-center text-sm text-stone-600">Buku ucapan dibuka selepas jemputan diaktifkan.</p>
                @else
                    @if (session('wish_success'))
                        <p class="mt-4 rounded-2xl bg-emerald-50 px-4 py-3 text-center text-sm text-emerald-900" role="status">{{ session('wish_success') }}</p>
                    @endif
                    @if (old('form') === 'wish' && $errors->any())
                        <div data-form-error class="mt-4 rounded-2xl bg-red-50 px-4 py-3 text-sm text-red-800" role="alert">
                            <ul class="list-disc space-y-1 pl-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('invitation.wish', $wedding->slug) }}" class="mt-5 space-y-4" data-loading>
                        @csrf
                        <input type="hidden" name="form" value="wish">
                        <div class="absolute -left-[9999px] h-0 overflow-hidden" aria-hidden="true">
                            <label for="wish-website">Laman web</label>
                            <input id="wish-website" name="website_url" type="text" tabindex="-1" autocomplete="off">
                        </div>
                        <div>
                            <label class="label" for="wish_name">Nama</label>
                            <input id="wish_name" name="wish_name" value="{{ old('form') === 'wish' ? old('wish_name') : '' }}" maxlength="100" required class="field">
                        </div>
                        <div>
                            <label class="label" for="message">Ucapan</label>
                            <textarea id="message" name="message" rows="4" maxlength="500" required class="field">{{ old('form') === 'wish' ? old('message') : '' }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-secondary w-full">Hantar Ucapan</button>
                    </form>
                @endif

                <div class="mt-6 space-y-3">
                    @forelse ($wishes as $wish)
                        <article class="border-b border-[#a68484]/20 px-1 py-4">
                            <p class="font-semibold">{{ $wish->guest_name }}</p>
                            <p class="mt-2 whitespace-pre-line break-words text-sm leading-6">{{ $wish->message }}</p>
                            @if ($wish->created_at)
                                <time datetime="{{ $wish->created_at->timezone('Asia/Kuala_Lumpur')->toIso8601String() }}" class="mt-2 block text-xs text-stone-500">{{ \App\Support\MalayFormatter::dateTime($wish->created_at) }}</time>
                            @endif
                        </article>
                    @empty
                        <p class="text-center text-sm text-stone-500">Belum ada ucapan. Jadilah yang pertama.</p>
                    @endforelse
                </div>
                <div class="text-stone-600">{{ $wishes->links('partials.pagination') }}</div>
            </section>

            <footer class="mt-12 pb-8 text-center text-sm text-stone-500">
                <p class="font-inv-script text-3xl text-[color:var(--inv-primary)]">Terima kasih</p>
                <p class="mt-3">Kehadiran dan doa anda amat bermakna bagi kami.</p>
            </footer>
        </div>
        <div class="paper-edge paper-edge-bottom" aria-hidden="true"></div>
    </main>

    @if ($playMusic)
        <button type="button" data-music-toggle data-state="paused" class="music-toggle fixed bottom-5 right-5 z-30 flex h-14 w-14 items-center justify-center rounded-full border border-white/60 bg-white/70 text-[color:var(--inv-primary)] shadow-lg backdrop-blur-md" aria-label="Mainkan muzik">
            <span class="music-disc absolute inset-1 rounded-full border border-current/20"></span>
            <svg data-icon-play class="relative h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5.5v13l11-6.5-11-6.5z"/></svg>
            <svg data-icon-pause class="relative h-5 w-5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" hidden><path d="M7 5h4v14H7zM13 5h4v14h-4z"/></svg>
        </button>
        @if ($youtubeId)
            <div id="invitation-youtube" data-youtube="{{ $youtubeId }}" class="youtube-audio" aria-hidden="true"></div>
        @else
            <audio data-music loop playsinline preload="none" src="{{ $music }}"></audio>
        @endif
    @endif
</body>
</html>
