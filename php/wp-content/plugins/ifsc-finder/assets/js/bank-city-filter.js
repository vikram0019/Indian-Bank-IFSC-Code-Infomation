/**
 * Live city/town filter on the bank overview page, mirrors
 * client/src/components/BankCityFilter.js.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('ifsc-city-filter-input');
        var list = document.getElementById('ifsc-city-filter-list');
        var empty = document.getElementById('ifsc-city-filter-empty');
        if (!input || !list) {
            return;
        }

        var links = Array.prototype.slice.call(list.querySelectorAll('.ifsc-city-link'));

        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            var visibleCount = 0;

            links.forEach(function (link) {
                var match = !q || link.dataset.city.indexOf(q) !== -1 || link.dataset.state.indexOf(q) !== -1;
                link.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            if (empty) {
                empty.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    });
})();
