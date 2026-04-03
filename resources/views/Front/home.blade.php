@extends('Front.layout')

@section('title', 'Home')

@section('content')

    {{-- =========================
        ✅ Alerts (Front)
        - session
        - floating_alert موجود بالـ layout
    ========================= --}}
    @include('components.alerts.front.session')
    {{-- ========================= --}}

    {{-- HERO --}}
    <div class="front-hero mb-4">
        <div class="row align-items-center hero-row-center">
            <div class="col-md-6 mb-4 mb-md-0">

                {{-- ✅ HERO PRODUCT CAROUSEL بدل الصورة + إطار --}}
                <div class="hero-carousel-frame mb-3">
                    <div class="hero-carousel" id="heroProductCarousel">
                        <div class="hero-carousel-viewport">
                            <div class="hero-carousel-track">
                                @if (isset($latestProducts) && $latestProducts->count())
                                    @foreach ($latestProducts->take(6) as $p)
                                        @php
                                            $img = optional($p->images->first())->url ?? null;
                                        @endphp

                                        <div class="hero-slide">
                                            <a href="{{ route('front.products.show', $p->slug) }}" class="hero-slide-link">

                                                {{-- ✅ اسم المنتج فوق كل صورة --}}
                                                <div class="hero-slide-title">
                                                    {{ \Illuminate\Support\Str::limit($p->name, 40) }}
                                                </div>

                                                @if ($img)
                                                    <img src="{{ $img }}" alt="{{ $p->name }}">
                                                @else
                                                    <div class="hero-slide-empty"></div>
                                                @endif

                                            </a>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- fallback لو ما في منتجات --}}
                                    <div class="hero-slide">
                                        <a href="{{ route('front.products.index') }}" class="hero-slide-link">
                                            <div class="hero-slide-title">
                                                Home Decor Collection
                                            </div>

                                            <img src="https://images.pexels.com/photos/4109995/pexels-photo-4109995.jpeg?auto=compress&cs=tinysrgb&w=800"
                                                alt="Decor">
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- arrows --}}
                        <button type="button" class="hero-nav-btn hero-nav-prev" data-hero-prev>
                            <i class="mdi mdi-chevron-left"></i>
                        </button>
                        <button type="button" class="hero-nav-btn hero-nav-next" data-hero-next>
                            <i class="mdi mdi-chevron-right"></i>
                        </button>

                        {{-- dots --}}
                        <div class="hero-dots" data-hero-dots></div>
                    </div>
                </div>
            </div>

            <div class="col-md-auto ps-4 pe-0">
                <div class="hero-ref-title">
                    ELEVATE YOUR SPACE
                </div>

                <div class="hero-ref-script mb-2">
                    Curated Comfort. Timeless Style.
                </div>

                <p class="hero-ref-desc">
                    Discover hand-picked home essentials that blend seamlessly into your lifestyle.
                    Transform your space with warmth and beauty.
                </p>

                <a href="{{ route('front.products.index') }}" class="btn btn-sm btn-gradient-primary mt-3">
                    SHOP COLLECTION
                </a>
            </div>

        </div>
    </div>

    {{-- FEATURED CATEGORIES --}}
    <section class="mb-4">
        <div class="text-center mb-3">
            <h2 class="front-section-title">Modern Home Decor Essentials</h2>
            <p class="front-section-subtitle">
                Curated categories to help you style your space with minimal, warm pieces.
            </p>
        </div>

        @if (isset($featuredCategories) && $featuredCategories->count())
            {{-- سكروول أفقي للكروت --}}
            <div class="front-category-scroll">
                <div class="front-category-track">
                    @foreach ($featuredCategories as $category)
                        <a href="{{ route('front.products.index', ['category' => $category->slug]) }}"
                            class="text-decoration-none">
                            <div class="front-card front-category-card">
                                <div class="mb-2">
                                    <i class="mdi mdi-shape-outline text-primary"></i>
                                </div>
                                <div class="front-category-card-title">
                                    {{ $category->name }}
                                </div>
                                <div class="front-category-card-subtitle mt-1">
                                    View products
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @else
            <p class="text-muted text-center">
                Categories will appear here once you add them from the admin panel.
            </p>
        @endif
    </section>

    {{-- LATEST PRODUCTS --}}
    <section class="mt-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <div class="front-section-title mb-1">Latest products</div>
                <div class="front-section-subtitle">
                    Hand picked pieces from your collection.
                </div>
            </div>
            <a href="{{ route('front.products.index') }}" class="small text-primary">
                View all
            </a>
        </div>

        @if (isset($latestProducts) && $latestProducts->count())
            <div class="row g-3 front-products-grid">
                @foreach ($latestProducts as $product)
                    @include('Front.partials.card', [
                        'product' => $product,
                        'clickable' => false,
                        'colClass' => 'col-6 col-md-3',
                    ])
                @endforeach
            </div>
        @else
            <p class="text-muted">
                No products yet. Once you add products from the admin panel, they will be listed here.
            </p>
        @endif
    </section>

    @push('scripts')
        <script src="{{ asset('assets/js/front/home.js') }}"></script>
    @endpush

@endsection
