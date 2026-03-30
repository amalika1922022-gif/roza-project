@extends('Front.layout')

@section('title', 'Products')

@section('content')

    {{-- ✅ Session alerts (موحّدة) --}}
    @include('components.alerts.front.session')

    {{-- ✅ Errors box (موحّد) --}}
    @include('components.alerts.front.errors_box')

    <div class="mb-3">
        <h1 class="front-section-title mb-1">All Products</h1>
        <p class="front-section-subtitle">
            Browse the full collection, and filter by category or price.
        </p>
    </div>

    <div class="row">
        {{-- FILTERS --}}
        @include('Front.partials.sidebar_filter')


        {{-- PRODUCT GRID --}}
        <div class="col-md-9" id="productsArea">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="small text-muted">
                    Showing {{ $products->firstItem() ?? 0 }}–{{ $products->lastItem() ?? 0 }}
                    of {{ $products->total() ?? $products->count() }} products
                </div>

                <form method="GET" class="d-flex align-items-center gap-2" id="frontSortForm">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request('q'))
                        <input type="hidden" name="q" value="{{ request('q') }}">
                    @endif

                    <span class="small text-muted" style="white-space: nowrap;">Sort by</span>

                    {{-- ✅ from unified form file --}}
                    @include('Front.partials.form', [
                        'key' => 'sort',
                        'value' => request('sort'),
                        'wrapperClass' => 'mb-0',
                        'showError' => false,
                        'attrs' => [
                            'onchange' => 'this.form.submit()',
                        ],
                    ])
                </form>
            </div>

            @if ($products->count())
                <div class="row g-3">
                    @foreach ($products as $product)
                        @include('Front.partials.card', [
                            'product' => $product,
                            'clickable' => true,
                            'colClass' => 'col-6 col-md-4',
                        ])
                    @endforeach
                </div>

                <div class="mt-3">
                    {{ $products->links() }}
                </div>
            @else
                <p class="text-muted">No products found matching your filters.</p>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            window.__productsIndexAllUrl = @json(route('front.products.index'));
        </script>
        <script src="{{ asset('assets/js/front/products-index.js') }}"></script>
    @endpush

@endsection
