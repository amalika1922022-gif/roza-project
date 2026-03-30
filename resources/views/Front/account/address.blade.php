@extends('Front.layout')

@section('title', 'Shipping address')

@section('content')

    <h1 class="front-section-title mb-1">Shipping address</h1>
    <p class="front-section-subtitle mb-3">
        This address will be used during checkout.
    </p>

    {{-- ✅ Session Alerts --}}
    @include('components.alerts.front.session')

    {{-- ✅ Laravel Errors Box --}}
    @include('components.alerts.front.errors_box')

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="front-card">

                <h6 class="mb-2" style="color:#4b3a42;">Default address</h6>

                @if ($address)
                    {{-- عرض العنوان الحالي --}}
                    <div class="mb-3">
                        <div class="fw-semibold">{{ $address->full_name }}</div>
                        <div class="small text-muted">{{ $address->city }}</div>
                        <div class="small text-muted">
                            {{ $address->address }}
                            @if ($address->postal_code)
                                , {{ $address->postal_code }}
                            @endif
                        </div>
                        <div class="small text-muted">
                            {{ $address->country }}
                        </div>
                    </div>

                    <button type="button" class="btn btn-sm btn-gradient-primary" id="toggleAddressFormBtn">
                        Edit address
                    </button>
                @else
                    <p class="text-muted mb-2">
                        You don't have a saved address yet. Add one below to speed up checkout.
                    </p>
                @endif

                @php
                    // ✅ إذا في أخطاء Laravel، نعرض الفورم مباشرة حتى لو كان العنوان موجود
                    $showFormInitially = !$address || $errors->any();
                @endphp

                <form method="POST" action="{{ route('front.account.address.update') }}"
                    class="mt-3 {{ $showFormInitially ? '' : 'd-none' }}" id="addressForm" novalidate>
                    @csrf

                    {{-- Full name --}}
                    <div class="mb-2">
                        @include('Front.partials.form', [
                            'key' => 'full_name',
                            'value' => old('full_name', $address->full_name ?? $user->name),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Please enter at least 2 words (e.g., John Smith).
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'full_name', 'id' => 'err_full_name'])
                    </div>

                    {{-- Phone --}}
                    <div class="mb-2">
                        @include('Front.partials.form', [
                            'key' => 'phone',
                            'value' => old('phone', $address->phone ?? $user->phone),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Numbers only (8–15 digits).
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'phone', 'id' => 'err_phone'])
                    </div>

                    {{-- Country --}}
                    <div class="mb-2">
                        @include('Front.partials.form', [
                            'key' => 'country',
                            'value' => old('country', $address->country ?? ''),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Minimum 2 characters.
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'country', 'id' => 'err_country'])
                    </div>

                    {{-- City --}}
                    <div class="mb-2">
                        @include('Front.partials.form', [
                            'key' => 'city',
                            'value' => old('city', $address->city ?? ''),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Minimum 2 characters.
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'city', 'id' => 'err_city'])
                    </div>

                    {{-- Address --}}
                    <div class="mb-2">
                        @include('Front.partials.form', [
                            'key' => 'address',
                            'value' => old('address', $address->address ?? ''),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Minimum 5 characters.
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'address', 'id' => 'err_address'])
                    </div>

                    {{-- Postal code --}}
                    <div class="mb-3">
                        @include('Front.partials.form', [
                            'key' => 'postal_code',
                            'value' => old('postal_code', $address->postal_code ?? ''),
                            'showError' => false,
                            'wrapperClass' => 'mb-0',
                        ])

                        <small class="text-muted d-block mt-1" style="font-size:.78rem;">
                            Exactly 3 digits.
                        </small>

                        {{-- ✅ Field Validation Alert --}}
                        @include('components.alerts.front.validation', ['field' => 'postal_code', 'id' => 'err_postal_code'])
                    </div>

                    <button type="submit" class="btn btn-sm btn-gradient-primary">
                        Save address
                    </button>
                </form>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('assets/js/front/account-address.js') }}"></script>
    @endpush

@endsection
