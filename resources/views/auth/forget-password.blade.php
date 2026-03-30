@extends('Auth.layout')

@section('title', 'Forgot Password')

@section('content')

    <h4>Forgot your password?</h4>
    <h6 class="font-weight-light mb-4">
        Enter your email address and we will send you a password reset link.
    </h6>

    {{-- ✅ Alerts (مرة وحدة) --}}

    <form class="pt-3"
          method="POST"
          action="{{ route('auth.password.email') }}"
          id="forgetForm"
          novalidate>
        @csrf

        {{-- ✅ Email فقط من ملف الفورم --}}
        @include('Auth.form', [
            'fields' => ['email'],
            'email_placeholder' => 'Email',
            'autofocus_email' => true
        ])

        <div class="mt-3 d-grid gap-2">
            <button type="submit"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                Send reset link
            </button>
        </div>

        <div class="text-center mt-4 font-weight-light">
            <a href="{{ route('auth.login') }}" class="text-primary">
                Back to login
            </a>
        </div>
    </form>

@endsection
