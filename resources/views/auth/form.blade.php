@php
    // fields: array مثل ['email','password']
    $fields = $fields ?? [];

    // helper بسيط
    $has = fn($key) => in_array($key, $fields, true);
@endphp

{{-- Name --}}
@if ($has('name'))
    <div class="form-group">
        <input type="text" class="form-control form-control-lg @error('name') is-invalid @enderror" name="name"
            id="name" value="{{ old('name', $name_value ?? '') }}" placeholder="{{ $name_placeholder ?? 'Name' }}"
            required autocomplete="name">
        @if (!empty($name_hint))
            <div class="field-hint">{{ $name_hint }}</div>
        @endif
        @include('Components.alerts.auth.validation', ['field' => 'name', 'id' => 'err_name'])
    </div>
@endif

{{-- Email --}}
@if ($has('email'))
    <div class="form-group">
        <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
            id="email" value="{{ old('email', $email_value ?? '') }}"
            placeholder="{{ $email_placeholder ?? 'Email' }}" required autocomplete="email"
            {{ $autofocus_email ?? true ? 'autofocus' : '' }}>
        @include('Components.alerts.auth.validation', ['field' => 'email', 'id' => 'err_email'])
    </div>
@endif
{{-- Phone --}}
@if ($has('phone'))
    <div class="form-group">
        <input type="text" class="form-control form-control-lg @error('phone') is-invalid @enderror" name="phone"
            id="phone" value="{{ old('phone', $phone_value ?? '') }}"
            placeholder="{{ $phone_placeholder ?? 'Phone' }}" required inputmode="numeric" minlength="8"
            maxlength="15" pattern="^\d{8,15}$" autocomplete="tel">

        @include('Components.alerts.auth.validation', ['field' => 'phone', 'id' => 'err_phone'])
    </div>
@endif

{{-- Password --}}
@if ($has('password'))
    <div class="form-group password-toggle-wrap">
        <div class="input-with-icon">
            <input type="password" class="form-control form-control-lg @error('password') is-invalid @enderror"
                id="{{ $password_id ?? 'password' }}" name="password"
                placeholder="{{ $password_placeholder ?? 'Password' }}" required
                autocomplete="{{ $password_autocomplete ?? 'current-password' }}">

            <button type="button" class="toggle-password" data-target="#{{ $password_id ?? 'password' }}"
                tabindex="-1">
                <i class="mdi mdi-eye-off"></i>
            </button>
        </div>

        @include('Components.alerts.auth.validation', ['field' => 'password', 'id' => 'err_password'])
    </div>
@endif

{{-- Password Confirmation --}}
@if ($has('password_confirmation'))
    <div class="form-group password-toggle-wrap">
        <div class="input-with-icon">
            <input type="password"
                class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                id="{{ $password_confirmation_id ?? 'password_confirmation' }}" name="password_confirmation"
                placeholder="{{ $password_confirmation_placeholder ?? 'Confirm Password' }}" required
                autocomplete="new-password">

            <button type="button" class="toggle-password"
                data-target="#{{ $password_confirmation_id ?? 'password_confirmation' }}" tabindex="-1">
                <i class="mdi mdi-eye-off"></i>
            </button>
        </div>

        @include('Components.alerts.auth.validation', [
            'field' => 'password_confirmation',
            'id' => 'err_password_confirmation',
        ])
    </div>
@endif

{{-- Remember + Forgot --}}
@if ($has('remember'))
    <div class="my-2 d-flex justify-content-between align-items-center">
        <div class="form-check">
            <label class="form-check-label text-muted">
                <input type="checkbox" class="form-check-input" name="remember" {{ old('remember') ? 'checked' : '' }}>
                Keep me signed in
            </label>
        </div>

        @if (!empty($forgot_route))
            <a href="{{ $forgot_route }}" class="auth-link text-primary">Forgot password?</a>
        @endif
    </div>
@endif
