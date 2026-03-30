@extends('Admin.layout')

@section('content')
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-home"></i>
            </span>
            Dashboard
        </h3>
    </div>

    <div class="row">
        {{-- Orders --}}
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-danger card-img-holder text-white js-card-link"
                data-href="{{ route('admin.orders.index') }}">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Orders
                        <i class="mdi mdi-cart-outline mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $stats['orders_count'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- Products --}}
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-info card-img-holder text-white js-card-link"
                data-href="{{ route('admin.products.index') }}">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Products
                        <i class="mdi mdi-cube-outline mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $stats['products_count'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- Categories --}}
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-success card-img-holder text-white js-card-link"
                data-href="{{ route('admin.categories.index') }}">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Categories
                        <i class="mdi mdi-view-grid-outline mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $stats['categories_count'] ?? 0 }}</h2>
                </div>
            </div>
        </div>

        {{-- Customers --}}
        <div class="col-md-3 stretch-card grid-margin">
            <div class="card bg-gradient-warning card-img-holder text-white js-card-link"
                data-href="{{ route('admin.users.index') }}">
                <div class="card-body">
                    <img src="{{ asset('assets/images/dashboard/circle.svg') }}" class="card-img-absolute"
                        alt="circle-image" />
                    <h4 class="font-weight-normal mb-3">
                        Customers
                        <i class="mdi mdi-account-group-outline mdi-24px float-end"></i>
                    </h4>
                    <h2 class="mb-5">{{ $stats['customers_count'] ?? 0 }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- جدول آخر الطلبات --}}
    <div class="row mt-4">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Latest Orders</h4>
                        <a href="{{ route('admin.orders.index') }}" class="text-primary small">
                            View all
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Customer</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Created at</th>
                                    <th></th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($latestOrders as $order)
                                    <tr>
                                        <td>{{ $order->id }}</td>
                                        <td>{{ $order->user->name ?? '—' }}</td>
                                        <td>{{ number_format($order->total, 2) }}</td>
                                        <td>
                                            <span class="badge bg-gradient-info text-white">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="badge bg-gradient-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} text-white">
                                                {{ ucfirst($order->payment_status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at?->format('Y-m-d H:i') }}</td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.orders.show', $order) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Details
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7">No orders yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/admin/dashboard.js') }}"></script>
    @endpush
@endsection
