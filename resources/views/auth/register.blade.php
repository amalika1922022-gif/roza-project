@extends('Auth.layout')

@section('title', 'Register')

@section('content')

    <h4>New here?</h4>
    <h6 class="font-weight-light mb-4">
        Signing up is easy. It only takes a few steps.
    </h6>

    {{-- ✅ Alerts --}}
    {{-- @include('Components.alerts.auth.session') --}}

    <form class="pt-3"
          method="POST"
          action="{{ route('auth.register.post') }}"
          id="registerForm"
          novalidate>
        @csrf

        @include('Auth.form', [
            'fields' => ['name', 'email', 'phone', 'password', 'password_confirmation'],

            // name
            'name_placeholder' => 'Full name',
            'name_hint' => 'At least 2 words (e.g., John Smith).',

            // password
            'password_id' => 'password',
            'password_autocomplete' => 'new-password',
            'password_confirmation_id' => 'password_confirmation',
        ])

        <div class="mt-3 d-grid gap-2">
            <button type="submit"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                SIGN UP
            </button>
        </div>

        <div class="text-center mt-4 font-weight-light">
            Already have an account?
            <a href="{{ route('auth.login') }}" class="text-primary">Login</a>
        </div>
    </form>

@endsection
