@extends('Front.layout')

@section('title', 'My orders')

@section('content')

    <h1 class="front-section-title mb-1">My orders</h1>
    <p class="front-section-subtitle mb-3">
        Track your past purchases and their status.
    </p>

    {{-- ✅ Session Alerts --}}
    @include('components.alerts.front.session')

    {{-- ✅ Laravel Errors Box (اختياري بس ثابت ضمن نظام التنبيهات) --}}
    @include('components.alerts.front.errors_box')

    <div class="front-card">
        @if ($orders->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 10%;">#</th>
                            <th style="width: 20%;">Date</th>
                            <th style="width: 20%;">Total</th>
                            <th style="width: 20%;">Status</th>
                            <th style="width: 30%;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            @php
                                $status = $order->status;
                                $class = match ($status) {
                                    'pending' => 'bg-warning',
                                    'processing' => 'bg-info',
                                    'shipped' => 'bg-primary',
                                    'delivered' => 'bg-success',
                                    'cancelled' => 'bg-secondary',
                                    'failed' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
                            @endphp

                            {{-- السطر الرئيسي للطلب --}}
                            <tr class="js-order-toggle" style="cursor:pointer;">
                                <td>
                                    <span class="text-decoration">{{ $order->id }}</span>
                                </td>
                                <td>{{ $order->created_at?->format('Y-m-d') }}</td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td>
                                    <span class="badge badge-status {{ $class }} text-white">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <span class="small text-muted">
                                        Click to view details
                                        <i class="mdi mdi-chevron-down ms-1"></i>
                                    </span>
                                </td>
                            </tr>

                            {{-- سطر التفاصيل --}}
                            <tr class="order-details-row d-none">
                                <td colspan="5">
                                    <div class="p-3 rounded" style="background:#faf5ff;">
                                        <div class="d-flex justify-content-between flex-wrap mb-2">
                                            <div class="small">
                                                <strong>Order ID:</strong> {{ $order->id }}<br><br>
                                                <strong>Placed on:</strong> {{ $order->created_at?->format('Y-m-d H:i') }}
                                            </div>
                                            <div class="small text-end">
                                                <strong>Total:</strong> ${{ number_format($order->total, 2) }}<br><br>
                                                <strong>Status:</strong>
                                                <span class="badge badge-status {{ $class }} text-white">
                                                    {{ ucfirst($status) }}
                                                </span>
                                            </div>
                                        </div>

                                        {{-- عناصر الطلب --}}
                                        @if ($order->items && $order->items->count())
                                            <div class="table-responsive mb-2">
                                                <table class="table table-sm mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Product</th>
                                                            <th class="text-center" style="width: 80px;">Qty</th>
                                                            <th class="text-end" style="width: 120px;">Price</th>
                                                            <th class="text-end" style="width: 120px;">Total</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($order->items as $item)
                                                            <tr>
                                                                <td class="small">
                                                                    {{ $item->name ?? ($item->product->name ?? 'Product') }}
                                                                </td>
                                                                <td class="text-center small">
                                                                    {{ $item->quantity }}
                                                                </td>
                                                                <td class="text-end small">
                                                                    ${{ number_format($item->price, 2) }}
                                                                </td>
                                                                <td class="text-end small">
                                                                    ${{ number_format($item->total, 2) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            <p class="small text-muted mb-2">
                                                No items found for this order.
                                            </p>
                                        @endif

                                        {{-- العنوان --}}
                                        @if ($order->address)
                                            <div class="small text-muted mt-2">
                                                <strong>Shipping address:</strong><br>
                                                {{ $order->address->full_name ?? '' }}<br>
                                                {{ $order->address->address ?? '' }}<br>
                                                {{ $order->address->city ?? '' }},
                                                {{ $order->address->country ?? '' }}
                                                @if ($order->address->postal_code)
                                                    – {{ $order->address->postal_code }}
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-2">
                {{ $orders->links() }}
            </div>
        @else
            <p class="text-muted mb-0">You have no orders yet.</p>
        @endif
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/front/account-orders.js') }}"></script>
    @endpush

@endsection
