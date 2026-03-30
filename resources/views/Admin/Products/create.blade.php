@extends('Admin.layout')

@section('content')

<div class="page-header d-flex justify-content-between align-items-center">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
            <i class="mdi mdi-cube-outline"></i>
        </span>
        Create Product
    </h3>
</div>

<div class="card mt-3">
    <div class="card-body">
        <h4 class="card-title mb-4">New Product</h4>

        <form action="{{ route('admin.products.store') }}"
              method="POST"
              enctype="multipart/form-data"
              id="productCreateForm"
              novalidate>
            @csrf

            {{-- ✅ Product Fields (validation under each field only) --}}
            @include('Admin.products.form', [
                'categories' => $categories
            ])

            <div class="mt-4 d-flex justify-content-end">
                <a href="{{ route('admin.products.index') }}" class="btn btn-light me-2">
                    Cancel
                </a>

                <button type="submit" class="btn btn-gradient-primary" id="btnCreateProduct">
                    Create Product
                </button>
            </div>

        </form>
    </div>
</div>

@endsection
