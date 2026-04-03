@extends('Front.layout')

@section('title', $product->name)

@section('content')


    <style>
        /* إذا بدك شي هون */
    </style>

    @php
        $images = $product->images ?? collect();

        // ترتيب الصور: الأساسية أولاً ثم الباقي بالـ sort_order
        $ordered = $images->sortBy('sort_order');
        $primary = $ordered->firstWhere('is_primary', true) ?? $ordered->first();
        $carouselImages = $primary ? collect([$primary])->merge($ordered->where('id', '!=', $primary->id)) : collect();

        // لو ما في ولا صورة → $mainSrc = null
        $firstImage = $carouselImages->first();
        $mainSrc = $firstImage ? $firstImage->url ?? asset('storage/' . $firstImage->file_path) : null;

        // أقصى كمية مسموحة (لو ما في stock نحط 1 بس لسلامة الفورم)
        $maxStock = max(1, (int) ($product->stock ?? 1));
    @endphp

    <div class="row">
        {{-- الصور --}}
        <div class="col-md-6 mb-3">
            <div class="front-card position-relative">

                <div class="position-relative mb-3">
                    @if ($mainSrc)
                        <img id="mainProductImage" src="{{ $mainSrc }}" alt="{{ $product->name }}"
                            class="front-product-main-img">
                    @else
                        {{-- ما في صور للمنتج → كرت أبيض بنفس الأبعاد --}}
                        <div id="mainProductImage" class="front-product-main-img"></div>
                    @endif

                    @if ($carouselImages->count() > 1)
                        <button type="button" class="front-product-arrow left" id="prevImageBtn">
                            <i class="mdi mdi-chevron-left"></i>
                        </button>
                        <button type="button" class="front-product-arrow right" id="nextImageBtn">
                            <i class="mdi mdi-chevron-right"></i>
                        </button>
                    @endif
                </div>

                {{-- الصور الصغيرة – سطر واحد مع scroll أفقي --}}
                @if ($carouselImages->count() > 1)
                    <div class="front-product-thumbs mt-2" id="thumbStrip">
                        @foreach ($carouselImages as $idx => $img)
                            @php
                                $thumbSrc = $img->url ?? asset('storage/' . $img->file_path);
                            @endphp
                            <button type="button" class="front-product-thumb-btn {{ $idx === 0 ? 'active' : '' }}"
                                data-index="{{ $idx }}">
                                <img src="{{ $thumbSrc }}" alt="thumb" class="front-product-thumb-img">
                            </button>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>

        {{-- معلومات المنتج --}}
        <div class="col-md-6 mb-3">
            <div class="front-card h-100">
                @if ($product->category)
                    <div class="front-product-category mb-1">
                        {{ $product->category->name }}
                    </div>
                @endif

                <h1 class="front-section-title mb-1">{{ $product->name }}</h1>

                <div class="front-product-price mb-2" style="font-size:1.3rem;">
                    ${{ number_format($product->price, 2) }}
                </div>

                @if ($product->short_description ?? null)
                    <p class="front-section-subtitle mb-2">
                        {{ $product->short_description }}
                    </p>
                @endif

                {{-- ✅ Add to cart عبر AJAX + نفس فلو اليرت العائم من الـ layout --}}
                <form action="{{ route('front.cart.add') }}" method="POST" class="mt-2 mb-3 js-add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="small text-muted">Quantity</span>

                        {{-- ✅ Quantity via unified form file --}}
                        @include('Front.partials.form', [
                            'key' => 'quantity',
                            'value' => 1,
                            'wrapperClass' => 'mb-0',
                            'showError' => false,
                            'attrs' => [
                                'min' => 1,
                                'style' => 'width: 80px;',
                                'id' => 'productQuantityInput',
                            ],
                        ])
                    </div>

                    {{-- =========================
                        ✅ "Hint" Alert (Front)
                        هذا مو session ولا errors_box
                        هذا hint خاص بالكمية وبيتعامل معه common.js
                    ========================= --}}
                    <div class="small text-muted mb-2">
                        Available: {{ $product->stock ?? 0 }} pcs
                        <span id="qtyHint" class="ms-1 d-none js-qty-hint"></span>
                    </div>
                    {{-- ========================= --}}

                    <button type="submit" class="btn btn-gradient-primary btn-sm me-2">
                        <i class="mdi mdi-cart-plus me-1"></i> Add to cart
                    </button>

                    <a href="{{ route('front.products.index') }}" class="btn btn-sm btn-light">
                        Back to products
                    </a>
                </form>

                @if ($product->description ?? null)
                    <h6 class="mt-3" style="color:#4b3a42;">Details</h6>
                    <div class="small text-muted">
                        {!! nl2br(e($product->description)) !!}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.__productShow = {
                images: @json(
                    ($carouselImages->count() > 1)
                        ? $carouselImages->map(fn($img) => $img->url ?? asset('storage/' . $img->file_path))->values()
                        : []
                ),
                hasCarousel: @json($carouselImages->count() > 1),
                maxStock: @json($maxStock),
            };
        </script>

        <script src="{{ asset('assets/js/front/product-show.js') }}"></script>
    @endpush

@endsection
