@php
    $field = $field ?? null;
    $id = $id ?? null;
@endphp

@if($field && $id)
    @error($field)
        <div class="invalid-feedback" id="{{ $id }}" data-server="1">{{ $message }}</div>
    @else
        <div class="invalid-feedback" id="{{ $id }}"></div>
    @enderror
@endif
