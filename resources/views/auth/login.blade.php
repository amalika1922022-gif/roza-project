@extends('Auth.layout')

@section('title', 'Login')

@section('content')

    <h4>Hello! let's get started</h4>
    <h6 class="font-weight-light mb-4">Sign in to continue.</h6>

    {{-- ✅ تنبيه خاص بالـ blocked فقط (صندوق فوق الحقول) --}}
    @include('Components.alerts.auth.blocked', ['field' => 'email'])

    <form class="pt-3"
          method="POST"
          action="{{ route('auth.login.post') }}"
          id="loginForm"
          novalidate>
        @csrf

        {{-- ✅ استدعاء الحقول من ملف الفورم --}}
        @include('Auth.form', [
            'fields' => ['email', 'password', 'remember'],
            'password_id' => 'login_password',
            'password_autocomplete' => 'current-password',
            'forgot_route' => route('auth.password.request'),
            'autofocus_email' => true,
        ])

        <div class="mt-3 d-grid gap-2">
            <button type="submit"
                    class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn">
                SIGN IN
            </button>
        </div>

        <div class="text-center mt-4 font-weight-light">
            Don't have an account?
            <a href="{{ route('auth.register') }}" class="text-primary">Create</a>
        </div>
    </form>

@endsection
