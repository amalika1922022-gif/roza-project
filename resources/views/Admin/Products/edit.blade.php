@extends('Admin.layout')

@section('content')

    <style>
        .switch-purple .form-check-input {
            width: 44px;
            height: 24px;
            margin: 0;
            cursor: pointer;
            -webkit-appearance: none;
            appearance: none;
            background-color: #e4e6f7;
            border-radius: 999px;
            position: relative;
            border: none;
            outline: none;
            transition: background-color 0.2s ease-in-out;
        }

        .switch-purple .form-check-input::before {
            content: "";
            position: absolute;
            top: 3px;
            left: 3px;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background-color: #ffffff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease-in-out;
        }

        .switch-purple .form-check-input:checked {
            background-image: linear-gradient(90deg, #b66dff, #5e72e4);
        }

        .switch-purple .form-check-input:checked::before {
            transform: translateX(20px);
        }

        /* تمييز الصورة الأساسية في الواجهة */
        .product-image-item.is-primary {
            border-radius: .35rem;
        }

        /* ✅ نفس نمط الفرونت/اللوغ: inline errors */
        .invalid-feedback { display: block; }

        /* ✅ إخفاء أيقونة التعجب (Bootstrap invalid icon) بدون ما نغير باقي الستايل */
        .form-control.is-invalid {
            background-image: none !important;
            padding-right: .75rem !important;
        }
    </style>

    <div class="page-header d-flex justify-content-between align-items-center">
        <h3 class="page-title">
            <span class="page-title-icon bg-gradient-primary text-white me-2">
                <i class="mdi mdi-cube-outline"></i>
            </span>
            Edit Product
        </h3>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mt-3">
        <div class="card-body">
            <h4 class="card-title mb-4">Update Product</h4>

            <form action="{{ route('admin.products.update', $product->id) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  id="productEditForm"
                  novalidate>
                @csrf
                @method('PUT')

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $product->name) }}" required>

                            {{-- ✅ نفس اللي عملناه بالفرونت: مكان ثابت للخطأ (Laravel + JS) --}}
                            <div class="invalid-feedback" id="err_name">
                                @error('name') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug (optional)</label>
                            <input type="text" name="slug" id="slug"
                                   class="form-control @error('slug') is-invalid @enderror"
                                   value="{{ old('slug', $product->slug) }}" placeholder="auto-generated if empty">

                            <div class="invalid-feedback" id="err_slug">
                                @error('slug') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="category_id">Category *</label>
                            <select name="category_id" id="category_id"
                                    class="form-control @error('category_id') is-invalid @enderror" required>
                                <option value="">Select category</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>

                            <div class="invalid-feedback" id="err_category_id">
                                @error('category_id') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" step="0.01" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   value="{{ old('price', $product->price) }}" required>

                            <div class="invalid-feedback" id="err_price">
                                @error('price') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Compare price --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="compare_price">Compare price (optional)</label>
                            <input type="number" step="0.01" name="compare_price" id="compare_price"
                                   class="form-control @error('compare_price') is-invalid @enderror"
                                   value="{{ old('compare_price', $product->compare_price) }}">

                            <div class="invalid-feedback" id="err_compare_price">
                                @error('compare_price') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Stock --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" name="stock" id="stock"
                                   class="form-control @error('stock') is-invalid @enderror"
                                   value="{{ old('stock', $product->stock) }}" min="0" required>

                            <div class="invalid-feedback" id="err_stock">
                                @error('stock') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Active + Weight --}}
                <div class="row align-items-center mt-2">
                    <div class="col-md-4">
                        <div class="form-group d-flex align-items-center">
                            <input type="hidden" name="is_active" value="0">
                            <div class="switch-purple">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                       value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            </div>
                            <label for="is_active" class="ms-2 mb-0">
                                Product is active
                            </label>
                        </div>
                        @error('is_active')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="weight">Weight (optional)</label>
                            <input type="number" step="0.01" name="weight" id="weight"
                                   class="form-control @error('weight') is-invalid @enderror"
                                   value="{{ old('weight', $product->weight) }}">

                            <div class="invalid-feedback" id="err_weight">
                                @error('weight') {{ $message }} @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="form-group mt-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                    @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Upload new images --}}
                <div class="form-group mt-3">
                    <label for="images">Product Images (optional)</label>
                    <input type="file" name="images[]" id="images"
                           class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                           multiple>
                    <small class="form-text text-muted">
                        You can upload multiple images; the first one will be used as the main image.
                    </small>
                    @error('images')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    @error('images.*')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- hidden inputs لإدارة حالة الصور --}}
                @php
                    $currentPrimary = $product->images->firstWhere('is_primary', true);
                @endphp

                <input type="hidden"
                       name="primary_image_id"
                       id="primary_image_id"
                       value="{{ old('primary_image_id', optional($currentPrimary)->id) }}">

                <input type="hidden"
                       name="primary_new_image_index"
                       id="primary_new_image_index"
                       value="{{ old('primary_new_image_index') }}">

                <input type="hidden"
                       name="deleted_image_ids"
                       id="deleted_image_ids"
                       value="">

                {{-- Current + new images --}}
                <div class="form-group mt-4">
                    <label>Current Images</label>
                    <div id="current-images-wrapper" class="d-flex flex-wrap">
                        {{-- صور موجودة في الداتابيس --}}
                        @if ($product->images && $product->images->count())
                            @foreach ($product->images as $image)
                                @php $isPrimary = $image->is_primary; @endphp
                                <div class="position-relative me-3 mb-3 existing-image product-image-item {{ $isPrimary ? 'is-primary' : '' }}"
                                     style="width: 100px;"
                                     data-image-id="{{ $image->id }}">
                                    <div style="width: 100px; height: 100px; overflow: hidden; border-radius: .35rem;">
                                        <img src="{{ $image->url ?? asset('storage/' . $image->file_path) }}"
                                             alt="product image"
                                             class="img-fluid w-100 h-100"
                                             style="object-fit: cover;">
                                    </div>

                                    <button type="button"
                                            class="btn btn-xs w-100 mt-1 btn-set-primary-existing {{ $isPrimary ? 'btn-gradient-primary' : 'btn-outline-primary' }}"
                                            data-image-id="{{ $image->id }}"
                                            style="font-size: 11px; padding: 2px 4px;">
                                        {{ $isPrimary ? 'Primary' : 'Set as primary' }}
                                    </button>

                                    <button type="button"
                                            class="btn btn-xs btn-outline-danger w-100 mt-1 btn-delete-existing-image"
                                            data-image-id="{{ $image->id }}"
                                            style="font-size: 11px; padding: 2px 4px;">
                                        Delete
                                    </button>
                                </div>
                            @endforeach
                        @endif
                        {{-- صور الـ preview رح تنضاف هون بالـ JS --}}
                    </div>
                </div>

                {{-- أزرار الحفظ / الإلغاء --}}
                <div class="mt-4 d-flex justify-content-end">
                    {{-- Cancel يرجّع للـ index بدون ما يغيّر شيء --}}
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light me-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-gradient-primary">
                        Update Product
                    </button>
                </div>

            </form>

        </div>
    </div>

    {{-- JS: إدارة الصور (existing + new) + primary --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput            = document.getElementById('images');
            const wrapper              = document.getElementById('current-images-wrapper');
            const primaryImageInput    = document.getElementById('primary_image_id');
            const primaryNewIndexInput = document.getElementById('primary_new_image_index');
            const deletedImageIdsInput = document.getElementById('deleted_image_ids');

            if (!fileInput || !wrapper) return;

            // ملفات الصور الجديدة فقط
            let currentFiles = [];
            // IDs الصور القديمة المحذوفة
            let deletedExistingImages = [];

            function syncInputFiles() {
                const dt = new DataTransfer();
                currentFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }

            function clearPrimaryClasses() {
                wrapper.querySelectorAll('.product-image-item').forEach(item => {
                    item.classList.remove('is-primary');
                });

                wrapper.querySelectorAll('.btn-set-primary-existing, .btn-set-primary-new').forEach(btn => {
                    btn.classList.remove('btn-gradient-primary');
                    if (!btn.classList.contains('btn-outline-primary')) {
                        btn.classList.add('btn-outline-primary');
                    }
                    btn.textContent = 'Set as primary';
                });
            }

            function setExistingAsPrimary(imageId) {
                clearPrimaryClasses();

                if (primaryImageInput) {
                    primaryImageInput.value = imageId;
                }
                if (primaryNewIndexInput) {
                    primaryNewIndexInput.value = '';
                }

                const item = wrapper.querySelector('.existing-image[data-image-id="' + imageId + '"]');
                if (item) {
                    item.classList.add('is-primary');
                    const btn = item.querySelector('.btn-set-primary-existing');
                    if (btn) {
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-gradient-primary');
                        btn.textContent = 'Primary';
                    }
                }
            }

            function setNewAsPrimary(index) {
                clearPrimaryClasses();

                if (primaryNewIndexInput) {
                    primaryNewIndexInput.value = index;
                }
                if (primaryImageInput) {
                    primaryImageInput.value = '';
                }

                const item = wrapper.querySelector('.preview-image[data-index="' + index + '"]');
                if (item) {
                    item.classList.add('is-primary');
                    const btn = item.querySelector('.btn-set-primary-new');
                    if (btn) {
                        btn.classList.remove('btn-outline-primary');
                        btn.classList.add('btn-gradient-primary');
                        btn.textContent = 'Primary';
                    }
                }
            }

            function renderPreviews() {
                // حذف الـ previews القديمة فقط
                wrapper.querySelectorAll('.preview-image').forEach(el => el.remove());

                if (!currentFiles.length) return;

                currentFiles.forEach((file, index) => {
                    const url = URL.createObjectURL(file);

                    const div = document.createElement('div');
                    div.className = 'position-relative me-3 mb-3 preview-image product-image-item';
                    div.style.width = '100px';
                    div.dataset.index = index;

                    div.innerHTML = `
                        <div style="width: 100px; height: 100px; overflow: hidden; border-radius: .35rem;">
                            <img src="${url}"
                                 alt="preview"
                                 class="img-fluid w-100 h-100"
                                 style="object-fit: cover;">
                        </div>
                        <button type="button"
                                class="btn btn-xs btn-outline-primary w-100 mt-1 btn-set-primary-new"
                                data-index="${index}"
                                style="font-size: 11px; padding: 2px 4px;">
                            Set as primary
                        </button>
                        <button type="button"
                                class="btn btn-xs btn-outline-danger w-100 mt-1 delete-new-image"
                                data-index="${index}"
                                style="font-size: 11px; padding: 2px 4px;">
                            Delete
                        </button>
                    `;

                    wrapper.appendChild(div);
                });

                const savedIndex = primaryNewIndexInput ? primaryNewIndexInput.value : '';
                if (savedIndex !== '' && !Number.isNaN(parseInt(savedIndex, 10))) {
                    const idx = parseInt(savedIndex, 10);
                    setNewAsPrimary(idx);
                }
            }

            fileInput.addEventListener('change', function () {
                const newFiles = Array.from(this.files || []);
                currentFiles = currentFiles.concat(newFiles);
                syncInputFiles();
                renderPreviews();
            });

            wrapper.addEventListener('click', function (e) {
                const primaryExistingBtn = e.target.closest('.btn-set-primary-existing');
                if (primaryExistingBtn) {
                    const imageId = primaryExistingBtn.getAttribute('data-image-id');
                    if (imageId) setExistingAsPrimary(imageId);
                    return;
                }

                const primaryNewBtn = e.target.closest('.btn-set-primary-new');
                if (primaryNewBtn) {
                    const idx = primaryNewBtn.getAttribute('data-index');
                    if (idx !== null) setNewAsPrimary(idx);
                    return;
                }

                const deleteNewBtn = e.target.closest('.delete-new-image');
                if (deleteNewBtn) {
                    const idx = parseInt(deleteNewBtn.getAttribute('data-index'), 10);
                    if (!Number.isNaN(idx)) {
                        if (primaryNewIndexInput &&
                            primaryNewIndexInput.value !== '' &&
                            parseInt(primaryNewIndexInput.value, 10) === idx) {
                            primaryNewIndexInput.value = '';
                        }

                        currentFiles.splice(idx, 1);
                        syncInputFiles();
                        renderPreviews();
                    }
                    return;
                }

                const deleteExistingBtn = e.target.closest('.btn-delete-existing-image');
                if (deleteExistingBtn) {
                    const imageId = deleteExistingBtn.getAttribute('data-image-id');
                    if (!imageId) return;

                    if (primaryImageInput && primaryImageInput.value === imageId) {
                        primaryImageInput.value = '';
                    }

                    if (!deletedExistingImages.includes(imageId)) {
                        deletedExistingImages.push(imageId);
                    }
                    if (deletedImageIdsInput) {
                        deletedImageIdsInput.value = deletedExistingImages.join(',');
                    }

                    const item = deleteExistingBtn.closest('.existing-image');
                    if (item) item.remove();
                    return;
                }
            });
        });
    </script>

    {{-- ✅ NEW: نفس نمط الفرونت/اللوغ: Inline validation بدون صندوق أخطاء --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const form     = document.getElementById('productEditForm');

            const nameEl   = document.getElementById('name');
            const catEl    = document.getElementById('category_id');
            const priceEl  = document.getElementById('price');
            const compEl   = document.getElementById('compare_price');
            const stockEl  = document.getElementById('stock');
            const weightEl = document.getElementById('weight');

            const errName   = document.getElementById('err_name');
            const errCat    = document.getElementById('err_category_id');
            const errPrice  = document.getElementById('err_price');
            const errComp   = document.getElementById('err_compare_price');
            const errStock  = document.getElementById('err_stock');
            const errWeight = document.getElementById('err_weight');

            function clearInvalid(input, errBox) {
                if (!input) return;
                input.classList.remove('is-invalid');
                if (errBox) errBox.textContent = '';
            }

            function setInvalid(input, errBox, message) {
                if (!input) return;
                input.classList.add('is-invalid');
                if (errBox) errBox.textContent = message || '';
            }

            // ✅ منع أحرف: decimals (price/compare/weight)
            function enforceDecimal(el){
                if (!el) return;
                el.addEventListener('input', () => {
                    let v = (el.value || '').replace(/[^\d.]/g, '');
                    const parts = v.split('.');
                    if (parts.length > 2) v = parts[0] + '.' + parts.slice(1).join('');
                    if (v.includes('.')) {
                        const p = v.split('.');
                        v = p[0] + '.' + (p[1] || '').slice(0, 2);
                    }
                    el.value = v;
                });
            }

            // ✅ منع أحرف: integer (stock)
            function enforceInteger(el){
                if (!el) return;
                el.addEventListener('input', () => {
                    el.value = (el.value || '').replace(/\D/g, '');
                });
            }

            enforceDecimal(priceEl);
            enforceDecimal(compEl);
            enforceDecimal(weightEl);
            enforceInteger(stockEl);

            // تنظيف الخطأ أثناء الكتابة (مثل اللوغ)
            [
                [nameEl, errName],
                [catEl, errCat],
                [priceEl, errPrice],
                [compEl, errComp],
                [stockEl, errStock],
                [weightEl, errWeight],
            ].forEach(([el, err]) => {
                if (!el) return;
                el.addEventListener('input', () => clearInvalid(el, err));
                el.addEventListener('change', () => clearInvalid(el, err));
            });

            function isNumeric(v){
                if (v === null || v === undefined) return false;
                if (String(v).trim() === '') return false;
                return !isNaN(v) && isFinite(v);
            }

            function validateClient(){
                clearInvalid(nameEl, errName);
                clearInvalid(catEl, errCat);
                clearInvalid(priceEl, errPrice);
                clearInvalid(compEl, errComp);
                clearInvalid(stockEl, errStock);
                clearInvalid(weightEl, errWeight);

                let firstBad = null;

                const vName = (nameEl?.value || '').trim();
                if (!vName){
                    setInvalid(nameEl, errName, 'Product name is required.');
                    firstBad = firstBad || nameEl;
                }

                const vCat = (catEl?.value || '').trim();
                if (!vCat){
                    setInvalid(catEl, errCat, 'Category is required.');
                    firstBad = firstBad || catEl;
                }

                const vPrice = (priceEl?.value || '').trim();
                if (!vPrice){
                    setInvalid(priceEl, errPrice, 'Price is required.');
                    firstBad = firstBad || priceEl;
                } else if (!isNumeric(vPrice)){
                    setInvalid(priceEl, errPrice, 'Price must be a number.');
                    firstBad = firstBad || priceEl;
                }

                const vStock = (stockEl?.value || '').trim();
                if (vStock === ''){
                    setInvalid(stockEl, errStock, 'Stock is required.');
                    firstBad = firstBad || stockEl;
                } else if (!/^\d+$/.test(vStock)){
                    setInvalid(stockEl, errStock, 'Stock must be numbers only.');
                    firstBad = firstBad || stockEl;
                }

                const vComp = (compEl?.value || '').trim();
                if (vComp !== '' && !isNumeric(vComp)){
                    setInvalid(compEl, errComp, 'Compare price must be a number.');
                    firstBad = firstBad || compEl;
                }

                const vWeight = (weightEl?.value || '').trim();
                if (vWeight !== '' && !isNumeric(vWeight)){
                    setInvalid(weightEl, errWeight, 'Weight must be a number.');
                    firstBad = firstBad || weightEl;
                }

                if (firstBad){
                    firstBad.focus();
                    return false;
                }
                return true;
            }

            if (form){
                form.addEventListener('submit', function(e){
                    const ok = validateClient();
                    if (!ok){
                        e.preventDefault();
                        return;
                    }
                });
            }

            // ✅ إذا رجعنا من الباك وفي أخطاء Laravel: ركّز أول حقل غلط
            const firstServerInvalid = document.querySelector('#productEditForm .is-invalid');
            if (firstServerInvalid) firstServerInvalid.focus();
        });
    </script>


@endsection
