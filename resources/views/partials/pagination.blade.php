@if ($paginator->hasPages())
    <nav class="pager" aria-label="Halaman">
        @if ($paginator->onFirstPage())
            <span class="pager-btn is-off">Sebelumnya</span>
        @else
            <a class="pager-btn" href="{{ $paginator->previousPageUrl() }}">Sebelumnya</a>
        @endif
        <span class="pager-info">Halaman {{ $paginator->currentPage() }} dari {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="pager-btn" href="{{ $paginator->nextPageUrl() }}">Berikutnya</a>
        @else
            <span class="pager-btn is-off">Berikutnya</span>
        @endif
    </nav>
@endif
