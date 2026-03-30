@extends('Admin.layout')

@section('content')

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-account-circle-outline"></i>
            </span>
            User Details
        </h3>

        <a href="{{ route('admin.users.index') }}" class="btn btn-light">
            Back
        </a>
    </div>

    {{-- ✅ Alerts --}}
    @include('Components.alerts.admin.session')
    @include('Components.alerts.admin.error_box')

    <div class="row mt-3">

        {{-- LEFT: Account info --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">
                        {{ $user->name }}
                    </h4>

                    <p class="mb-2"><strong>Email:</strong> {{ $user->email }}</p>

                    <p class="mb-2">
                        <strong>Role:</strong>
                        <span class="badge bg-gradient-{{ $user->role === 'admin' ? 'primary' : 'success' }} text-white">
                            {{ ucfirst($user->role) }}
                        </span>
                    </p>

                    <p class="mb-2">
                        <strong>Status:</strong>
                        @if ($user->is_blocked)
                            <span class="badge bg-gradient-danger text-white">Blocked</span>
                        @else
                            <span class="badge bg-gradient-info text-white">Active</span>
                        @endif
                    </p>

                    <p class="mb-0">
                        <strong>Joined at:</strong>
                        {{ $user->created_at?->format('Y-m-d H:i') }}
                    </p>

                    {{-- Actions --}}
                    <div class="mt-4 d-flex align-items-center gap-2">

                        <a href="{{ route('admin.users.index') }}"
                           class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center">
                            Back to users
                        </a>

                        {{-- ✅ Block / Unblock (Unified) --}}
                        @include('Admin.Users.block_unblock_form', ['user' => $user])

                    </div>

                </div>
            </div>
        </div>

        {{-- RIGHT: User orders --}}
        <div class="col-md-6 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title mb-3">User Orders</h4>

                    @if ($user->orders->count() === 0)
                        <p class="text-muted mb-0">No orders for this user.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Payment</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($user->orders as $order)
                                        <tr>
                                            <td>{{ $order->id }}</td>
                                            <td>{{ number_format($order->total, 2) }}</td>

                                            <td>
                                                <span class="badge bg-gradient-info text-white">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>

                                            <td>
                                                <span class="badge bg-gradient-{{ $order->payment_status === 'paid' ? 'success' : 'warning' }} text-white">
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>

                                            <td>{{ $order->created_at?->format('Y-m-d') }}</td>
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
