// public/assets/front/js/pages/products-index.js
(function () {
  document.addEventListener('DOMContentLoaded', function () {
    const catsBox = document.getElementById('frontFilterCats');
    const productsArea = document.getElementById('productsArea');
    const sortForm = document.getElementById('frontSortForm');
    const searchForm = document.getElementById('frontSearchForm');

    // إذا الصفحة مو صفحة المنتجات، لا تعمل شي
    if (!productsArea) return;

    // ✅ 1) Card clickable (Event delegation)
    document.addEventListener('click', function (e) {
      const card = e.target.closest('.product-card-clickable');
      if (!card) return;

      // إذا الكليك على form/button/a داخل الكرت → ما تفتح
      if (e.target.closest('form') || e.target.closest('button') || e.target.closest('a')) return;

      const href = card.getAttribute('data-href');
      if (href) window.location.href = href;
    });

    // ✅ helper: set active category in sidebar
    function setActiveCategoryFromUrl(url) {
      const u = new URL(url, window.location.origin);
      const cat = u.searchParams.get('category');

      document.querySelectorAll('.front-filter-link').forEach(a => a.classList.remove('is-active'));

      if (!cat) {
        const allLink = document.querySelector('.front-filter-link[href="' + (window.__productsIndexAllUrl || '') + '"]');
        if (allLink) allLink.classList.add('is-active');
        return;
      }

      const match = document.querySelector('.front-filter-link[href*="category=' + CSS.escape(cat) + '"]');
      if (match) match.classList.add('is-active');
    }

    // ✅ helper: fetch + replace productsArea only
    async function loadProducts(url, pushState = true) {
      const pageScrollY = window.scrollY;
      const catsScrollTop = catsBox ? catsBox.scrollTop : 0;

      try {
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });

        if (!res.ok) {
          window.location.href = url;
          return;
        }

        const html = await res.text();
        const doc = new DOMParser().parseFromString(html, 'text/html');
        const newProductsArea = doc.getElementById('productsArea');

        if (!newProductsArea) {
          window.location.href = url;
          return;
        }

        productsArea.innerHTML = newProductsArea.innerHTML;

        // update sidebar active
        setActiveCategoryFromUrl(url);

        // restore scroll
        window.scrollTo(0, pageScrollY);
        if (catsBox) catsBox.scrollTop = catsScrollTop;

        if (pushState) history.pushState({ url }, '', url);
      } catch (e) {
        window.location.href = url;
      }
    }

    // ✅ 2) Intercept clicks: categories
    document.addEventListener('click', function (e) {
      const a = e.target.closest('.front-filter-link');
      if (!a) return;

      const href = a.getAttribute('href');
      if (!href) return;

      e.preventDefault();
      loadProducts(href, true);
    });

    // ✅ 3) Intercept pagination inside products area
    document.addEventListener('click', function (e) {
      const a = e.target.closest('#productsArea .pagination a');
      if (!a) return;

      const href = a.getAttribute('href');
      if (!href) return;

      e.preventDefault();
      loadProducts(href, true);
    });

    // ✅ 4) Intercept sort submit
    if (sortForm) {
      sortForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const url = sortForm.action + '?' + new URLSearchParams(new FormData(sortForm)).toString();
        loadProducts(url, true);
      });
    }

    // ✅ 5) Intercept search submit
    if (searchForm) {
      searchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const url = searchForm.action + '?' + new URLSearchParams(new FormData(searchForm)).toString();
        loadProducts(url, true);
      });
    }

    // ✅ 6) back/forward
    window.addEventListener('popstate', function (ev) {
      const url = (ev.state && ev.state.url) ? ev.state.url : window.location.href;
      loadProducts(url, false);
    });
  });
})();
