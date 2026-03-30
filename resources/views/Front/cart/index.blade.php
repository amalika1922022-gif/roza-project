@extends('Front.layout')

@section('title', 'Your cart')

@section('content')


    <h1 class="front-section-title mb-1">Your cart</h1>
    <p class="front-section-subtitle mb-3">
        Review your items before proceeding to checkout.
    </p>

    @include('components.alerts.front.session')

    @if (isset($items) && $items->count())
        <div class="row">
            <div class="col-md-8 mb-3">
                <div class="front-card">
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    @php
                                        $image = optional($item->product->images->first())->url;
                                        $price = $item->price_at_added;
                                        $rowTotal = $price * $item->quantity;
                                    @endphp
                                    <tr>
                                        <td>
                                            {{-- ✅ كليك على الصورة أو الاسم يفتح صفحة المنتج --}}
                                            <a href="{{ route('front.products.show', $item->product->slug) }}"
                                                class="d-flex align-items-center gap-2 text-decoration-none text-reset">
                                                <div class="cart-image-box">
                                                    @if ($image)
                                                        <img src="{{ $image }}" alt="{{ $item->product->name }}">
                                                    @else
                                                        <div class="cart-image-placeholder"></div>
                                                    @endif
                                                </div>

                                                <div>
                                                    <div class="small fw-semibold cart-product-name">
                                                        {{ $item->product->name }}
                                                    </div>
                                                    @if ($item->product->category)
                                                        <div class="small text-muted">
                                                            {{ $item->product->category->name }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </a>
                                        </td>

                                        <td>${{ number_format($price, 2) }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>${{ number_format($rowTotal, 2) }}</td>
                                        <td class="text-end">
                                            <form action="{{ route('front.cart.remove', $item->id) }}" method="POST"
                                                class="remove-item-form">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger js-remove-btn" type="button">
                                                    <i class="mdi mdi-close"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @php
                $subtotal = $totalAmount ?? $items->sum(fn($i) => $i->price_at_added * $i->quantity);
                $shipping = 0;
                $grandTotal = $subtotal + $shipping;
            @endphp

            <div class="col-md-4 mb-3">
                <div class="front-card">
                    <h6 class="mb-3" style="color:#4b3a42;">Order summary</h6>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Subtotal</span>
                        <span>${{ number_format($subtotal, 2) }}</span>
                    </div>
                    <div class="d-flex justify-content-between small mb-1">
                        <span>Shipping</span>
                        <span>${{ number_format($shipping, 2) }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-semibold mb-3">
                        <span>Total</span>
                        <span>${{ number_format($grandTotal, 2) }}</span>
                    </div>
                    @auth
                        <a href="{{ route('front.checkout.index') }}" class="btn btn-gradient-primary w-100">
                            Proceed to checkout
                        </a>
                    @else
                        <button type="button" id="checkoutGuestBtn" class="btn btn-gradient-primary w-100">
                            Proceed to checkout
                        </button>
                    @endauth
                </div>
            </div>
        </div>
    @else
        <div class="front-card text-center">
            <div class="mb-2" style="font-size:2rem;">🕊</div>
            <h2 class="front-section-title mb-1">Your cart is empty</h2>
            <p class="front-section-subtitle mb-3">
                Add a few beautiful pieces to see them here.
            </p>
            <a href="{{ route('front.products.index') }}" class="btn btn-gradient-primary btn-sm">
                Browse products
            </a>
        </div>
    @endif
    <!-- Custom Remove Item Modal -->
    <div id="removeModal" class="modal-overlay d-none">
        <div class="modal-box">
            <p class="modal-text">Are you sure you want to remove this item?</p>

            <div class="modal-actions">
                <button class="btn btn-light cancel-remove">Cancel</button>
                <button class="btn btn-gradient-danger confirm-remove">Remove</button>
            </div>
        </div>
    </div>



    <div id="loginRequiredModal"
        style="display:none; position:fixed; inset:0;
            background:rgba(0,0,0,.35);
            backdrop-filter: blur(6px);
            z-index:9999;">

        <div
            style="min-height:100vh; display:flex;
                align-items:center; justify-content:center; padding:18px;">
            <div class="card" style="max-width:420px; width:100%;
                    border-radius:16px; padding:20px;">

                <h5 class="mb-2">Login required</h5>
                <p class="text-muted mb-3">
                    Please log in to continue to checkout.
                </p>

                <div class="d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-secondary" data-close-login-modal>
                        Cancel
                    </button>

                    <a href="{{ route('auth.login') }}" class="btn btn-gradient-primary">
                        Login
                    </a>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/front/cart.js') }}"></script>
    @endpush

@endsection
