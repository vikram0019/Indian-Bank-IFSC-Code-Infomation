/**
 * Live city/town filter + pagination on the bank overview page, mirrors
 * client/src/components/BankCityFilter.js (pagination is a plugin-only addition
 * since that list can run into the hundreds for large banks).
 */
(function () {
    var PAGE_SIZE = 30;

    document.addEventListener('DOMContentLoaded', function () {
        var input = document.getElementById('ifsc-city-filter-input');
        var list = document.getElementById('ifsc-city-filter-list');
        var empty = document.getElementById('ifsc-city-filter-empty');
        var pagination = document.getElementById('ifsc-city-pagination');
        if (!input || !list) {
            return;
        }

        var links = Array.prototype.slice.call(list.querySelectorAll('.ifsc-city-link'));
        var currentPage = 1;

        function matches(link, q) {
            return !q || link.dataset.city.indexOf(q) !== -1 || link.dataset.state.indexOf(q) !== -1;
        }

        function renderPagination(totalPages) {
            if (!pagination) return;
            if (totalPages <= 1) {
                pagination.hidden = true;
                pagination.innerHTML = '';
                return;
            }
            pagination.hidden = false;
            var html = '';
            html += '<button type="button" class="ifsc-page-btn" data-page="' + (currentPage - 1) + '"' + (currentPage === 1 ? ' disabled' : '') + '>&larr; Prev</button>';
            html += '<span class="ifsc-page-status">Page ' + currentPage + ' of ' + totalPages + '</span>';
            html += '<button type="button" class="ifsc-page-btn" data-page="' + (currentPage + 1) + '"' + (currentPage === totalPages ? ' disabled' : '') + '>Next &rarr;</button>';
            pagination.innerHTML = html;
        }

        function render() {
            var q = input.value.trim().toLowerCase();
            var matched = links.filter(function (link) { return matches(link, q); });

            if (q) {
                // While actively searching, show every match unpaginated —
                // filtered result sets are short enough not to need paging.
                links.forEach(function (link) {
                    link.style.display = matches(link, q) ? '' : 'none';
                });
                renderPagination(0);
            } else {
                var totalPages = Math.max(1, Math.ceil(matched.length / PAGE_SIZE));
                if (currentPage > totalPages) currentPage = totalPages;
                var start = (currentPage - 1) * PAGE_SIZE;
                var end = start + PAGE_SIZE;
                links.forEach(function (link, i) {
                    link.style.display = (i >= start && i < end) ? '' : 'none';
                });
                renderPagination(totalPages);
            }

            if (empty) {
                empty.style.display = matched.length === 0 ? '' : 'none';
            }
        }

        input.addEventListener('input', function () {
            currentPage = 1;
            render();
        });

        if (pagination) {
            pagination.addEventListener('click', function (e) {
                var btn = e.target.closest('.ifsc-page-btn');
                if (!btn || btn.disabled) return;
                currentPage = parseInt(btn.dataset.page, 10);
                render();
                list.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }

        render();
    });
})();
