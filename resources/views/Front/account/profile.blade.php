@extends('Front.layout')

@section('title', 'My profile')

@section('content')

    <h1 class="front-section-title mb-1">My profile</h1>
    <p class="front-section-subtitle mb-3">
        Manage your personal information and navigate to your orders and address.
    </p>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="front-card">
                <div class="d-flex align-items-center mb-3">
                    <div style="
                        width:56px;
                        height:56px;
                        border-radius:50%;
                        background:linear-gradient(135deg,#f1c4d7,#8f5fd3);
                        display:flex;
                        align-items:center;
                        justify-content:center;
                        color:#fff;
                        font-weight:700;
                        font-size:1.4rem;">
                        {{ strtoupper(substr($user->name,0,1)) }}
                    </div>
                    <div class="ms-3">
                        <div class="fw-semibold">{{ $user->name }}</div>
                        <div class="small text-muted">{{ $user->email }}</div>

                        @if ($user->phone)
                            <div class="small text-muted mt-1">
                                {{ $user->phone }}
                            </div>
                        @else
                            <div class="small text-muted mt-1">
                                No phone number added
                            </div>
                        @endif
                    </div>
                </div>

                <div class="small text-muted mb-2">
                    Member since {{ $user->created_at?->format('M Y') }}
                </div>

                <div class="d-flex flex-wrap gap-2 mt-3">
                    <a href="{{ route('front.account.orders') }}" class="btn btn-sm btn-gradient-primary">
                        My orders
                    </a>
                    <a href="{{ route('front.account.address') }}" class="btn btn-sm btn-light">
                        Address
                    </a>
                </div>
            </div>
        </div>
    </div>

@endsection
