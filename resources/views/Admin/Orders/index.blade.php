@extends('Admin.layout')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-receipt"></i>
            </span>
            Orders
        </h3>
    </div>

    {{-- ✅ Session alerts --}}
    @include('Components.alerts.admin.session')

    {{-- ✅ Global errors --}}
    @include('Components.alerts.admin.error_box')

    <div class="card mt-3">
        <div class="card-body">

            {{-- ✅ Filters --}}
            <div class="orders-head">
                <h4 class="card-title">Orders List</h4>

                <form method="GET" class="orders-filters">

                    {{-- Customer --}}
                    <div class="soft-select">
                        <i class="mdi mdi-account-outline"></i>
                        <select name="user_id">
                            <option value="">All customers</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}"
                                    {{ (string) $selectedUserId === (string) $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Status --}}
                    <div class="soft-select sm">
                        <i class="mdi mdi-progress-clock"></i>
                        <select name="status">
                            <option value="">All statuses</option>
                            @foreach ($availableStatuses as $status)
                                <option value="{{ $status }}" {{ $selectedStatus === $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-gradient-primary orders-btn">
                        <i class="mdi mdi-filter-variant"></i>
                        Filter
                    </button>

                    <a href="{{ route('admin.orders.index') }}" class="btn orders-btn orders-btn-reset">
                        <i class="mdi mdi-refresh"></i>
                        Reset
                    </a>

                </form>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Created at</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($orders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>

                                <td>
                                    @if ($order->user)
                                        <div class="mb-1">{{ $order->user->name }}</div>
                                        <div class="text-muted small">{{ $order->user->email }}</div>
                                    @else
                                        <span class="text-muted">Guest</span>
                                    @endif
                                </td>

                                <td>{{ number_format($order->total, 2) }}</td>

                                <td>
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
                                </td>

                                <td>
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
                                </td>

                                <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>

                                <td class="text-end">
                                    <a href="{{ route('admin.orders.show', $order->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No orders found.</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

            @if (method_exists($orders, 'links'))
                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            @endif

        </div>
    </div>
@endsection
