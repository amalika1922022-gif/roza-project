// public/assets/js/front/common.js
(function () {
  document.addEventListener('DOMContentLoaded', function () {

    // (اختياري) إذا بدك كل صفحة تبدأ من فوق
    window.scrollTo({ top: 0, left: 0, behavior: 'auto' });

    const floatingAlert = document.getElementById('front-floating-alert');

    function showFloatingAlert(type, message) {
      if (!floatingAlert) return;

      floatingAlert.classList.remove('alert-success', 'alert-danger', 'hide');
      floatingAlert.classList.add(type === 'success' ? 'alert-success' : 'alert-danger');

      floatingAlert.textContent = message;

      floatingAlert.style.display = 'block';
      floatingAlert.classList.add('show');

      setTimeout(() => {
        floatingAlert.classList.add('hide');

        setTimeout(() => {
          floatingAlert.classList.remove('show', 'hide');
          floatingAlert.style.display = 'none';
        }, 500);
      }, 2500);
    }

    // Hide normal alerts after 3s (except floating)
    const alerts = document.querySelectorAll('.alert:not(#front-floating-alert)');
    alerts.forEach(alert => {
      setTimeout(() => {
        alert.style.transition = 'opacity .5s ease';
        alert.style.opacity = '0';
        setTimeout(() => alert.remove(), 500);
      }, 3000);
    });

    // AJAX add-to-cart (global)
    const addToCartForms = document.querySelectorAll('form.js-add-to-cart');

    addToCartForms.forEach(form => {
      form.addEventListener('submit', function (e) {
        e.preventDefault();

        const formData = new FormData(form);

        fetch(form.action, {
          method: 'POST',
          headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
          },
          body: formData,
        })
          .then(r => r.json())
          .then(data => {
            const hint = document.querySelector('.js-qty-hint') || document.getElementById('qtyHint');

            if (data.status === 'success') {
              showFloatingAlert('success', data.message || 'Product added to cart 💜');

              if (hint) {
                hint.classList.add('d-none');
                hint.textContent = '';
                hint.classList.remove('text-danger', 'text-success');
              }
              return;
            }

            // error
            if (hint && data.message) {
              hint.classList.remove('d-none', 'text-success');
              hint.classList.add('text-danger');
              hint.textContent = data.message;
              setTimeout(() => hint.classList.add('d-none'), 3000);
            } else {
              showFloatingAlert('error', data.message || 'Something went wrong.');
            }
          })
          .catch(() => {
            const hint = document.querySelector('.js-qty-hint') || document.getElementById('qtyHint');

            if (hint) {
              hint.classList.remove('d-none', 'text-success');
              hint.classList.add('text-danger');
              hint.textContent = 'Something went wrong. Please try again.';
              setTimeout(() => hint.classList.add('d-none'), 3000);
            } else {
              showFloatingAlert('error', 'Something went wrong. Please try again.');
            }
          });
      });
    });
  });
})();
