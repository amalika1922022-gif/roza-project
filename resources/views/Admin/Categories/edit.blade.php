@extends('Admin.layout')

@section('content')

    {{-- Page header --}}
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-warning text-white me-2">
                <i class="mdi mdi-pencil-outline"></i>
            </span>
            Edit Category
        </h3>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.categories.update', $category) }}"
                  method="POST"
                  novalidate>
                @csrf
                @method('PUT')

                {{-- Category fields (field-level validation alerts only) --}}
                @include('Admin.Categories.form', ['category' => $category])

                <div class="mt-3">
                    <button type="submit" class="btn btn-gradient-warning">
                        Update
                    </button>

                    <a href="{{ route('admin.categories.index') }}"
                       class="btn btn-light">
                        Cancel
                    </a>
                </div>

            </form>

        </div>
    </div>

@endsection
