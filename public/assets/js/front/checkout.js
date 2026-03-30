document.addEventListener('DOMContentLoaded', () => {
    // =========================
    // Payment method switches
    // =========================
    const codSwitch = document.getElementById('payment_cod');
    const stripeSwitch = document.getElementById('payment_stripe');
    const paymentInput = document.getElementById('payment_method_input');

    function setMethod(method) {
        if (!paymentInput || !codSwitch || !stripeSwitch) return;

        paymentInput.value = method;

        if (method === 'cod') {
            codSwitch.checked = true;
            stripeSwitch.checked = false;
        } else {
            codSwitch.checked = false;
            stripeSwitch.checked = true;
        }
    }

    if (codSwitch && stripeSwitch && paymentInput) {
        codSwitch.addEventListener('change', function () {
            setMethod(this.checked ? 'cod' : 'stripe');
        });

        stripeSwitch.addEventListener('change', function () {
            setMethod(this.checked ? 'stripe' : 'cod');
        });

        // old value from backend (set in blade)
        const oldMethod = window.__checkoutOldPaymentMethod || 'cod';
        setMethod(oldMethod);
    }

    // =========================
    // Checkout form validation
    // =========================
    const form = document.getElementById('checkoutForm');
    if (!form) return;

    const el = {
        full_name: document.getElementById('full_name'),
        email: document.getElementById('email'),
        phone: document.getElementById('phone'),
        address: document.getElementById('address'),
        city: document.getElementById('city'),
        country: document.getElementById('country'),
        postal_code: document.getElementById('postal_code'),
    };

    const err = {
        full_name: document.getElementById('err_full_name'),
        email: document.getElementById('err_email'),
        phone: document.getElementById('err_phone'),
        address: document.getElementById('err_address'),
        city: document.getElementById('err_city'),
        country: document.getElementById('err_country'),
        postal_code: document.getElementById('err_postal_code'),
    };

    const box = document.getElementById('clientErrors');
    const list = document.getElementById('clientErrorsList');

    const hideErrorsBox = () => {
        if (!box || !list) return;
        box.classList.add('d-none');
        list.innerHTML = '';
    };

    const showErrorsBox = (errors) => {
        if (!box || !list) return;
        list.innerHTML = '';
        errors.forEach((e) => {
            const li = document.createElement('li');
            li.textContent = e;
            list.appendChild(li);
        });
        box.classList.remove('d-none');
        box.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const clearInvalid = (key) => {
        const input = el[key];
        const errEl = err[key];
        if (!input) return;

        input.classList.remove('is-invalid');

        // clear text only when user types
        if (errEl) {
            errEl.textContent = '';
            errEl.removeAttribute('data-server');
        }
    };

    const setInvalid = (key, msg) => {
        const input = el[key];
        const errEl = err[key];
        if (!input) return;

        input.classList.add('is-invalid');
        if (errEl) errEl.textContent = msg || '';
    };

    // digits-only helpers
    if (el.phone) {
        el.phone.addEventListener('input', () => {
            el.phone.value = el.phone.value.replace(/\D/g, '').slice(0, 15);
            clearInvalid('phone');
            hideErrorsBox();
        });
    }

    if (el.postal_code) {
        el.postal_code.addEventListener('input', () => {
            el.postal_code.value = el.postal_code.value.replace(/\D/g, '').slice(0, 3);
            clearInvalid('postal_code');
            hideErrorsBox();
        });
    }

    // clear on typing
    ['full_name', 'email', 'address', 'city', 'country'].forEach((k) => {
        if (!el[k]) return;
        el[k].addEventListener('input', () => {
            clearInvalid(k);
            hideErrorsBox();
        });
    });

    // focus first server invalid
    const firstServerInvalid = document.querySelector('#checkoutForm .is-invalid');
    if (firstServerInvalid) firstServerInvalid.focus();

    function validateClient() {
        hideErrorsBox();

        const errors = [];

        // Full name
        const vName = (el.full_name?.value || '').trim().replace(/\s+/g, ' ');
        const parts = vName.split(' ').filter(Boolean);
        if (!vName) {
            errors.push('Full name is required.');
            setInvalid('full_name', 'Full name is required.');
        } else if (vName.length < 3) {
            errors.push('Full name must be at least 3 characters.');
            setInvalid('full_name', 'Minimum 3 characters.');
        } else if (parts.length < 2) {
            errors.push('Please enter your full name (at least 2 words).');
            setInvalid('full_name', 'Please enter at least 2 words.');
        }

        // Email
        const vEmail = (el.email?.value || '').trim();
        if (!vEmail) {
            errors.push('Email is required.');
            setInvalid('email', 'Email is required.');
        } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(vEmail)) {
            errors.push('Please enter a valid email address.');
            setInvalid('email', 'Invalid email format.');
        }

        // Phone
        const vPhone = (el.phone?.value || '').trim();
        if (!/^\d{8,15}$/.test(vPhone)) {
            errors.push('Phone number must be numbers only (8 to 15 digits).');
            setInvalid('phone', 'Numbers only (8–15 digits).');
        }

        // Address
        const vAddress = (el.address?.value || '').trim();
        if (!vAddress || vAddress.length < 5) {
            errors.push('Address must be at least 5 characters.');
            setInvalid('address', 'Minimum 5 characters.');
        }

        // City
        const vCity = (el.city?.value || '').trim();
        if (!vCity || vCity.length < 2) {
            errors.push('City name must be at least 2 characters.');
            setInvalid('city', 'Minimum 2 characters.');
        }

        // Country
        const vCountry = (el.country?.value || '').trim();
        if (!vCountry || vCountry.length < 2) {
            errors.push('Country name must be at least 2 characters.');
            setInvalid('country', 'Minimum 2 characters.');
        }

        // Postal
        const vPostal = (el.postal_code?.value || '').trim();
        if (!/^\d{3}$/.test(vPostal)) {
            errors.push('Postal code must be exactly 3 digits.');
            setInvalid('postal_code', 'Exactly 3 digits.');
        }

        if (errors.length) {
            showErrorsBox(errors);
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
            return false;
        }

        return true;
    }

    form.addEventListener('submit', (e) => {
        if (!validateClient()) e.preventDefault();
    });
});
