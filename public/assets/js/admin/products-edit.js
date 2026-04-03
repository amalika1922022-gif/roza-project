document.addEventListener('DOMContentLoaded', function () {

    /* =========================
       IMAGE MANAGEMENT
    ========================= */

    const fileInput = document.getElementById('images');
    const wrapper = document.getElementById('current-images-wrapper');
    const primaryImageInput = document.getElementById('primary_image_id');
    const primaryNewIndexInput = document.getElementById('primary_new_image_index');
    const deletedImageIdsInput = document.getElementById('deleted_image_ids');

    if (fileInput && wrapper) {

        let currentFiles = [];
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

            if (primaryImageInput) primaryImageInput.value = imageId;
            if (primaryNewIndexInput) primaryNewIndexInput.value = '';

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

            if (primaryNewIndexInput) primaryNewIndexInput.value = index;
            if (primaryImageInput) primaryImageInput.value = '';

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
                             class="img-fluid w-100 h-100"
                             style="object-fit: cover;">
                    </div>
                    <button type="button"
                            class="btn btn-xs btn-outline-primary w-100 mt-1 btn-set-primary-new"
                            data-index="${index}">
                        Set as primary
                    </button>
                    <button type="button"
                            class="btn btn-xs btn-outline-danger w-100 mt-1 delete-new-image"
                            data-index="${index}">
                        Delete
                    </button>
                `;

                wrapper.appendChild(div);
            });

            const savedIndex = primaryNewIndexInput?.value;
            if (savedIndex !== '' && !Number.isNaN(parseInt(savedIndex))) {
                setNewAsPrimary(parseInt(savedIndex));
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
                setExistingAsPrimary(primaryExistingBtn.dataset.imageId);
                return;
            }

            const primaryNewBtn = e.target.closest('.btn-set-primary-new');
            if (primaryNewBtn) {
                setNewAsPrimary(primaryNewBtn.dataset.index);
                return;
            }

            const deleteNewBtn = e.target.closest('.delete-new-image');
            if (deleteNewBtn) {
                const idx = parseInt(deleteNewBtn.dataset.index);

                if (primaryNewIndexInput?.value == idx) {
                    primaryNewIndexInput.value = '';
                }

                currentFiles.splice(idx, 1);
                syncInputFiles();
                renderPreviews();
                return;
            }

            const deleteExistingBtn = e.target.closest('.btn-delete-existing-image');
            if (deleteExistingBtn) {
                const imageId = deleteExistingBtn.dataset.imageId;

                if (primaryImageInput?.value == imageId) {
                    primaryImageInput.value = '';
                }

                if (!deletedExistingImages.includes(imageId)) {
                    deletedExistingImages.push(imageId);
                }

                if (deletedImageIdsInput) {
                    deletedImageIdsInput.value = deletedExistingImages.join(',');
                }

                deleteExistingBtn.closest('.existing-image')?.remove();
            }
        });
    }

    /* =========================
       VALIDATION
    ========================= */

    const form = document.getElementById('productEditForm');

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
        input?.classList.remove('is-invalid');
        if (errBox) errBox.textContent = '';
    }

    function setInvalid(input, errBox, message) {
        input?.classList.add('is-invalid');
        if (errBox) errBox.textContent = message;
    }

    function enforceDecimal(el) {
        el?.addEventListener('input', () => {
            let v = el.value.replace(/[^\d.]/g, '');
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
        el?.addEventListener('input', () => {
            el.value = el.value.replace(/\D/g, '');
        });
    }

    enforceDecimal(priceEl);
    enforceDecimal(compEl);
    enforceDecimal(weightEl);
    enforceInteger(stockEl);

    function isNumeric(v) {
        return v !== '' && !isNaN(v);
    }

    function validateClient() {

        let firstBad = null;

        if (!nameEl.value.trim()) {
            setInvalid(nameEl, errName, 'Required');
            firstBad = firstBad || nameEl;
        }

        if (!catEl.value) {
            setInvalid(catEl, errCat, 'Required');
            firstBad = firstBad || catEl;
        }

        if (!isNumeric(priceEl.value)) {
            setInvalid(priceEl, errPrice, 'Invalid');
            firstBad = firstBad || priceEl;
        }

        if (!/^\d+$/.test(stockEl.value)) {
            setInvalid(stockEl, errStock, 'Invalid');
            firstBad = firstBad || stockEl;
        }

        if (firstBad) {
            firstBad.focus();
            return false;
        }

        return true;
    }

    form.addEventListener('submit', function (e) {
        if (!validateClient()) {
            e.preventDefault();
        }
    });

});