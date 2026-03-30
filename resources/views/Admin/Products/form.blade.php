<div class="row">
    {{-- Name --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Name *</label>
            <input type="text"
                   name="name"
                   id="name"
                   class="form-control @error('name') is-invalid @enderror"
                   value="{{ old('name', $product->name ?? '') }}"
                   required>

            @include('Components.alerts.admin.validation_pro', ['field' => 'name', 'id' => 'err_name'])
        </div>
    </div>

    {{-- Slug --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="slug">Slug (optional)</label>
            <input type="text"
                   name="slug"
                   id="slug"
                   class="form-control @error('slug') is-invalid @enderror"
                   value="{{ old('slug', $product->slug ?? '') }}"
                   placeholder="auto-generated if empty">

            @include('Components.alerts.admin.validation_pro', ['field' => 'slug', 'id' => 'err_slug'])
        </div>
    </div>

    {{-- Category --}}
    <div class="col-md-6">
        <div class="form-group">
            <label for="category_id">Category *</label>
            <select name="category_id"
                    id="category_id"
                    class="form-control @error('category_id') is-invalid @enderror"
                    required>
                <option value="">Select category</option>

                @foreach($categories as $category)
                    <option value="{{ $category->id }}"
                        {{ (string) old('category_id', $product->category_id ?? request('category_id')) === (string) $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>

            @include('Components.alerts.admin.validation_pro', ['field' => 'category_id', 'id' => 'err_category_id'])
        </div>
    </div>

    {{-- Price --}}
    <div class="col-md-4">
        <div class="form-group">
            <label for="price">Price *</label>
            <input type="number"
                   step="0.01"
                   name="price"
                   id="price"
                   class="form-control @error('price') is-invalid @enderror"
                   value="{{ old('price', $product->price ?? '') }}"
                   required>

            @include('Components.alerts.admin.validation_pro', ['field' => 'price', 'id' => 'err_price'])
        </div>
    </div>

    {{-- Compare price --}}
    <div class="col-md-4">
        <div class="form-group">
            <label for="compare_price">Compare price (optional)</label>
            <input type="number"
                   step="0.01"
                   name="compare_price"
                   id="compare_price"
                   class="form-control @error('compare_price') is-invalid @enderror"
                   value="{{ old('compare_price', $product->compare_price ?? '') }}">

            @include('Components.alerts.admin.validation_pro', ['field' => 'compare_price', 'id' => 'err_compare_price'])
        </div>
    </div>

    {{-- Stock --}}
    <div class="col-md-4">
        <div class="form-group">
            <label for="stock">Stock *</label>
            <input type="number"
                   name="stock"
                   id="stock"
                   class="form-control @error('stock') is-invalid @enderror"
                   value="{{ old('stock', $product->stock ?? 0) }}"
                   min="0"
                   required>

            @include('Components.alerts.admin.validation_pro', ['field' => 'stock', 'id' => 'err_stock'])
        </div>
    </div>
</div>{{-- row --}}

{{-- Active switch + Weight --}}
<div class="row align-items-center mt-2">
    <div class="col-md-4">
        <div class="form-group d-flex align-items-center">
            <input type="hidden" name="is_active" value="0">

            <div class="switch-purple">
                <input type="checkbox"
                       class="form-check-input"
                       id="is_active"
                       name="is_active"
                       value="1"
                       {{ old('is_active', $product->is_active ?? 1) ? 'checked' : '' }}>
            </div>

            <label for="is_active" class="ms-2 mb-0">
                Product is active
            </label>
        </div>

        @error('is_active')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>

    {{-- Weight --}}
    <div class="col-md-4">
        <div class="form-group">
            <label for="weight">Weight (optional)</label>
            <input type="number"
                   step="0.01"
                   name="weight"
                   id="weight"
                   class="form-control @error('weight') is-invalid @enderror"
                   value="{{ old('weight', $product->weight ?? '') }}">

            @include('Components.alerts.admin.validation_pro', ['field' => 'weight', 'id' => 'err_weight'])
        </div>
    </div>
</div>

{{-- Description --}}
<div class="form-group mt-3">
    <label for="description">Description</label>
    <textarea name="description"
              id="description"
              rows="4"
              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description ?? '') }}</textarea>

    @error('description')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- Product Images (upload) --}}
<div class="form-group mt-3">
    <label for="images">Product Images (optional)</label>
    <input type="file"
           name="images[]"
           id="images"
           class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
           multiple>

    <small class="form-text text-muted">
        You can upload multiple images; you will be able to choose the primary one.
    </small>

    @error('images')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror

    @error('images.*')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

{{-- hidden: index الصورة الأساسية الجديدة --}}
<input type="hidden" name="primary_image_index" id="primary_image_index" value="{{ old('primary_image_index', 0) }}">

{{-- Current Images --}}
<div class="form-group mt-4">
    <label>Current Images</label>
    <div id="current-images-wrapper" class="d-flex flex-wrap">
        {{-- JS رح يضيف الـ previews هون --}}
    </div>
</div>
