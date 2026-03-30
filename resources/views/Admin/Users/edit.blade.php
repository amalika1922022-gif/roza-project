@extends('Admin.layout')

@section('content')

<div class="page-header">
    <h3 class="page-title">
        <span class="page-title-icon bg-gradient-warning text-white me-2">
            <i class="mdi mdi-account-edit-outline"></i>
        </span>
        Edit User Role
    </h3>
</div>

{{-- ✅ Alerts --}}
@include('Components.alerts.admin.session')
@include('Components.alerts.admin.error_box')

<div class="card mt-3">
    <div class="card-body">

        <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">User Name</label>
                <input type="text" class="form-control" value="{{ $user->name }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="text" class="form-control" value="{{ $user->email }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Role *</label>
                <select name="role" class="form-control" required>
                    <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>
                        Customer
                    </option>
                    <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                        Admin
                    </option>
                </select>
            </div>

            <button class="btn btn-gradient-warning">
                Update Role
            </button>

            <a href="{{ route('admin.users.index') }}" class="btn btn-light ms-2">
                Cancel
            </a>

        </form>

    </div>
</div>

@endsection
