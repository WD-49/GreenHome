    @props(['paginator'])
    @php
        $tab = request('tab') ?? ''; // Hoặc truyền vào nếu muốn rõ ràng hơn
    @endphp
    @if ($paginator->hasPages())
        <nav class="d-flex justify-content-end mt-3" aria-label="Pagination">
            <ul class="pagination">
                {{-- Previous Page Link --}}

                @if ($paginator->onFirstPage())
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">‹</span>
                    </li>
                @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev">‹</a>
                    </li>
                @endif

                {{-- Pagination Elements --}}
                {{-- Pagination Elements --}}
                @foreach ($paginator->links()->elements[0] as $page => $url)
                    @php
                        $urlWithTab = $url . (Str::contains($url, '?') ? '&' : '?') . 'tab=' . request()->get('tab');
                    @endphp
                    @if ($page == $paginator->currentPage())
                        <li class="page-item active" aria-current="page">
                            <span class="page-link">{{ $page }}</span>
                        </li>
                    @else
                        <li class="page-item">
                            <a class="page-link" href="{{ $urlWithTab }}">{{ $page }}</a>
                        </li>
                    @endif
                @endforeach

                {{-- Next Page Link --}}
                @if ($paginator->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next">›</a>
                    </li>
                @else
                    <li class="page-item disabled" aria-disabled="true">
                        <span class="page-link">›</span>
                    </li>
                @endif
            </ul>
        </nav>
    @endif
