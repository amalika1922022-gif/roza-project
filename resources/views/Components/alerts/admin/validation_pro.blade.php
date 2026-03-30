@isset($field, $id)
    @error($field)
        <div class="invalid-feedback" id="{{ $id }}">{{ $message }}</div>
    @else
        <div class="invalid-feedback" id="{{ $id }}"></div>
    @enderror
@endisset
