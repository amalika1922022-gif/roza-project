document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-card-link').forEach(function (card) {
        card.addEventListener('click', function (e) {
            // لو ضغط على لينك/زر داخل الكرت (مستقبلاً) ما نمنعو
            if (e.target.closest('a') || e.target.closest('button')) return;

            const href = this.getAttribute('data-href');
            if (href) window.location.href = href;
        });
    });
});
