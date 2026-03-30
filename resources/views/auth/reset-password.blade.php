@extends('Auth.layout')

@section('title', 'Reset Password')

@section('content')

    <h4>Reset your password</h4>
    <h6 class="font-weight-light mb-4">Enter your new password below.</h6>

    <form method="POST"
          action="{{ route('auth.password.reset.demo.submit') }}"
          id="resetForm"
          novalidate>
        @csrf

        {{-- ✅ ما نلمس القديم: لازم ينضل hidden email --}}
        <input type="hidden" name="email" value="{{ $email }}">

        {{-- ✅ Password + Password Confirmation من ملف الفورم --}}
        @include('Auth.form', [
            'fields' => ['password', 'password_confirmation'],

            // ids (حتى يشتغل toggle-password بالـ JS العام)
            'password_id' => 'password',
            'password_autocomplete' => 'new-password',
            'password_placeholder' => 'New password',

            'password_confirmation_id' => 'password_confirmation',
            'password_confirmation_placeholder' => 'Confirm password',
        ])

        <button type="submit"
                class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn w-100">
            Reset password
        </button>
    </form>

    {{-- ✅ إذا الإيميل مو موجود وراجع خطأ من السيرفر (exists) — خليها Alert تحت الفورم --}}
    @error('email')
        <div class="alert alert-danger mt-3 mb-0">
            {{ $message }}
        </div>
    @enderror

@endsection
