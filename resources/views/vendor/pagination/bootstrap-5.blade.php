@if ($paginator->hasPages())
    <nav class="d-flex align-items-center justify-content-between gap-3"
         style="min-height: 28px;">

        {{-- ✅ Showing results --}}
        <div class="pagination-info small text-muted"
             style="margin:0; line-height:28px;">
            Showing
            <span class="fw-semibold">{{ $paginator->firstItem() }}</span>
            to
            <span class="fw-semibold">{{ $paginator->lastItem() }}</span>
            of
            <span class="fw-semibold">{{ $paginator->total() }}</span>
            results
        </div>

        {{-- ✅ Pagination --}}
        <ul class="pagination mb-0 align-items-center"
            style="margin:0 !important; height:28px; display:flex; align-items:center;">

            {{-- Previous --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true"
                    style="display:flex; align-items:center;">
                    <span class="page-link"
                          aria-hidden="true"
                          style="line-height:28px; position:relative; top:-1px;">
                        &lsaquo;
                    </span>
                </li>
            @else
                <li class="page-item" style="display:flex; align-items:center;">
                    <a class="page-link"
                       href="{{ $paginator->previousPageUrl() }}"
                       rel="prev"
                       style="line-height:28px; position:relative; top:-1px;">
                        &lsaquo;
                    </a>
                </li>
            @endif

            {{-- Pages --}}
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li class="page-item disabled" style="display:flex; align-items:center;">
                        <span class="page-link" style="line-height:28px;">
                            {{ $element }}
                        </span>
                    </li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"
                                style="display:flex; align-items:center;">
                                <span class="page-link" style="line-height:28px;">
                                    {{ $page }}
                                </span>
                            </li>
                        @else
                            <li class="page-item" style="display:flex; align-items:center;">
                                <a class="page-link"
                                   href="{{ $url }}"
                                   style="line-height:28px;">
                                    {{ $page }}
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next --}}
            @if ($paginator->hasMorePages())
                <li class="page-item" style="display:flex; align-items:center;">
                    <a class="page-link"
                       href="{{ $paginator->nextPageUrl() }}"
                       rel="next"
                       style="line-height:28px; position:relative; top:-1px;">
                        &rsaquo;
                    </a>
                </li>
            @else
                <li class="page-item disabled" style="display:flex; align-items:center;">
                    <span class="page-link"
                          aria-hidden="true"
                          style="line-height:28px; position:relative; top:-1px;">
                        &rsaquo;
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
