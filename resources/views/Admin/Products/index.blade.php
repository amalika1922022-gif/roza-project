@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cube-outline"></i>
            </span>
            Products
        </h3>

        <a href="{{ route('admin.products.create') }}" class="btn btn-gradient-primary">
            + Add Product
        </a>
    </div>

    {{-- ✅ Success alert --}}
    @include('Components.alerts.admin.session')

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Products List</h4>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Active</th>
                            <th>Created at</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->category->name ?? '—' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock }}</td>
                                <td>
                                    @if($product->is_active)
                                        <span class="badge bg-gradient-success text-white">Active</span>
                                    @else
                                        <span class="badge bg-gradient-secondary text-white">Inactive</span>
                                    @endif
                                </td>
                                <td>{{ $product->created_at?->format('Y-m-d') }}</td>

                                <td class="text-end">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                       class="btn btn-sm btn-outline-info">
                                        View
                                    </a>

                                    <a href="{{ route('admin.products.edit', $product->id) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.products.destroy', $product->id) }}"
                                          method="POST"
                                          class="d-inline-block"
                                          onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-outline-danger" type="submit">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No products found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>

        </div>
    </div>

@endsection
