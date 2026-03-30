@php
    $field = $field ?? 'email';
@endphp

@if ($errors->has($field) && str_contains(strtolower($errors->first($field)), 'blocked'))
    <div class="alert alert-danger py-2 px-3 mb-3">
        <small>{{ $errors->first($field) }}</small>
    </div>
@endif
