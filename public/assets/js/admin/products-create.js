document.addEventListener('DOMContentLoaded', function () {
    initProductCreateImagePreview();
    initProductCreateClientValidation();
    initProductCreateAjaxSubmit();
});

function initProductCreateImagePreview() {
    const fileInput = document.getElementById('images');
    const wrapper = document.getElementById('current-images-wrapper');
    const primaryInput = document.getElementById('primary_image_index');

    if (!fileInput || !wrapper || !primaryInput) return;

    window.currentFiles = window.currentFiles || [];

    function syncInputFiles() {
        const dt = new DataTransfer();
        window.currentFiles.forEach(file => dt.items.add(file));
        fileInput.files = dt.files;
    }

    function clearPrimaryClasses() {
        wrapper.querySelectorAll('.product-image-item').forEach(item => item.classList.remove('is-primary'));

        wrapper.querySelectorAll('.btn-set-primary-new').forEach(btn => {
            btn.classList.remove('btn-gradient-primary');
            if (!btn.classList.contains('btn-outline-primary')) btn.classList.add('btn-outline-primary');
            btn.textContent = 'Set as primary';
        });
    }

    function setNewAsPrimary(index) {
        clearPrimaryClasses();
        primaryInput.value = index;

        const item = wrapper.querySelector('.preview-image[data-index="' + index + '"]');
        if (!item) return;

        item.classList.add('is-primary');

        const btn = item.querySelector('.btn-set-primary-new');
        if (!btn) return;

        btn.classList.remove('btn-outline-primary');
        btn.classList.add('btn-gradient-primary');
        btn.textContent = 'Primary';
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
                    <img src="${url}" alt="preview" class="img-fluid w-100 h-100" style="object-fit: cover;">
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

    fileInput.addEventListener('change', function () {
        const newFiles = Array.from(this.files || []);
        window.currentFiles = window.currentFiles.concat(newFiles);
        syncInputFiles();
        renderPreviews();
    });

    wrapper.addEventListener('click', function (e) {
        const primaryBtn = e.target.closest('.btn-set-primary-new');
        if (primaryBtn) {
            const idx = primaryBtn.getAttribute('data-index');
            if (idx !== null) setNewAsPrimary(parseInt(idx, 10));
            return;
        }

        const deleteBtn = e.target.closest('.delete-new-image');
        if (!deleteBtn) return;

        const idx = parseInt(deleteBtn.getAttribute('data-index'), 10);
        if (Number.isNaN(idx)) return;

        if (parseInt(primaryInput.value, 10) === idx) {
            primaryInput.value = 0;
        } else if (parseInt(primaryInput.value, 10) > idx) {
            primaryInput.value = parseInt(primaryInput.value, 10) - 1;
        }

        window.currentFiles.splice(idx, 1);
        syncInputFiles();
        renderPreviews();
    });
}

function initProductCreateClientValidation() {
    const form = document.getElementById('productCreateForm');
    if (!form) return;

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

    window.validateClient = validateClient;

    form.addEventListener('submit', function (e) {
        const ok = validateClient();
        if (!ok) {
            e.preventDefault();
            return;
        }
    });

    const firstServerInvalid = document.querySelector('#productCreateForm .is-invalid');
    if (firstServerInvalid) firstServerInvalid.focus();
}

function initProductCreateAjaxSubmit() {
    const form = document.getElementById('productCreateForm');
    if (!form) return;

    const submitBtn = document.getElementById('btnCreateProduct');

    function setServerError(field, msg) {
        const input = form.querySelector(`[name="${field}"]`) || form.querySelector(`[name="${field}[]"]`);
        if (input) input.classList.add('is-invalid');

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
        if (!id) return;

        const box = document.getElementById(id);
        if (box) box.textContent = msg || '';
    }

    form.addEventListener('submit', async function (e) {
        if (typeof window.validateClient === 'function') {
            const ok = window.validateClient();
            if (!ok) {
                e.preventDefault();
                return;
            }
        }

        e.preventDefault();

        if (submitBtn) submitBtn.disabled = true;

        const fd = new FormData();

        const formElements = form.querySelectorAll('input, select, textarea');
        
        formElements.forEach(el => {
            if (!el.name) return;
        
            if (el.type === 'file') {
                // تجاهل file input الأصلي
                return;
            }
        
            if ((el.type === 'checkbox' || el.type === 'radio') && !el.checked) {
                return;
            }
        
            fd.append(el.name, el.value);
        });
        
        // ✨ هون السر الحقيقي
        (window.currentFiles || []).forEach(file => {
            fd.append('images[]', file);
        });

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

                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                ['err_name', 'err_slug', 'err_category_id', 'err_price', 'err_compare_price', 'err_stock', 'err_weight']
                    .forEach(id => {
                        const box = document.getElementById(id);
                        if (box) box.textContent = '';
                    });

                Object.keys(errors).forEach(field => {
                    setServerError(field, (errors[field] && errors[field][0]) ? errors[field][0] : '');
                });

                const firstBad = form.querySelector('.is-invalid');
                if (firstBad) firstBad.focus();

                if (submitBtn) submitBtn.disabled = false;
                return;
            }

            if (!res.ok) {
                const text = await res.text();
                console.error('Create product failed:', text);
                alert('صار خطأ من السيرفر. افتح Console وشوف التفاصيل.');
                if (submitBtn) submitBtn.disabled = false;
                return;
            }

            const data = await res.json().catch(() => ({}));
            if (data.redirect) {
                window.location.href = data.redirect;
            } else {
                window.location.reload();
            }

        } catch (err) {
            console.error(err);
            alert('صار خطأ غير متوقع.');
            if (submitBtn) submitBtn.disabled = false;
        }
    });
}