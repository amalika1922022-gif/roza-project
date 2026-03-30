document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('images');
    const wrapper = document.getElementById('current-images-wrapper');
    const primaryImageInput = document.getElementById('primary_image_id');
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

document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('productEditForm');

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

    function setInvalid(input, errBox, message) {
        if (!input) return;
        input.classList.add('is-invalid');
        if (errBox) errBox.textContent = message || '';
    }

    // ✅ منع أحرف: decimals (price/compare/weight)
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

    // ✅ منع أحرف: integer (stock)
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

    if (form) {
        form.addEventListener('submit', function (e) {
            const ok = validateClient();
            if (!ok) {
                e.preventDefault();
                return;
            }
        });
    }

    // ✅ إذا رجعنا من الباك وفي أخطاء Laravel: ركّز أول حقل غلط
    const firstServerInvalid = document.querySelector('#productEditForm .is-invalid');
    if (firstServerInvalid) firstServerInvalid.focus();
});
