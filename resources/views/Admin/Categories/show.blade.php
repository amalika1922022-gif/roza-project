@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-view-grid-plus-outline"></i>
            </span>
            Category Details
        </h3>

        <div>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-sm btn-light me-2">
                <i class="mdi mdi-arrow-left"></i> Back to categories
            </a>

            <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-gradient-primary">
                <i class="mdi mdi-pencil-outline"></i> Edit
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-md-5">
            <div class="card">
                <div class="card-body">
                    <h4 class="card-title mb-3">Category info</h4>

                    <div class="mb-2">
                        <span class="text-muted d-block">Name</span>
                        <strong>{{ $category->name }}</strong>
                    </div>

                    <div class="mb-2">
                        <span class="text-muted d-block">Slug</span>
                        <code>{{ $category->slug }}</code>
                    </div>

                    @if ($category->parent)
                        <div class="mb-2">
                            <span class="text-muted d-block">Parent category</span>
                            <span class="badge bg-gradient-info text-white">
                                {{ $category->parent->name }}
                            </span>
                        </div>
                    @endif

                    @if ($category->children->count())
                        <div class="mb-2">
                            <span class="text-muted d-block">Subcategories</span>
                            @foreach ($category->children as $child)
                                <span class="badge bg-gradient-light text-dark border me-1 mb-1">
                                    {{ $child->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <div class="mb-2">
                        <span class="text-muted d-block">Description</span>
                        <p class="mb-0">
                            {{ $category->description ?: 'No description.' }}
                        </p>
                    </div>

                    <div class="mt-3">
                        <span class="text-muted d-block">Created at</span>
                        <span>{{ $category->created_at?->format('Y-m-d H:i') }}</span>
                    </div>

                </div>
            </div>
        </div>

        {{-- Products table --}}
        <div class="col-md-7">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <h4 class="card-title mb-1">Products in this category</h4>
                            <p class="card-description mb-0">
                                Total: {{ $category->products->count() }} product(s)
                            </p>
                        </div>

                        <a href="{{ route('admin.products.create') }}?category_id={{ $category->id }}"
                            class="btn btn-sm btn-gradient-primary">
                            <i class="mdi mdi-plus"></i> Add product
                        </a>
                    </div>

                    @if ($category->products->isEmpty())
                        <p class="text-muted mb-0">
                            No products in this category yet.
                        </p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Stock</th>
                                        <th>Status</th>
                                        <th class="text-end"> ""</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($category->products as $product)
                                        <tr>
                                            <td>{{ $product->id }}</td>
                                            <td>{{ $product->name }}</td>
                                            <td>${{ number_format($product->price, 2) }}</td>
                                            <td>{{ $product->stock }}</td>
                                            <td>
                                                @if ($product->is_active)
                                                    <span class="badge bg-gradient-success text-white">Active</span>
                                                @else
                                                    <span class="badge bg-gradient-secondary text-white">Hidden</span>
                                                @endif
                                            </td>
                                            <td class="text-end">
                                                {{-- View icon --}}
                                                <a href="{{ route('admin.products.show', $product->id) }}"
                                                    class="btn btn-sm btn-link p-0" title="View">
                                                    <i class="mdi mdi-eye-outline text-info" style="font-size:18px;"></i>
                                                </a>

                                                {{-- Edit icon --}}
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="btn btn-sm btn-link p-0 ms-2" title="Edit">
                                                    <i class="mdi mdi-pencil-outline text-muted"
                                                        style="font-size:18px;"></i>
                                                </a>

                                                {{-- Delete icon --}}
                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                    method="POST" class="d-inline-block ms-2"
                                                    onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit" class="btn btn-sm btn-link p-0" title="Delete">
                                                        <i class="mdi mdi-delete-outline text-danger"
                                                            style="font-size:18px;"></i>
                                                    </button>
                                                </form>
                                            </td>

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
