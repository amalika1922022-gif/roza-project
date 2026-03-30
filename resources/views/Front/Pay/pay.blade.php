```blade
@extends('Front.layout')

@section('title', 'Stripe Payment')

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://js.stripe.com/v3/"></script>

    <div class="container roza-checkout-wrap py-4">

        {{-- ✅ Session alerts (موحّدة) --}}
        @include('components.alerts.front.session')

        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
            <div>
                <h3 class="mb-1">Secure Payment</h3>
                <div class="roza-muted">Pay safely with Stripe.</div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <span class="roza-badge">Order #{{ $order->uuid ?? $order->id }}</span>
                {{-- ✅ تعديل 2: أضفنا roza-back-btn فقط --}}
                <a href="{{ route('front.checkout.index') }}" class="btn btn-outline-secondary roza-btn roza-back-btn">
                    Back to checkout
                </a>
            </div>
        </div>

        <div class="row g-4">
            {{-- Payment card --}}
            <div class="col-lg-7">
                <div class="card roza-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <div class="fw-bold">Card details</div>
                        <div class="roza-muted small">Powered by Stripe</div>
                    </div>

                    <div class="card-body">
                        {{-- ✅ تم حذف msgBox لأنه تنبيه قديم ومش ضروري بعد فصل التنبيهات --}}

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Card</label>
                            <div class="roza-input-like">
                                <div id="card-element"></div>
                            </div>
                            <div id="card-errors" class="roza-error mt-2"></div>
                        </div>

                        <div class="roza-divider"></div>

                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="roza-muted">
                                By clicking pay, you agree to complete this purchase.
                            </div>

                            <button id="payBtn" type="button" class="btn btn-primary roza-btn">
                                Pay {{ number_format($total, 2) }} {{ strtoupper($currency) }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 roza-muted small">
                    Tip: If your bank requires verification (3D Secure), a popup/redirect may appear.
                </div>
            </div>

            {{-- Order summary --}}
            <div class="col-lg-5">
                <div class="card roza-card roza-sticky" style="position: sticky; top: 18px;">
                    <div class="card-header">
                        <div class="fw-bold">Order summary</div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <span class="roza-muted">Subtotal</span>
                            <span class="fw-semibold">
                                {{ number_format($order->subtotal ?? $total, 2) }} {{ strtoupper($currency) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mt-2">
                            <span class="roza-muted">Shipping</span>
                            <span class="fw-semibold">
                                {{ number_format($order->shipping ?? 0, 2) }} {{ strtoupper($currency) }}
                            </span>
                        </div>

                        <div class="d-flex justify-content-between mt-2">
                            <span class="roza-muted">Discount</span>
                            <span class="fw-semibold">
                                -{{ number_format($order->discount ?? 0, 2) }} {{ strtoupper($currency) }}
                            </span>
                        </div>

                        <div class="roza-divider"></div>

                        <div class="d-flex justify-content-between">
                            <span class="fw-bold">Total</span>
                            <span class="fw-bold">{{ number_format($total, 2) }} {{ strtoupper($currency) }}</span>
                        </div>

                        <div class="mt-3 small roza-muted">
                            Payment status: <strong>{{ $order->payment_status ?? 'unpaid' }}</strong><br>
                            Order status: <strong>{{ $order->status ?? 'pending' }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ✅ Roza Modal (Popup داخل نفس الصفحة) --}}
    <div id="rozaModal"
        style="
        display:none;
        position:fixed;
        inset:0;
        background:rgba(0,0,0,.35);
        backdrop-filter: blur(6px);
        -webkit-backdrop-filter: blur(6px);
        z-index:9999;
     ">
        <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:18px;">
            <div class="card roza-card" style="width:min(520px, 100%); border-radius:16px;">
                <div class="card-body" style="padding:18px 20px;">
                    <div class="d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span id="rozaModalIcon" style="font-size:1.35rem; line-height:1;">⚠️</span>
                            <div>
                                <div id="rozaModalTitle" style="font-weight:800; font-size:1.05rem;">Payment failed</div>
                                <div id="rozaModalText" class="roza-muted" style="margin-top:4px; font-size:.95rem;"></div>
                            </div>
                        </div>
                        <button type="button" id="rozaModalX" class="btn btn-light btn-sm"
                            style="border-radius:10px;">✕</button>
                    </div>

                    <div class="mt-3 d-flex justify-content-end gap-2">
                        <button type="button" id="rozaModalOk" class="btn btn-primary roza-btn" style="padding:10px 14px;">
                            OK
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        {{-- Stripe CDN (إذا مو محطوط بالـ layout) --}}
        <script src="https://js.stripe.com/v3/"></script>

        {{-- Config للملف الخارجي --}}
        <script>
            window.__stripePayment = {
                stripeKey: @json($stripeKey),
                intentUrl: @json(route('pay.intent')),
                failUrl: @json(route('pay.fail')),
                successUrl: @json(route('pay.success')),
                csrf: @json(csrf_token()),
            };
        </script>

        <script src="{{ asset('assets/js/front/pay-stripe.js') }}"></script>
    @endpush

@endsection
```
