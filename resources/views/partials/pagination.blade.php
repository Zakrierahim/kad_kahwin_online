@if ($paginator->hasPages())
    <nav class="mt-6 flex items-center justify-between gap-3 text-sm" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="text-stone-400">Sebelum</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="font-medium underline decoration-[color:var(--inv-primary,#b08d57)] underline-offset-4">Sebelum</a>
        @endif
        <span>Halaman {{ $paginator->currentPage() }} daripada {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="font-medium underline decoration-[color:var(--inv-primary,#b08d57)] underline-offset-4">Seterusnya</a>
        @else
            <span class="text-stone-400">Seterusnya</span>
        @endif
    </nav>
@endif
