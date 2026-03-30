@php
/**
 * Usage:
 * @include('Front.partials.form', ['key' => 'email', 'value' => old('email')])
 *
 * Optional:
 * - value         (mixed)
 * - attrs         (array)  // overrides / extra attrs
 * - showError     (bool)
 * - wrapperClass  (string)
 * - selected      (mixed)  // optional for select
 */

if (!isset($key)) {
    throw new InvalidArgumentException('Form field "key" is required.');
}

/*
|--------------------------------------------------------------------------
| Field Registry (ALL fields defined here)
| key MUST equal the actual field name (q, sort, quantity, email, ...)
|--------------------------------------------------------------------------
*/
$fields = [

    // ======================
    // Checkout / Address
    // ======================
    'full_name' => [
        'type' => 'input',
        'name' => 'full_name',
        'label' => 'Full name',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'placeholder' => 'First name Last name',
        'attrs' => [
            'id' => 'full_name',
            'minlength' => 3,
        ],
    ],

    'email' => [
        'type' => 'input',
        'name' => 'email',
        'label' => 'Email',
        'inputType' => 'email',
        'required' => true,
        'class' => 'form-control-sm',
        'attrs' => [
            'id' => 'email',
            'autocomplete' => 'email',
        ],
    ],

    'phone' => [
        'type' => 'input',
        'name' => 'phone',
        'label' => 'Phone',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'attrs' => [
            'id' => 'phone',
            'inputmode' => 'numeric',
            'pattern' => '[0-9]{8,15}',
            'minlength' => 8,
            'maxlength' => 15,
            'title' => 'Phone must be numbers only (8 to 15 digits).',
        ],
    ],

    'country' => [
        'type' => 'input',
        'name' => 'country',
        'label' => 'Country',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'placeholder' => 'e.g., Netherlands',
        'attrs' => [
            'id' => 'country',
            'minlength' => 2,
        ],
    ],

    'city' => [
        'type' => 'input',
        'name' => 'city',
        'label' => 'City',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'placeholder' => 'e.g., Amsterdam',
        'attrs' => [
            'id' => 'city',
            'minlength' => 2,
        ],
    ],

    'address' => [
        'type' => 'input',
        'name' => 'address',
        'label' => 'Address',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'placeholder' => 'Street + number',
        'attrs' => [
            'id' => 'address',
            'minlength' => 5,
        ],
    ],

    'postal_code' => [
        'type' => 'input',
        'name' => 'postal_code',
        'label' => 'Postal code',
        'inputType' => 'text',
        'required' => true,
        'class' => 'form-control-sm',
        'placeholder' => 'e.g., 123',
        'attrs' => [
            'id' => 'postal_code',
            'inputmode' => 'numeric',
            'pattern' => '[0-9]{3}',
            'minlength' => 3,
            'maxlength' => 3,
            'title' => 'Postal code must be exactly 3 digits.',
        ],
    ],

    // ======================
    // Products Filters
    // ======================
    'q' => [
        'type' => 'input',
        'name' => 'q',
        'label' => null,
        'inputType' => 'text',
        'required' => false,
        'class' => 'form-control-sm',
        'placeholder' => 'Search products',
        'attrs' => [
            'id' => 'q',
            'autocomplete' => 'off',
        ],
    ],

    'sort' => [
        'type' => 'select',
        'name' => 'sort',
        'label' => null,
        'required' => false,
        'class' => 'form-select-sm',
        'placeholder' => null,
        'options' => [
            '' => 'Default',
            'price_asc' => 'Price: low to high',
            'price_desc' => 'Price: high to low',
            'latest' => 'Newest',
        ],
        'attrs' => [
            'id' => 'sort',
        ],
    ],

    // ======================
    // Quantity (Product / Cart)
    // ======================
    'quantity' => [
        'type' => 'input',
        'name' => 'quantity',
        'label' => null,
        'inputType' => 'number',
        'required' => true,
        'class' => 'form-control-sm',
        'attrs' => [
            'min' => 1,
        ],
    ],
];

/*
|--------------------------------------------------------------------------
| Resolve field
|--------------------------------------------------------------------------
*/
if (!isset($fields[$key])) {
    throw new InvalidArgumentException("Unknown form field key: {$key}");
}

$f = $fields[$key];

$name         = $f['name'];
$type         = $f['type'] ?? 'input';
$label        = $f['label'] ?? null;
$class        = $f['class'] ?? '';
$required     = $f['required'] ?? false;
$placeholder  = $f['placeholder'] ?? '';
$wrapperClass = $wrapperClass ?? 'mb-2';
$showError    = $showError ?? true;

// merge attrs (override allowed)
$attrs = array_merge($f['attrs'] ?? [], $attrs ?? []);

// old() handling
$value = old($name, $value ?? '');

// select handling
$options  = $f['options'] ?? [];
$selected = old($name, $selected ?? $value);

// input defaults
$inputType = $f['inputType'] ?? 'text';

// textarea defaults
$rows = $f['rows'] ?? 4;
@endphp

<div class="{{ $wrapperClass }}">
    @if($label)
        <label for="{{ $attrs['id'] ?? $name }}" class="form-label small">
            {{ $label }} @if($required)<span class="text-danger">*</span>@endif
        </label>
    @endif

    @if($type === 'input')
        <input
            type="{{ $inputType }}"
            name="{{ $name }}"
            value="{{ $value }}"
            placeholder="{{ $placeholder }}"
            class="form-control {{ $class }} @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            @foreach($attrs as $k => $v) {{ $k }}="{{ $v }}" @endforeach
        >

    @elseif($type === 'textarea')
        <textarea
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            class="form-control {{ $class }} @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            @foreach($attrs as $k => $v) {{ $k }}="{{ $v }}" @endforeach
        >{{ $value }}</textarea>

    @elseif($type === 'select')
        <select
            name="{{ $name }}"
            class="form-select {{ $class }} @error($name) is-invalid @enderror"
            {{ $required ? 'required' : '' }}
            @foreach($attrs as $k => $v) {{ $k }}="{{ $v }}" @endforeach
        >
            @if(!is_null($placeholder))
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach($options as $optValue => $optText)
                <option value="{{ $optValue }}" @selected((string)$selected === (string)$optValue)>
                    {{ $optText }}
                </option>
            @endforeach
        </select>
    @endif

    @if($showError)
        @error($name)
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    @endif
</div>
