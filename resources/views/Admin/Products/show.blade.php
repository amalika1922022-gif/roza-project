@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-eye-outline"></i>
            </span>
            Product Details
        </h3>

        <a href="{{ route('admin.products.index') }}" class="btn btn-light">
            Back to list
        </a>
    </div>

    <div class="row mt-3">

        {{-- LEFT: Product details --}}
        <div class="col-md-7 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">
                        {{ $product->name }}
                    </h4>

                    {{-- Basic info --}}
                    <p class="mb-2"><strong>Category:</strong> {{ $product->category->name ?? '—' }}</p>
                    <p class="mb-2"><strong>Slug:</strong> {{ $product->slug }}</p>
                    <p class="mb-2"><strong>SKU:</strong> {{ $product->sku }}</p>

                    {{-- Pricing & stock --}}
                    <p class="mt-3 mb-0">
                        <strong>Price:</strong> {{ number_format($product->price, 2) }}<br>
                        <strong>Compare price:</strong>
                        {{ $product->compare_price ? number_format($product->compare_price, 2) : '—' }}<br>
                        <strong>Stock:</strong> {{ $product->stock }}<br>
                        <strong>Weight:</strong> {{ $product->weight ?? '—' }}
                    </p>

                    {{-- Status --}}
                    <p class="mt-3 mb-0">
                        <strong>Status:</strong>
                        @if ($product->is_active)
                            <span class="badge bg-gradient-success text-white">Active</span>
                        @else
                            <span class="badge bg-gradient-secondary text-white">Inactive</span>
                        @endif
                    </p>

                    {{-- Description --}}
                    <p class="mt-3 mb-0">
                        <strong>Description:</strong><br>
                        {{ $product->description ?? 'No description.' }}
                    </p>

                    {{-- Dates --}}
                    <p class="mt-3 mb-0">
                        <strong>Created at:</strong> {{ $product->created_at?->format('Y-m-d H:i') }}<br>
                        <strong>Updated at:</strong> {{ $product->updated_at?->format('Y-m-d H:i') }}
                    </p>

                    {{-- Actions --}}
                    <div class="mt-3">
                        <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-gradient-primary">
                            Edit Product
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- RIGHT: Images --}}
        <div class="col-md-5 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title">Images</h4>

                    @if ($product->images && $product->images->count())

                        <div class="admin-product-images-scroll">
                            <div class="d-flex flex-wrap gap-3">
                                @foreach ($product->images as $image)
                                    <div style="width: 120px;">

                                        <div style="width:120px; height:120px; overflow:hidden; border-radius:.5rem;">
                                            <img src="{{ $image->url ?? asset('storage/' . $image->file_path) }}"
                                                 alt="product image"
                                                 style="width:100%; height:100%; object-fit:cover; display:block;">
                                        </div>

                                        @if ($image->is_primary)
                                            <span class="badge bg-gradient-success text-white mt-2 d-inline-block">
                                                Primary
                                            </span>
                                        @endif

                                    </div>
                                @endforeach
                            </div>
                        </div>

                    @else
                        <p class="mb-0">No images for this product.</p>
                    @endif

                </div>
            </div>
        </div>

    </div>

@endsection
