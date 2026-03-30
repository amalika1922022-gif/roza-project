{{-- FILTERS --}}
<div class="col-md-3 mb-3">
    <div class="front-card">
        <h6 class="mb-3 front-filter-title">Filter</h6>

        @if (isset($categories) && $categories->count())
            <div class="mb-3">
                <div class="front-filter-section-label">Category</div>

                <div class="front-filter-cats" id="frontFilterCats">
                    <ul class="list-unstyled mb-0">

                        <li>
                            <a href="{{ route('front.products.index') }}"
                                class="front-filter-link {{ !request('category') ? 'is-active' : '' }}">
                                <span class="front-filter-dot"></span>
                                <span class="front-filter-text">All</span>
                                <span class="front-filter-badge">
                                    {{ $allProductsCount ?? ($products->total() ?? $products->count()) }}
                                </span>
                            </a>
                        </li>

                        @foreach ($categories as $parent)
                            @php
                                $parentCount = (int) ($parent->products_count ?? 0);
                                foreach ($parent->children ?? [] as $ch) {
                                    $parentCount += (int) ($ch->products_count ?? 0);
                                }
                            @endphp

                            <li>
                                <a href="{{ route('front.products.index', ['category' => $parent->slug]) }}"
                                    class="front-filter-link is-parent {{ request('category') === $parent->slug ? 'is-active' : '' }}">
                                    <span class="front-filter-dot"></span>
                                    <span class="front-filter-text">{{ $parent->name }}</span>
                                    <span class="front-filter-badge">{{ $parentCount }}</span>
                                </a>
                            </li>

                            @foreach ($parent->children ?? [] as $child)
                                <li class="front-filter-child">
                                    <a href="{{ route('front.products.index', ['category' => $child->slug]) }}"
                                        class="front-filter-link {{ request('category') === $child->slug ? 'is-active' : '' }}">
                                        <span class="front-filter-dot"></span>
                                        <span class="front-filter-text">{{ $child->name }}</span>

                                        @if (!is_null($child->products_count))
                                            <span class="front-filter-badge">{{ $child->products_count }}</span>
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        @endforeach

                    </ul>
                </div>
            </div>
        @endif

        <div class="front-filter-divider"></div>

        <div class="mb-3">
            <div class="front-filter-section-label">Search</div>

            <form method="GET" action="{{ route('front.products.index') }}" id="frontSearchForm">
                @if (request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif
                @if (request('sort'))
                    <input type="hidden" name="sort" value="{{ request('sort') }}">
                @endif

                <div class="input-group input-group-sm">
                    @include('Front.partials.form', [
                        'key' => 'q',
                        'value' => request('q'),
                        'showError' => false,
                        'wrapperClass' => 'mb-0',
                        'attrs' => [
                            'placeholder' => 'Search products',
                            'autocomplete' => 'off',
                        ],
                    ])

                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="mdi mdi-magnify"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
