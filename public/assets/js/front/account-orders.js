document.addEventListener('DOMContentLoaded', function () {
    const rows = document.querySelectorAll('.js-order-toggle');

    rows.forEach(function (row) {
        row.addEventListener('click', function () {
            const detailsRow = this.nextElementSibling;

            if (detailsRow && detailsRow.classList.contains('order-details-row')) {
                detailsRow.classList.toggle('d-none');
            }
        });
    });
});
