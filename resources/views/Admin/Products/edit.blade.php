@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cube-outline"></i>
            </span>
            Edit Product
        </h3>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title mb-4">Update Product</h4>

            <form action="{{ route('admin.products.update', $product->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="productEditForm"
                  novalidate>
                @csrf
                @method('PUT')

                {{-- ✅ Product Fields (validation under each field only) --}}
                @include('Admin.products.form', [
                    'categories' => $categories,
                    'product'    => $product
                ])

                {{-- hidden inputs لإدارة حالة الصور --}}
                @php
                    $currentPrimary = $product->images->firstWhere('is_primary', true);
                @endphp

                <input type="hidden" name="primary_image_id" id="primary_image_id"
                       value="{{ old('primary_image_id', optional($currentPrimary)->id) }}">

                <input type="hidden" name="primary_new_image_index" id="primary_new_image_index"
                       value="{{ old('primary_new_image_index') }}">

                <input type="hidden" name="deleted_image_ids" id="deleted_image_ids" value="">

                {{-- Current + new images --}}
                <div class="form-group mt-4">
                    <label>Current Images</label>

                    <div id="current-images-wrapper" class="d-flex flex-wrap">
                        {{-- صور موجودة في الداتابيس --}}
                        @if ($product->images && $product->images->count())
                            @foreach ($product->images as $image)
                                @php $isPrimary = $image->is_primary; @endphp

                                <div class="position-relative me-3 mb-3 existing-image product-image-item {{ $isPrimary ? 'is-primary' : '' }}"
                                     style="width: 100px;"
                                     data-image-id="{{ $image->id }}">

                                    <div style="width: 100px; height: 100px; overflow: hidden; border-radius: .35rem;">
                                        <img src="{{ $image->url ?? asset('storage/' . $image->file_path) }}"
                                             alt="product image"
                                             class="img-fluid w-100 h-100"
                                             style="object-fit: cover;">
                                    </div>

                                    <button type="button"
                                            class="btn btn-xs w-100 mt-1 btn-set-primary-existing {{ $isPrimary ? 'btn-gradient-primary' : 'btn-outline-primary' }}"
                                            data-image-id="{{ $image->id }}"
                                            style="font-size: 11px; padding: 2px 4px;">
                                        {{ $isPrimary ? 'Primary' : 'Set as primary' }}
                                    </button>

                                    <button type="button"
                                            class="btn btn-xs btn-outline-danger w-100 mt-1 btn-delete-existing-image"
                                            data-image-id="{{ $image->id }}"
                                            style="font-size: 11px; padding: 2px 4px;">
                                        Delete
                                    </button>
                                </div>
                            @endforeach
                        @endif

                        {{-- صور الـ preview رح تنضاف هون بالـ JS --}}
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light me-2">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-gradient-primary">
                        Update Product
                    </button>
                </div>

            </form>

        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/admin/products-edit.js') }}"></script>
    @endpush

@endsection
