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

        .product-image-item.is-primary {
            border-radius: .35rem;
        }

        .invalid-feedback {
            display: block;
        }

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
            Create Product
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
            <h4 class="card-title mb-4">New Product</h4>

            <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
                id="productCreateForm" novalidate>
                @csrf

                <div class="row">
                    {{-- Name --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input type="text" name="name" id="name"
                                class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
                                required>
                            @error('name')
                                <div class="invalid-feedback" id="err_name">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_name"></div>
                            @enderror
                        </div>
                    </div>

                    {{-- Slug --}}
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="slug">Slug (optional)</label>
                            <input type="text" name="slug" id="slug"
                                class="form-control @error('slug') is-invalid @enderror" value="{{ old('slug') }}"
                                placeholder="auto-generated if empty">
                            @error('slug')
                                <div class="invalid-feedback" id="err_slug">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_slug"></div>
                            @enderror
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
                                        {{ old('category_id', request('category_id')) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback" id="err_category_id">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_category_id"></div>
                            @enderror
                        </div>
                    </div>

                    {{-- Price --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="price">Price *</label>
                            <input type="number" step="0.01" name="price" id="price"
                                class="form-control @error('price') is-invalid @enderror" value="{{ old('price') }}"
                                required>
                            @error('price')
                                <div class="invalid-feedback" id="err_price">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_price"></div>
                            @enderror
                        </div>
                    </div>

                    {{-- Compare price --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="compare_price">Compare price (optional)</label>
                            <input type="number" step="0.01" name="compare_price" id="compare_price"
                                class="form-control @error('compare_price') is-invalid @enderror"
                                value="{{ old('compare_price') }}">
                            @error('compare_price')
                                <div class="invalid-feedback" id="err_compare_price">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_compare_price"></div>
                            @enderror
                        </div>
                    </div>

                    {{-- Stock --}}
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="stock">Stock *</label>
                            <input type="number" name="stock" id="stock"
                                class="form-control @error('stock') is-invalid @enderror" value="{{ old('stock', 0) }}"
                                min="0" required>
                            @error('stock')
                                <div class="invalid-feedback" id="err_stock">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_stock"></div>
                            @enderror
                        </div>
                    </div>

                </div>{{-- row --}}

                {{-- Active switch + Weight --}}
                <div class="row align-items-center mt-2">
                    <div class="col-md-4">
                        <div class="form-group d-flex align-items-center">
                            <input type="hidden" name="is_active" value="0">

                            <div class="switch-purple">
                                <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                                    value="1" {{ old('is_active', 1) ? 'checked' : '' }}>
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
                            <input type="number" step="0.01" name="weight" id="weight"
                                class="form-control @error('weight') is-invalid @enderror" value="{{ old('weight') }}">
                            @error('weight')
                                <div class="invalid-feedback" id="err_weight">{{ $message }}</div>
                            @else
                                <div class="invalid-feedback" id="err_weight"></div>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div class="form-group mt-3">
                    <label for="description">Description</label>
                    <textarea name="description" id="description" rows="4"
                        class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Product Images (upload) --}}
                <div class="form-group mt-3">
                    <label for="images">Product Images (optional)</label>
                    <input type="file" name="images[]" id="images"
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
                <input type="hidden" name="primary_image_index" id="primary_image_index" value="0">

                {{-- Current Images --}}
                <div class="form-group mt-4">
                    <label>Current Images</label>
                    <div id="current-images-wrapper" class="d-flex flex-wrap">
                        {{-- JS رح يضيف الـ previews هون --}}
                    </div>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    <a href="{{ route('admin.products.index') }}" class="btn btn-light me-2">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-gradient-primary" id="btnCreateProduct">
                        Create Product
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- سكربت: نفس فكرة الأيديت بس للصور الجديدة فقط --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const fileInput = document.getElementById('images');
            const wrapper = document.getElementById('current-images-wrapper');
            const primaryInput = document.getElementById('primary_image_index');

            if (!fileInput || !wrapper || !primaryInput) return;

            // ✅ خليها Global لحتى تبقى بين السكربتات
            window.currentFiles = window.currentFiles || [];

            function syncInputFiles() {
                const dt = new DataTransfer();
                window.currentFiles.forEach(file => dt.items.add(file));
                fileInput.files = dt.files;
            }

            function clearPrimaryClasses() {
                wrapper.querySelectorAll('.product-image-item').forEach(item => {
                    item.classList.remove('is-primary');
                });

                wrapper.querySelectorAll('.btn-set-primary-new').forEach(btn => {
                    btn.classList.remove('btn-gradient-primary');
                    if (!btn.classList.contains('btn-outline-primary')) {
                        btn.classList.add('btn-outline-primary');
                    }
                    btn.textContent = 'Set as primary';
                });
            }

            function setNewAsPrimary(index) {
                clearPrimaryClasses();

                primaryInput.value = index;

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
                wrapper.querySelectorAll('.preview-image').forEach(el => el.remove());

                if (!window.currentFiles.length) {
                    primaryInput.value = 0;
                    return;
                }

                window.currentFiles.forEach((file, index) => {
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

                let savedIndex = parseInt(primaryInput.value, 10);
                if (isNaN(savedIndex) || savedIndex >= window.currentFiles.length) {
                    savedIndex = 0;
                    primaryInput.value = savedIndex;
                }

                setNewAsPrimary(savedIndex);
            }

            fileInput.addEventListener('change', function() {
                const newFiles = Array.from(this.files || []);
                window.currentFiles = window.currentFiles.concat(newFiles);
                syncInputFiles();
                renderPreviews();
            });

            wrapper.addEventListener('click', function(e) {
                const primaryBtn = e.target.closest('.btn-set-primary-new');
                if (primaryBtn) {
                    const idx = primaryBtn.getAttribute('data-index');
                    if (idx !== null) {
                        setNewAsPrimary(parseInt(idx, 10));
                    }
                    return;
                }

                const deleteBtn = e.target.closest('.delete-new-image');
                if (deleteBtn) {
                    const idx = parseInt(deleteBtn.getAttribute('data-index'), 10);
                    if (!Number.isNaN(idx)) {
                        if (parseInt(primaryInput.value, 10) === idx) {
                            primaryInput.value = 0;
                        } else if (parseInt(primaryInput.value, 10) > idx) {
                            primaryInput.value = parseInt(primaryInput.value, 10) - 1;
                        }

                        window.currentFiles.splice(idx, 1);
                        syncInputFiles();
                        renderPreviews();
                    }
                    return;
                }
            });
        });
    </script>

    {{-- ✅ NEW: نفس نمط اللوغ/الفرونت: تحقق Inline + منع أحرف بالأرقام --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const form = document.getElementById('productCreateForm');

            const nameEl = document.getElementById('name');
            const catEl = document.getElementById('category_id');
            const priceEl = document.getElementById('price');
            const compEl = document.getElementById('compare_price');
            const stockEl = document.getElementById('stock');
            const weightEl = document.getElementById('weight');

            const errName = document.getElementById('err_name');
            const errCat = document.getElementById('err_category_id');
            const errPrice = document.getElementById('err_price');
            const errComp = document.getElementById('err_compare_price');
            const errStock = document.getElementById('err_stock');
            const errWeight = document.getElementById('err_weight');

            function clearInvalid(input, errBox) {
                if (!input) return;
                input.classList.remove('is-invalid');
                if (errBox) errBox.textContent = '';
            }

            function setInvalid(input, errBox, msg) {
                if (!input) return;
                input.classList.add('is-invalid');
                if (errBox) errBox.textContent = msg || '';
            }

            function enforceDecimal(el) {
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

            function enforceInteger(el) {
                if (!el) return;
                el.addEventListener('input', () => {
                    el.value = (el.value || '').replace(/\D/g, '');
                });
            }

            enforceDecimal(priceEl);
            enforceDecimal(compEl);
            enforceDecimal(weightEl);
            enforceInteger(stockEl);

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

            function isNumeric(v) {
                if (v === null || v === undefined) return false;
                if (String(v).trim() === '') return false;
                return !isNaN(v) && isFinite(v);
            }

            function validateClient() {
                clearInvalid(nameEl, errName);
                clearInvalid(catEl, errCat);
                clearInvalid(priceEl, errPrice);
                clearInvalid(compEl, errComp);
                clearInvalid(stockEl, errStock);
                clearInvalid(weightEl, errWeight);

                let firstBad = null;

                const vName = (nameEl?.value || '').trim();
                if (!vName) {
                    setInvalid(nameEl, errName, 'Product name is required.');
                    firstBad = firstBad || nameEl;
                }

                const vCat = (catEl?.value || '').trim();
                if (!vCat) {
                    setInvalid(catEl, errCat, 'Category is required.');
                    firstBad = firstBad || catEl;
                }

                const vPrice = (priceEl?.value || '').trim();
                if (!vPrice) {
                    setInvalid(priceEl, errPrice, 'Price is required.');
                    firstBad = firstBad || priceEl;
                } else if (!isNumeric(vPrice)) {
                    setInvalid(priceEl, errPrice, 'Price must be a number.');
                    firstBad = firstBad || priceEl;
                }

                const vStock = (stockEl?.value || '').trim();
                if (vStock === '') {
                    setInvalid(stockEl, errStock, 'Stock is required.');
                    firstBad = firstBad || stockEl;
                } else if (!/^\d+$/.test(vStock)) {
                    setInvalid(stockEl, errStock, 'Stock must be numbers only.');
                    firstBad = firstBad || stockEl;
                }

                const vComp = (compEl?.value || '').trim();
                if (vComp !== '' && !isNumeric(vComp)) {
                    setInvalid(compEl, errComp, 'Compare price must be a number.');
                    firstBad = firstBad || compEl;
                }

                const vWeight = (weightEl?.value || '').trim();
                if (vWeight !== '' && !isNumeric(vWeight)) {
                    setInvalid(weightEl, errWeight, 'Weight must be a number.');
                    firstBad = firstBad || weightEl;
                }

                if (firstBad) {
                    firstBad.focus();
                    return false;
                }
                return true;
            }

            // ✅ خليها Global لسكربت الـ AJAX submit
            window.validateClient = validateClient;

            if (form) {
                form.addEventListener('submit', function(e) {
                    const ok = validateClient();
                    if (!ok) {
                        e.preventDefault();
                        return;
                    }
                });
            }

            const firstServerInvalid = document.querySelector('#productCreateForm .is-invalid');
            if (firstServerInvalid) firstServerInvalid.focus();
        });
    </script>

    {{-- ✅ NEW: AJAX Submit to keep images on Laravel validation errors --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('productCreateForm');
            if (!form) return;

            const submitBtn = document.getElementById('btnCreateProduct');

            function setServerError(field, msg) {
                // حاول يلاقي input حسب الاسم
                const input = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
                if (input) input.classList.add('is-invalid');

                // ربط الحقول بصناديق الأخطاء الموجودة
                const map = {
                    name: 'err_name',
                    slug: 'err_slug',
                    category_id: 'err_category_id',
                    price: 'err_price',
                    compare_price: 'err_compare_price',
                    stock: 'err_stock',
                    weight: 'err_weight',
                };

                const id = map[field];
                if (id) {
                    const box = document.getElementById(id);
                    if (box) box.textContent = msg || '';
                }
            }

            form.addEventListener('submit', async function(e) {
                // خلّي الـ client validate يقرر
                if (typeof window.validateClient === 'function') {
                    const ok = window.validateClient();
                    if (!ok) {
                        e.preventDefault();
                        return;
                    }
                }

                e.preventDefault();

                if (submitBtn) {
                    submitBtn.disabled = true;
                }

                // FormData من الفورم (بيشمل الصور لأن syncInputFiles بيملي input.files)
                const fd = new FormData(form);

                try {
                    const res = await fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                        },
                        body: fd
                    });

                    if (res.status === 422) {
                        const data = await res.json();
                        const errors = data.errors || {};

                        // نظّف أخطاء قديمة
                        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove(
                            'is-invalid'));
                        ['err_name', 'err_slug', 'err_category_id', 'err_price', 'err_compare_price',
                            'err_stock', 'err_weight'
                        ].forEach(id => {
                            const box = document.getElementById(id);
                            if (box) box.textContent = '';
                        });

                        Object.keys(errors).forEach(field => {
                            setServerError(field, (errors[field] && errors[field][0]) ? errors[
                                field][0] : '');
                        });

                        // ركّز أول حقل غلط
                        const firstBad = form.querySelector('.is-invalid');
                        if (firstBad) firstBad.focus();

                        // ✅ ما في Reload => الصور بتضل
                        if (submitBtn) submitBtn.disabled = false;
                        return;
                    }

                    // نجاح
                    const data = await res.json().catch(() => ({}));
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    } else {
                        // fallback
                        window.location.reload();
                    }

                } catch (err) {
                    console.error(err);
                    alert('صار خطأ غير متوقع.');
                    if (submitBtn) submitBtn.disabled = false;
                }
            });
        });
    </script>

@endsection
