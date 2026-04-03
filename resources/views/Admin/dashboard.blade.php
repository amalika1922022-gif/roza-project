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

    <div class="row mt-4">
        <div class="col-md-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">Homepage Carousel Products</h4>
                    </div>

                    @include('Components.alerts.admin.session')

                    <form action="{{ route('admin.homepage-carousel.store') }}" method="POST" class="row g-3 mb-4">
                        @csrf

                        <div class="col-md-8">
                            <label for="product_id" class="form-label">Select product</label>
                            <select name="product_id" id="product_id" class="form-control" required>
                                <option value="">Choose a product</option>
                                @foreach ($allProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="sort_order" class="form-label">Order</label>
                            <input type="number" name="sort_order" id="sort_order" class="form-control" min="1"
                                placeholder="Auto">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-gradient-primary w-100">
                                Add
                            </button>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:90px;">#</th>
                                    <th>Image</th>
                                    <th>Product</th>
                                    <th style="width:120px;">Order</th>
                                    <th class="text-end" style="width:160px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($homepageCarouselItems as $item)
                                    @php
                                        $img =
                                            optional($item->product->images->first())->url ??
                                            (optional($item->product->images->first())->file_path
                                                ? asset(
                                                    'storage/' . optional($item->product->images->first())->file_path,
                                                )
                                                : null);
                                    @endphp

                                    <tr>
                                        <td>{{ $item->id }}</td>

                                        <td>
                                            @if ($img)
                                                <img src="{{ $img }}" alt="{{ $item->product->name }}"
                                                    style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                                            @else
                                                <div
                                                    style="width:60px; height:60px; background:#fff; border:1px solid #eee; border-radius:8px;">
                                                </div>
                                            @endif
                                        </td>

                                        <td>{{ $item->product->name ?? '—' }}</td>
                                        <td>
    <form action="{{ route('admin.homepage-carousel.update', $item->id) }}"
          method="POST"
          class="d-flex align-items-center gap-2">
        @csrf
        @method('PUT')

        <input type="number"
               name="sort_order"
               value="{{ $item->sort_order }}"
               min="1"
               class="form-control form-control-sm"
               style="width: 90px;">

        <button type="submit" class="btn btn-sm btn-outline-primary">
            Save
        </button>
    </form>
</td>

<td class="text-end">
    <form action="{{ route('admin.homepage-carousel.destroy', $item->id) }}"
          method="POST"
          class="d-inline-block form-delete-carousel-item">
        @csrf
        @method('DELETE')

        <button type="submit" class="btn btn-sm btn-outline-danger">
            Remove
        </button>
    </form>
</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No products added to homepage carousel yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.form-delete-carousel-item').forEach(form => {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();

                        Swal.fire({
                            title: 'Remove this product?',
                            text: 'It will no longer appear in the homepage carousel',
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonText: 'Remove',
                            cancelButtonText: 'Cancel',
                            buttonsStyling: false,
                            customClass: {
                                popup: 'swal-clean',
                                confirmButton: 'btn btn-gradient-primary me-2',
                                cancelButton: 'btn btn-light'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    });
                });
            });
        </script>
    @endpush
@endsection
