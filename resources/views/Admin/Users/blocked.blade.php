@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-danger text-white me-2">
                <i class="mdi mdi-account-cancel"></i>
            </span>
            Blocked Users
        </h3>

        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
            Back to Users
        </a>
    </div>

    {{-- ✅ Alerts --}}
    @include('Components.alerts.admin.session')
    @include('Components.alerts.admin.error_box')

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title">List of Blocked Users</h4>

            @if ($users->count() === 0)
                <p class="text-muted mt-3">There are no blocked users.</p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th class="text-center">#</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th class="text-center">Role</th>
                                <th class="text-center">Blocked At</th>
                                <th class="text-center">Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($users as $user)
                                <tr>
                                    <td class="text-center">{{ $user->id }}</td>

                                    <td>{{ $user->name }}</td>

                                    <td>{{ $user->email }}</td>

                                    <td class="text-center">
                                        <span
                                            class="badge bg-gradient-{{ $user->role === 'admin' ? 'primary' : 'success' }} text-white">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>

                                    <td class="text-center">
                                        {{ $user->updated_at?->format('Y-m-d H:i') }}
                                    </td>

                                    {{-- Status --}}
                                    <td class="text-center">
                                        @include('Admin.Users.block_unblock_form', ['user' => $user])
                                    </td>
                                    {{-- Actions --}}
                                    <td class="text-center">
                                        <a href="{{ route('admin.users.show', $user->id) }}"
                                            class="btn btn-sm btn-outline-info">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $users->links() }}
                </div>
            @endif

        </div>
    </div>

@endsection
