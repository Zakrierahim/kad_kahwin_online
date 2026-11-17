@props(['style' => 'classic'])
<div class="pointer-events-none absolute inset-0 overflow-hidden" aria-hidden="true">
    @if ($style !== 'none')
        <div class="absolute inset-3 rounded-[1.6rem] border border-[color:var(--inv-primary)]/30"></div>
        <div class="absolute inset-5 rounded-[1.35rem] border border-[color:var(--inv-primary)]/15"></div>
    @endif
    @if ($style === 'songket')
        <div class="songket-frame absolute inset-0 opacity-40"></div>
    @endif
    @if (in_array($style, ['classic', 'jasmine', 'songket'], true))
        <svg class="absolute -left-3 -top-3 h-24 w-24 text-[color:var(--inv-primary)] opacity-70" viewBox="0 0 80 80" fill="none" stroke="currentColor">
            <path d="M18 46c8-18 22-24 34-18" stroke-width="1.2"/>
            <path d="M28 50c2-8 8-14 16-16" stroke-width="1.2"/>
            <circle cx="48" cy="28" r="3" fill="currentColor" stroke="none"/>
            <path d="M46 30c4 6 4 12 0 16c-5-3-7-8-6-14" stroke-width="1"/>
            <path d="M50 31c6 2 10 7 10 13c-6-1-10-5-12-11" stroke-width="1"/>
        </svg>
        <svg class="absolute -right-3 -top-3 h-24 w-24 -scale-x-100 text-[color:var(--inv-primary)] opacity-70" viewBox="0 0 80 80" fill="none" stroke="currentColor">
            <path d="M18 46c8-18 22-24 34-18" stroke-width="1.2"/>
            <circle cx="48" cy="28" r="3" fill="currentColor" stroke="none"/>
            <path d="M46 30c4 6 4 12 0 16c-5-3-7-8-6-14" stroke-width="1"/>
        </svg>
    @endif
</div>
