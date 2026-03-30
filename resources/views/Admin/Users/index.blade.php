@extends('Admin.layout')

@section('content')
    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-account-group-outline"></i>
            </span>
            Users
        </h3>
    </div>

    {{-- ✅ Alerts --}}
    @include('Components.alerts.admin.session')
    @include('Components.alerts.admin.error_box')

    {{-- Blocked users button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="card-title mb-0">Users List</h4>

        <a href="{{ route('admin.users.blocked') }}" class="btn btn-sm btn-gradient-danger">
            <i class="mdi mdi-account-cancel"></i>
            Blocked Users
        </a>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">Users List</h4>

            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th style="width: 160px;">Role</th>
                            <th style="width: 150px;">Status</th>
                            <th>Joined</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>

                                {{-- Role --}}
                                <td>
                                    <form action="{{ route('admin.users.updateRole', $user->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')

                                        <select name="role" class="form-select form-select-sm"
                                            onchange="this.form.submit()">
                                            <option value="customer" {{ $user->role === 'customer' ? 'selected' : '' }}>
                                                Customer
                                            </option>
                                            <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>
                                                Admin
                                            </option>
                                        </select>
                                    </form>
                                </td>

                                {{-- Status + Block --}}
                                <td>
                                    @include('Admin.Users.block_unblock_form', ['user' => $user])
                                </td>
                                <td>{{ $user->created_at?->format('Y-m-d') }}</td>

                                <td class="text-end">
                                    <a href="{{ route('admin.users.show', $user->id) }}"
                                        class="btn btn-sm btn-outline-info">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">No users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>

        </div>
    </div>
@endsection
