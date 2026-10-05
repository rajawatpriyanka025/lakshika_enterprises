@if ($paginator->hasPages())
    <nav class="pagination" aria-label="Pagination">
        <span>Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}</span>
        <span class="pagination-links">
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm" aria-disabled="true">← Previous</span>
            @else
                <a class="btn btn-sm" href="{{ $paginator->previousPageUrl() }}" rel="prev">← Previous</a>
            @endif
            @if ($paginator->hasMorePages())
                <a class="btn btn-sm" href="{{ $paginator->nextPageUrl() }}" rel="next">Next →</a>
            @else
                <span class="btn btn-sm" aria-disabled="true">Next →</span>
            @endif
        </span>
    </nav>
@endif
