@extends('Front.layout')

@section('title', 'Payment Successful')

@section('content')

    {{-- ✅ Session alerts (موحّدة) --}}
    @include('components.alerts.front.session')

    <div class="container success-wrap py-5">
        <div class="success-card">

            <div class="text-center">
                <div class="success-icon">✓</div>
                <h2 class="success-title mb-2">Payment successful</h2>
                <p class="success-muted mb-0">
                    Thank you! Your payment has been completed successfully.
                </p>
            </div>

            <div class="success-divider"></div>

            {{-- Order & Payment Info --}}
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h6 class="mb-3">Order information</h6>

                    <div class="info-row">
                        <span>Order ID</span>
                        <strong>#{{ $order->uuid ?? $order->id }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Order status</span>
                        <strong>{{ ucfirst($order->status) }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Payment status</span>
                        <strong>{{ ucfirst($order->payment_status) }}</strong>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <h6 class="mb-3">Payment details</h6>

                    <div class="info-row">
                        <span>Amount paid</span>
                        <strong>
                            {{ number_format($payment->amount, 2) }}
                            {{ strtoupper($payment->currency) }}
                        </strong>
                    </div>

                    <div class="info-row">
                        <span>Payment method</span>
                        <strong>Stripe</strong>
                    </div>

                    <div class="info-row">
                        <span>Transaction ID</span>
                        <strong>{{ $payment->provider_payment_id }}</strong>
                    </div>

                    <div class="info-row">
                        <span>Stripe status</span>
                        <strong>{{ $stripeStatus }}</strong>
                    </div>
                </div>
            </div>

            <div class="success-divider"></div>

            <div class="d-flex justify-content-between flex-wrap gap-2">
                <a href="{{ route('front.account.orders') }}" class="btn btn-gradient-primary">
                    View my orders
                </a>

                <a href="{{ route('front.home') }}" class="btn roza-btn-outline-black">
                    Continue shopping
                </a>

            </div>
        </div>
    </div>
@endsection
