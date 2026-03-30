@isset($field, $id)
    @php
        $msg = $errors->first($field);
        $isBlocked = $msg && str_contains(strtolower($msg), 'blocked');
    @endphp

    @if (!$isBlocked)
        @error($field)
            <div class="invalid-feedback" id="{{ $id }}">{{ $message }}</div>
        @else
            <div class="invalid-feedback" id="{{ $id }}"></div>
        @enderror
    @else
        {{-- blocked handled by Components.alerts.auth.blocked --}}
        <div class="invalid-feedback" id="{{ $id }}"></div>
    @endif
@endisset
