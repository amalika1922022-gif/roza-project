// public/assets/front/js/pages/home.js
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const root = document.getElementById('heroProductCarousel');
    if (!root) return;

    const track = root.querySelector('.hero-carousel-track');
    const slides = Array.from(root.querySelectorAll('.hero-slide'));
    const prevBtn = root.querySelector('[data-hero-prev]');
    const nextBtn = root.querySelector('[data-hero-next]');
    const dotsWrap = root.querySelector('[data-hero-dots]');

    // إذا ما في سلايدات كفاية
    if (!track || slides.length <= 1) {
      if (prevBtn) prevBtn.style.display = 'none';
      if (nextBtn) nextBtn.style.display = 'none';
      if (dotsWrap) dotsWrap.style.display = 'none';
      return;
    }

    let index = 0;
    let timer = null;

    function renderDots() {
      if (!dotsWrap) return;
      dotsWrap.innerHTML = '';

      slides.forEach((_, i) => {
        const d = document.createElement('div');
        d.className = 'hero-dot' + (i === index ? ' active' : '');
        d.addEventListener('click', () => goTo(i));
        dotsWrap.appendChild(d);
      });
    }

    function goTo(i) {
      index = (i + slides.length) % slides.length;
      track.style.transform = `translateX(-${index * 100}%)`;
      renderDots();
    }

    if (prevBtn) prevBtn.addEventListener('click', () => goTo(index - 1));
    if (nextBtn) nextBtn.addEventListener('click', () => goTo(index + 1));

    // swipe (mobile)
    let startX = null;
    root.addEventListener(
      'touchstart',
      (e) => (startX = e.touches[0].clientX),
      { passive: true }
    );

    root.addEventListener('touchend', (e) => {
      if (startX === null) return;

      const endX = e.changedTouches[0].clientX;
      const diff = endX - startX;
      startX = null;

      if (Math.abs(diff) > 35) {
        diff > 0 ? goTo(index - 1) : goTo(index + 1);
      }
    });

    // autoplay
    function startAutoplay() {
      stopAutoplay();
      timer = setInterval(() => goTo(index + 1), 4500);
    }

    function stopAutoplay() {
      if (timer) clearInterval(timer);
      timer = null;
    }

    startAutoplay();
    root.addEventListener('mouseenter', stopAutoplay);
    root.addEventListener('mouseleave', startAutoplay);

    renderDots();
  });
})();
