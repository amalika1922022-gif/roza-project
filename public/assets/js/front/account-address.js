document.addEventListener('DOMContentLoaded', function () {

    // نفس سكربت فتح/إغلاق الفورم (قديم)
    const btn = document.getElementById('toggleAddressFormBtn');
    const form = document.getElementById('addressForm');

    if (btn && form) {
        btn.addEventListener('click', function () {
            form.classList.toggle('d-none');
        });
    }

    // ✅ نفس الشيك: Validation بدون popup
    const box = document.getElementById('clientErrors');
    const list = document.getElementById('clientErrorsList');

    const fullName = document.getElementById('full_name');
    const phone = document.getElementById('phone');
    const country = document.getElementById('country');
    const city = document.getElementById('city');
    const address = document.getElementById('address');
    const postal = document.getElementById('postal_code');

    // ✅ إذا في خطأ Laravel: فوكس على أول حقل غلط + افتح الفورم لو مخفي
    const firstServerInvalid = document.querySelector('#addressForm .is-invalid');
    if (firstServerInvalid) {
        if (form && form.classList.contains('d-none')) form.classList.remove('d-none');
        firstServerInvalid.focus();
    }

    const hideErrorsBox = () => {
        if (!box || !list) return;
        box.classList.add('d-none');
        list.innerHTML = '';
    };

    const showErrorsBox = (errors) => {
        if (!box || !list) return;
        list.innerHTML = '';
        errors.forEach(e => {
            const li = document.createElement('li');
            li.textContent = e;
            list.appendChild(li);
        });
        box.classList.remove('d-none');
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const clearFieldError = (el, errId) => {
        if (!el) return;
        el.classList.remove('is-invalid');
        const err = document.getElementById(errId);
        // ✅ مهم: ما نمسح رسالة Laravel الموجودة أصلاً
        if (err && !err.hasAttribute('data-server')) err.textContent = '';
    };

    const setFieldError = (el, errId, msg) => {
        if (!el) return;
        el.classList.add('is-invalid');
        const err = document.getElementById(errId);
        if (err) err.textContent = msg;
    };

    // ✅ منع إدخال غير الأرقام
    if (phone) {
        phone.addEventListener('input', () => {
            phone.value = phone.value.replace(/\D/g, '');
            const errEl = document.getElementById('err_phone');
            if (errEl) errEl.textContent = '';
            phone.classList.remove('is-invalid');
            hideErrorsBox();
        });
    }

    if (postal) {
        postal.addEventListener('input', () => {
            postal.value = postal.value.replace(/\D/g, '').slice(0, 3);
            const errEl = document.getElementById('err_postal_code');
            if (errEl) errEl.textContent = '';
            postal.classList.remove('is-invalid');
            hideErrorsBox();
        });
    }

    // تنظيف أخطاء عند الكتابة
    const clearMap = {
        full_name: 'err_full_name',
        phone: 'err_phone',
        country: 'err_country',
        city: 'err_city',
        address: 'err_address',
        postal_code: 'err_postal_code',
    };

    [fullName, country, city, address].forEach(el => {
        if (!el) return;
        el.addEventListener('input', () => {
            hideErrorsBox();
            const errEl = document.getElementById(clearMap[el.id] || '');
            if (errEl) errEl.textContent = '';
            el.classList.remove('is-invalid');
        });
    });

    if (form) {
        form.addEventListener('submit', (e) => {
            hideErrorsBox();

            clearFieldError(fullName, 'err_full_name');
            clearFieldError(phone, 'err_phone');
            clearFieldError(country, 'err_country');
            clearFieldError(city, 'err_city');
            clearFieldError(address, 'err_address');
            clearFieldError(postal, 'err_postal_code');

            const errors = [];

            // Full name: لازم كلمتين
            const vName = (fullName?.value || '').trim().replace(/\s+/g, ' ');
            const parts = vName.split(' ').filter(Boolean);
            if (parts.length < 2) {
                errors.push('Please enter your full name (at least 2 words).');
                setFieldError(fullName, 'err_full_name', 'Please enter at least 2 words.');
            }

            // Phone: digits 8-15
            const vPhone = (phone?.value || '').trim();
            if (!/^\d{8,15}$/.test(vPhone)) {
                errors.push('Phone number must be numbers only (8 to 15 digits).');
                setFieldError(phone, 'err_phone', 'Numbers only (8–15 digits).');
            }

            // Country: min 2
            const vCountry = (country?.value || '').trim();
            if (vCountry.length < 2) {
                errors.push('Country name must be at least 2 characters.');
                setFieldError(country, 'err_country', 'Minimum 2 characters.');
            }

            // City: min 2
            const vCity = (city?.value || '').trim();
            if (vCity.length < 2) {
                errors.push('City name must be at least 2 characters.');
                setFieldError(city, 'err_city', 'Minimum 2 characters.');
            }

            // Address: min 5
            const vAddress = (address?.value || '').trim();
            if (vAddress.length < 5) {
                errors.push('Address must be at least 5 characters.');
                setFieldError(address, 'err_address', 'Minimum 5 characters.');
            }

            // Postal: exactly 3 digits
            const vPostal = (postal?.value || '').trim();
            if (!/^\d{3}$/.test(vPostal)) {
                errors.push('Postal code must be exactly 3 digits.');
                setFieldError(postal, 'err_postal_code', 'Exactly 3 digits.');
            }

            if (errors.length) {
                e.preventDefault();
                showErrorsBox(errors);

                const firstInvalid = form.querySelector('.is-invalid');
                if (firstInvalid) firstInvalid.focus();
            }
        });
    }

});
