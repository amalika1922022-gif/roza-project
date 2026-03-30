{{-- Name --}}
<div class="mb-3">
    <label class="form-label">Name *</label>

    <input
        type="text"
        name="name"
        class="form-control @error('name') is-invalid @enderror"
        value="{{ old('name', $category->name ?? '') }}"
        required
    >

    @include('Components.alerts.admin.validation_cat', ['field' => 'name'])
</div>

{{-- Slug --}}
<div class="mb-3">
    <label class="form-label">Slug (optional)</label>

    <input
        type="text"
        name="slug"
        class="form-control @error('slug') is-invalid @enderror"
        value="{{ old('slug', $category->slug ?? '') }}"
    >

    @include('Components.alerts.admin.validation_cat', ['field' => 'slug'])
</div>

{{-- Parent --}}
<div class="mb-3">
    <label class="form-label">Parent Category</label>

    <select
        name="parent_id"
        class="form-control @error('parent_id') is-invalid @enderror"
    >
        <option value="">-- None --</option>

        @foreach ($parents as $parent)
            <option
                value="{{ $parent->id }}"
                {{ (string) old('parent_id', $category->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}
            >
                {{ $parent->name }}
            </option>
        @endforeach
    </select>

    @include('Components.alerts.admin.validation_cat', ['field' => 'parent_id'])
</div>
{{-- Description --}}
<div class="mb-3">
    <label class="form-label">Description</label>

    <textarea
        name="description"
        class="form-control @error('description') is-invalid @enderror"
        rows="3"
    >{{ old('description', $category->description ?? '') }}</textarea>

    @include('Components.alerts.admin.validation_cat', ['field' => 'description'])
</div>

