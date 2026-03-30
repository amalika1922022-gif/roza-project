@extends('Admin.layout')

@section('content')

    {{-- Page header --}}
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-receipt"></i>
            </span>
            Order #{{ $order->id }}
        </h3>

        <a href="{{ route('admin.orders.index') }}" class="btn btn-light">
            Back to Orders
        </a>
    </div>

    {{-- ✅ Session alerts --}}
    @include('Components.alerts.admin.session')

    {{-- ✅ Global errors --}}
    @include('Components.alerts.admin.error_box')

    {{-- 1) Order Info --}}
    <div class="row mt-3">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">Order Info</h4>

                    <p><strong>ID:</strong> {{ $order->id }}</p>
                    <p><strong>UUID:</strong> {{ $order->uuid }}</p>

                    {{-- Status --}}
                    <p>
                        <strong>Status:</strong>
                        @php
                            $statusClass = match ($order->status) {
                                'pending' => 'bg-gradient-warning',
                                'processing' => 'bg-gradient-info',
                                'shipped' => 'bg-gradient-primary',
                                'delivered' => 'bg-gradient-success',
                                'cancelled' => 'bg-gradient-secondary',
                                'failed' => 'bg-gradient-danger',
                                default => 'bg-gradient-secondary',
                            };
                        @endphp
                        <span class="badge {{ $statusClass }} text-white">
                            {{ ucfirst($order->status) }}
                        </span>
                    </p>

                    {{-- Payment --}}
                    <p>
                        <strong>Payment status:</strong>
                        @php
                            $payClass = match ($order->payment_status) {
                                'unpaid' => 'bg-gradient-warning',
                                'paid' => 'bg-gradient-success',
                                'failed' => 'bg-gradient-danger',
                                'refunded' => 'bg-gradient-secondary',
                                default => 'bg-gradient-secondary',
                            };
                        @endphp
                        <span class="badge {{ $payClass }} text-white">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </p>

                    {{-- Totals --}}
                    <p><strong>Subtotal:</strong> {{ number_format($order->subtotal, 2) }}</p>
                    <p><strong>Shipping:</strong> {{ number_format($order->shipping, 2) }}</p>
                    <p><strong>Discount:</strong> {{ number_format($order->discount, 2) }}</p>
                    <p><strong>Total:</strong> {{ number_format($order->total, 2) }}</p>

                    {{-- Notes --}}
                    @if ($order->notes)
                        <p class="mt-3">
                            <strong>Notes:</strong><br>
                            {{ $order->notes }}
                        </p>
                    @endif

                    {{-- Dates --}}
                    <p class="text-muted small mt-3 mb-0">
                        Created at: {{ $order->created_at?->format('Y-m-d H:i') }}<br>
                        Updated at: {{ $order->updated_at?->format('Y-m-d H:i') }}
                    </p>

                </div>
            </div>
        </div>
    </div>

    {{-- 2) Customer & Shipping --}}
    <div class="row mt-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">Customer & Shipping</h4>

                    <div class="row">

                        {{-- Customer --}}
                        <div class="col-md-6 mb-3 mb-md-0">
                            <h5 class="mb-3">Customer</h5>

                            @if ($order->user)
                                <p><strong>Name:</strong> {{ $order->user->name }}</p>
                                <p><strong>Email:</strong> {{ $order->user->email }}</p>

                                <a href="{{ route('admin.orders.index', ['user_id' => $order->user->id]) }}"
                                    class="btn btn-sm btn-outline-primary">
                                    View all orders for this user
                                </a>
                            @else
                                <p class="text-muted">Guest order</p>
                            @endif
                        </div>

                        {{-- Shipping --}}
                        <div class="col-md-6">
                            <h5 class="mb-3">Shipping address</h5>

                            @if ($order->address)
                                <p><strong>Full name:</strong> {{ $order->address->full_name }}</p>
                                <p><strong>Address:</strong> {{ $order->address->address }}</p>
                                <p><strong>City:</strong> {{ $order->address->city }}</p>
                                <p><strong>Country:</strong> {{ $order->address->country }}</p>
                                <p><strong>Postal code:</strong> {{ $order->address->postal_code }}</p>
                            @else
                                <p class="text-muted">No address attached.</p>
                            @endif
                        </div>

                    </div>

                </div>
            </div>
        </div>
    </div>

    {{-- 3) Order Items --}}
    <div class="row mt-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">Order Items</h4>

                    @if ($order->items->count() === 0)
                        <p class="text-muted">No items found for this order.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>SKU</th>
                                        <th>Price</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->items as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->sku ?? '-' }}</td>
                                            <td>{{ number_format($item->price, 2) }}</td>
                                            <td>{{ $item->quantity }}</td>
                                            <td>{{ number_format($item->total, 2) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

    {{-- 4) Update Status --}}
    <div class="row mt-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">Update Status</h4>

                    <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label">Status *</label>
                            <select name="status" class="form-select" required>
                                @foreach ($availableStatuses as $status)
                                    <option value="{{ $status }}"
                                        {{ $order->status === $status ? 'selected' : '' }}>
                                        {{ ucfirst($status) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Note (optional)</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="Reason or internal note">{{ old('note') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-gradient-info">
                            Save Status
                        </button>
                    </form>

                </div>
            </div>
        </div>
    </div>

    {{-- 5) Status History --}}
    <div class="row mt-2">
        <div class="col-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">Status History</h4>

                    @if ($order->statusHistory->count() === 0)
                        <p class="text-muted">No status changes recorded.</p>
                    @else
                        <div class="table-responsive" style="max-height: 260px; overflow-y: auto;">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>From</th>
                                        <th>To</th>
                                        <th>Changed by</th>
                                        <th>Note</th>
                                        <th>At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->statusHistory as $history)
                                        <tr>
                                            <td>{{ ucfirst($history->previous_status) }}</td>
                                            <td>{{ ucfirst($history->new_status) }}</td>
                                            <td>{{ $history->admin?->name ?? 'System' }}</td>
                                            <td>{{ $history->note ?? '-' }}</td>
                                            <td>{{ $history->created_at?->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

@endsection
