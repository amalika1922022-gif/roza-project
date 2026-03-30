@extends('Front.layout')

@section('title', 'Checkout')

@section('content')

    <h1 class="front-section-title mb-1">Checkout</h1>
    <p class="front-section-subtitle mb-3">
        Fill in your details to complete the order.
    </p>

    @include('components.alerts.front.session')
    @include('components.alerts.front.errors_box')

    <div class="row">
        {{-- FORM --}}
        <div class="col-md-7 mb-3">
            <div class="front-card">
                <form action="{{ route('front.checkout.process') }}" method="POST" id="checkoutForm" novalidate>
                    @csrf

                    {{-- ✅ Client-side errors box (موجود مثل ما هو) --}}
                    <div id="clientErrors" class="alert alert-danger d-none mb-3">
                        <ul class="mb-0" id="clientErrorsList"></ul>
                    </div>

                    <h6 class="mb-2" style="color:#4b3a42;">Shipping details</h6>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Full name --}}
                            @include('Front.partials.form', [
                                'key' => 'full_name',
                                'value' => old('full_name', optional($address)->full_name ?? optional(auth()->user())->name),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                                Please enter at least 2 words (e.g., John Smith).
                            </small>

                            <div class="invalid-feedback" id="err_full_name" @error('full_name') data-server="1" @enderror>
                                @error('full_name')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            {{-- Email --}}
                            @include('Front.partials.form', [
                                'key' => 'email',
                                'value' => old('email', optional(auth()->user())->email),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <div class="invalid-feedback" id="err_email" @error('email') data-server="1" @enderror>
                                @error('email')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            {{-- Phone --}}
                            @include('Front.partials.form', [
                                'key' => 'phone',
                                'value' => old('phone', $user->phone ?? ''),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <div class="invalid-feedback" id="err_phone" @error('phone') data-server="1" @enderror>
                                @error('phone')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Address --}}
                    @include('Front.partials.form', [
                        'key' => 'address',
                        'value' => old('address', optional($address)->address),
                        'wrapperClass' => 'mb-3',
                        'showError' => false,
                    ])

                    <div class="invalid-feedback" id="err_address" @error('address') data-server="1" @enderror>
                        @error('address')
                            {{ $message }}
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            {{-- City --}}
                            @include('Front.partials.form', [
                                'key' => 'city',
                                'value' => old('city', optional($address)->city),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <div class="invalid-feedback" id="err_city" @error('city') data-server="1" @enderror>
                                @error('city')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- Country --}}
                            @include('Front.partials.form', [
                                'key' => 'country',
                                'value' => old('country', optional($address)->country),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <div class="invalid-feedback" id="err_country" @error('country') data-server="1" @enderror>
                                @error('country')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-4">
                            {{-- Postal code --}}
                            @include('Front.partials.form', [
                                'key' => 'postal_code',
                                'value' => old('postal_code', optional($address)->postal_code),
                                'wrapperClass' => 'mb-3',
                                'showError' => false,
                            ])

                            <div class="invalid-feedback" id="err_postal_code"
                                @error('postal_code') data-server="1" @enderror>
                                @error('postal_code')
                                    {{ $message }}
                                @enderror
                            </div>
                        </div>
                    </div>

                    <h6 class="mt-3 mb-2" style="color:#4b3a42;">Payment method</h6>

                    {{-- خيار COD --}}
                    <div class="pay-method-row mb-2">
                        <div class="pay-method-text">
                            <span style="font-size:.92rem; color:#4b3a42; font-weight:600;">Cash on delivery</span>
                            <small>Pay when you receive your order.</small>
                        </div>

                        <div class="payment-switch">
                            <input type="checkbox" id="payment_cod" class="form-check-input" checked>
                        </div>
                    </div>

                    {{-- خيار Stripe --}}
                    <div class="pay-method-row mb-3">
                        <div class="pay-method-text">
                            <span style="font-size:.92rem; color:#4b3a42; font-weight:600;">Pay with card (Stripe)</span>
                            <small>Secure online payment with Visa / MasterCard.</small>
                        </div>

                        <div class="payment-switch">
                            <input type="checkbox" id="payment_stripe" class="form-check-input">
                        </div>
                    </div>

                    {{-- hidden input --}}
                    <input type="hidden" name="payment_method" id="payment_method_input" value="cod">

                    <button type="submit" class="btn btn-gradient-primary btn-sm mt-3">
                        Place order
                    </button>
                </form>
            </div>
        </div>

        {{-- SUMMARY --}}
        <div class="col-md-5 mb-3">
            <div class="front-card">
                <h6 class="mb-3" style="color:#4b3a42;">Order summary</h6>

                @if (isset($items) && $items->count())
                    <ul class="list-unstyled small mb-2" style="max-height: 220px; overflow:auto;">
                        @foreach ($items as $item)
                            @php
                                $rowTotal = $item->price_at_added * $item->quantity;
                            @endphp
                            <li class="d-flex justify-content-between mb-1">
                                <span>{{ $item->quantity }} × {{ $item->product->name }}</span>
                                <span>${{ number_format($rowTotal, 2) }}</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="small text-muted">No items in the cart.</p>
                @endif

                <hr>

                <div class="d-flex justify-content-between small mb-1">
                    <span>Subtotal</span>
                    <span>${{ number_format($subtotal ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between small mb-1">
                    <span>Shipping</span>
                    <span>${{ number_format($shipping ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between fw-semibold mt-2">
                    <span>Total</span>
                    <span>${{ number_format($total ?? 0, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            window.__checkoutOldPaymentMethod = @json(old('payment_method', 'cod'));
        </script>
        <script src="{{ asset('assets/js/front/checkout.js') }}"></script>
    @endpush
@endsection
