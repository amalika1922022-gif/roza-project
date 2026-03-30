// public/assets/front/js/pages/product-show.js
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const cfg = window.__productShow || {};
    const images = Array.isArray(cfg.images) ? cfg.images : [];
    const hasCarousel = !!cfg.hasCarousel;

    // =========================
    // 1) Carousel
    // =========================
    if (hasCarousel && images.length > 1) {
      let currentIndex = 0;

      const mainImg = document.getElementById('mainProductImage');
      const thumbs = document.querySelectorAll('.front-product-thumb-btn');
      const prevBtn = document.getElementById('prevImageBtn');
      const nextBtn = document.getElementById('nextImageBtn');

      function setImage(index) {
        if (!images.length || !mainImg) return;

        currentIndex = ((index % images.length) + images.length) % images.length;

        // mainImg ممكن يكون IMG أو DIV إذا ما في صور
        if (mainImg.tagName === 'IMG') {
          mainImg.src = images[currentIndex];
        }

        thumbs.forEach(btn => btn.classList.remove('active'));
        const active = document.querySelector(
          '.front-product-thumb-btn[data-index="' + currentIndex + '"]'
        );
        if (active) active.classList.add('active');
      }

      thumbs.forEach(btn => {
        btn.addEventListener('click', function () {
          const idx = parseInt(this.getAttribute('data-index') || '0', 10);
          setImage(idx);
        });
      });

      if (prevBtn) prevBtn.addEventListener('click', () => setImage(currentIndex - 1));
      if (nextBtn) nextBtn.addEventListener('click', () => setImage(currentIndex + 1));
    }

    // =========================
    // 2) Quantity validation + hint
    // =========================
    const qtyInput = document.getElementById('productQuantityInput');
    const qtyHint = document.getElementById('qtyHint');

    if (qtyInput) {
      const maxFromAttr = parseInt(qtyInput.max || '1', 10);
      const maxStock = parseInt(cfg.maxStock || maxFromAttr || 1, 10);
      const max = Math.max(1, maxStock);

      qtyInput.addEventListener('input', function () {
        let value = parseInt(this.value || '1', 10);
        if (!Number.isFinite(value)) value = 1;

        if (value < 1) value = 1;

        if (value > max) {
          value = max;

          if (qtyHint) {
            qtyHint.textContent = 'You can order up to ' + max + ' pieces for now 💜';
            qtyHint.classList.remove('d-none', 'text-success');
            qtyHint.classList.add('text-danger');

            setTimeout(function () {
              qtyHint.classList.add('d-none');
            }, 2500);
          }
        }

        this.value = value;
      });
    }
  });
})();
