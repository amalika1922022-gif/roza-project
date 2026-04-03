@extends('Admin.layout')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-view-grid-outline"></i>
            </span>
            Categories
        </h3>

        <a href="{{ route('admin.categories.create') }}" class="btn btn-gradient-primary">
            + Add Category
        </a>
    </div>

    {{-- ✅ Alerts --}}
    @if (session('success'))
        <div class="alert alert-success mt-3 mb-0">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger mt-3 mb-0">
            {{ session('error') }}
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h4 class="card-title mb-0">All Categories</h4>

                        <div class="small text-muted">
                            Showing {{ $categories->firstItem() ?? 0 }}–{{ $categories->lastItem() ?? 0 }}
                            of {{ $categories->total() ?? $categories->count() }}
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr>
                                    <th style="width:90px;">#</th>
                                    <th>Name</th>
                                    <th>Slug</th>
                                    <th>Parent</th>
                                    <th style="width:140px;">Created</th>
                                    <th class="text-end" style="width:220px;">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($categories as $category)
                                    <tr>
                                        <td>{{ $category->id }}</td>

                                        <td>
                                            {{ $category->name }}
                                        </td>

                                        <td class="text-muted">
                                            {{ $category->slug }}
                                        </td>

                                        <td>
                                            @if ($category->parent)
                                                <span class="badge bg-gradient-info text-white">
                                                    {{ $category->parent->name }}
                                                </span>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>

                                        <td class="text-muted">
                                            {{ $category->created_at?->format('Y-m-d') }}
                                        </td>

                                        <td class="text-end">
                                            <a href="{{ route('admin.categories.show', $category) }}"
                                                class="btn btn-sm btn-outline-info">
                                                View
                                            </a>

                                            <a href="{{ route('admin.categories.edit', $category) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                Edit
                                            </a>

                                            <form action="{{ route('admin.categories.destroy', $category) }}"
                                                method="POST" class="d-inline-block form-delete-category">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            No categories found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $categories->links() }}
                    </div>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.form-delete-category').forEach(form => {

        form.addEventListener('submit', function (e) {
            e.preventDefault();

            Swal.fire({
                title: 'Delete this category?',
                text: 'This action cannot be undone',
                icon: 'question', // أخف من warning
                showCancelButton: true,

                confirmButtonText: 'Delete',
                cancelButtonText: 'Cancel',

                buttonsStyling: false, // مهم للتحكم الكامل

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
