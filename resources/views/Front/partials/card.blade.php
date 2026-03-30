@php
    // المطلوب: $product
    // اختياري: $clickable (true/false) - افتراضي false
    // اختياري: $colClass (كلاس الأعمدة) - افتراضي: col-6 col-md-3
    $clickable = $clickable ?? false;
    $colClass  = $colClass ?? 'col-6 col-md-3';

    $image = optional($product->images->first())->url ?? null;
    $showUrl = route('front.products.show', $product->slug);
@endphp

<div class="{{ $colClass }}">
    <div
        class="front-product-card {{ $clickable ? 'product-card-clickable' : '' }}"
        @if($clickable) data-href="{{ $showUrl }}" @endif
    >

        {{-- الصورة --}}
        @if($clickable)
            {{-- نسخة clickable: بدون رابط (الكرت كله يفتح التفاصيل) --}}
            @if ($image)
                <img src="{{ $image }}" class="front-product-img" alt="{{ $product->name }}">
            @else
                <div class="front-product-img" style="background:#fff;"></div>
            @endif
        @else
            {{-- نسخة home: الصورة رابط --}}
            <a href="{{ $showUrl }}">
                @if ($image)
                    <img src="{{ $image }}" class="front-product-img" alt="{{ $product->name }}">
                @else
                    <div class="front-product-img" style="background:#fff;"></div>
                @endif
            </a>
        @endif

        <div class="front-product-body">
            @if ($product->category)
                <div class="front-product-category">
                    {{ $product->category->name }}
                </div>
            @endif

            {{-- الاسم --}}
            @if($clickable)
                <div class="front-product-name">
                    {{ \Illuminate\Support\Str::limit($product->name, 45) }}
                </div>
            @else
                <a href="{{ $showUrl }}" class="text-decoration-none text-reset">
                    <div class="front-product-name">
                        {{ \Illuminate\Support\Str::limit($product->name, 40) }}
                    </div>
                </a>
            @endif

            <div class="d-flex justify-content-between align-items-center mt-2 front-product-actions">
                <div class="front-product-price">
                    ${{ number_format($product->price, 2) }}
                </div>

                <form action="{{ route('front.cart.add') }}" method="POST" class="js-add-to-cart">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <button
                        type="submit"
                        class="btn btn-sm btn-gradient-primary"
                        @if($clickable) onclick="event.stopPropagation();" @endif
                    >
                        <i class="mdi mdi-cart-plus"></i>
                    </button>
                </form>
            </div>

            <a
                href="{{ $showUrl }}"
                class="small d-inline-block mt-1 text-muted text-decoration-none"
                @if($clickable) onclick="event.stopPropagation();" @endif
            >
                View details
            </a>
        </div>
    </div>
</div>
