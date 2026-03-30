document.addEventListener('DOMContentLoaded', function () {
    // =========================
    // Remove item modal
    // =========================
    let selectedForm = null;

    const removeModal = document.getElementById('removeModal');
    const cancelRemoveBtn = document.querySelector('.cancel-remove');
    const confirmRemoveBtn = document.querySelector('.confirm-remove');

    if (removeModal) {
        document.querySelectorAll('.js-remove-btn').forEach((btn) => {
            btn.addEventListener('click', function () {
                selectedForm = this.closest('form');
                removeModal.classList.remove('d-none');
            });
        });

        if (cancelRemoveBtn) {
            cancelRemoveBtn.addEventListener('click', function () {
                removeModal.classList.add('d-none');
                selectedForm = null;
            });
        }

        if (confirmRemoveBtn) {
            confirmRemoveBtn.addEventListener('click', function () {
                if (selectedForm) selectedForm.submit();
            });
        }
    }

    // =========================
    // Guest checkout login modal
    // =========================
    const checkoutGuestBtn = document.getElementById('checkoutGuestBtn');
    const loginRequiredModal = document.getElementById('loginRequiredModal');

    if (checkoutGuestBtn && loginRequiredModal) {
        checkoutGuestBtn.addEventListener('click', () => {
            loginRequiredModal.style.display = 'block';
        });

        // زر الإغلاق داخل المودال (بدل function global)
        const closeBtn = loginRequiredModal.querySelector('[data-close-login-modal]');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                loginRequiredModal.style.display = 'none';
            });
        }

        // إغلاق عند الضغط على الخلفية
        loginRequiredModal.addEventListener('click', (e) => {
            if (e.target === loginRequiredModal) {
                loginRequiredModal.style.display = 'none';
            }
        });
    }
});
