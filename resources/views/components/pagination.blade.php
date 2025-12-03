@if ($paginator->hasPages())
    <nav class="d-flex justify-content-end" aria-label="Page navigation">
        <ul class="pagination pagination-sm m-0">
            {{-- Tombol Previous Page --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link btn-rounded" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link btn-rounded" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&laquo;</a>
                </li>
            @endif

            {{-- Link Nomor Halaman --}}
            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                @if ($page == $paginator->currentPage())
                    <li class="page-item active" aria-current="page"><span class="page-link btn-rounded">{{ $page }}</span></li>
                @else
                    <li class="page-item"><a class="page-link btn-rounded" href="{{ $url }}">{{ $page }}</a></li>
                @endif
            @endforeach

            {{-- Tombol Next Page --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link btn-rounded" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link btn-rounded" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif