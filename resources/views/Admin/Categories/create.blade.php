@extends('Admin.layout')

@section('content')

    {{-- Page header --}}
    <div class="page-header">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-success text-white me-2">
                <i class="mdi mdi-plus-box-outline"></i>
            </span>
            Add Category
        </h3>
    </div>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.categories.store') }}"
                  method="POST"
                  novalidate>
                @csrf

                {{-- Category fields (includes field-level validation alerts) --}}
                @include('Admin.Categories.form', ['category' => null])

                <div class="mt-3">
                    <button type="submit" class="btn btn-gradient-success">
                        Save
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
