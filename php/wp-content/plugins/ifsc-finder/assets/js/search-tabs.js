/**
 * Home page 3-tab search, mirrors client/src/components/SearchTabs.js.
 */
(function () {
    function slugify(value) {
        return value.trim().toLowerCase().replace(/\s+/g, '-');
    }

    function escapeHtml(str) {
        var div = document.createElement('div');
        div.textContent = str == null ? '' : String(str);
        return div.innerHTML;
    }

    function badgeHtml(label, active) {
        var cls = active ? 'ifsc-badge ifsc-badge--active' : 'ifsc-badge ifsc-badge--inactive';
        return '<span class="' + cls + '">' + escapeHtml(label) + '</span>';
    }

    function buildResultsTable(results) {
        if (!results || !results.length) {
            return '';
        }
        var rows = results.map(function (b) {
            return (
                '<tr>' +
                '<td><a class="ifsc-code-link" href="/ifsc/' + encodeURIComponent(b.ifsc) + '">' + escapeHtml(b.ifsc) + '</a></td>' +
                '<td>' + escapeHtml(b.bank || b.bankcode) + '</td>' +
                '<td>' + escapeHtml(b.branch) + '</td>' +
                '<td class="ifsc-muted">' + escapeHtml(b.city) + ', ' + escapeHtml(b.state) + '</td>' +
                '<td><div class="ifsc-badge-row">' +
                badgeHtml('NEFT', !!b.neft) + badgeHtml('RTGS', !!b.rtgs) + badgeHtml('IMPS', !!b.imps) + badgeHtml('UPI', !!b.upi) +
                '</div></td>' +
                '</tr>'
            );
        }).join('');

        return (
            '<div class="ifsc-table-wrap"><table class="ifsc-results-table"><thead><tr>' +
            '<th scope="col">IFSC Code</th><th scope="col">Bank</th><th scope="col">Branch</th>' +
            '<th scope="col">City, State</th><th scope="col">Transaction Modes</th>' +
            '</tr></thead><tbody>' + rows + '</tbody></table></div>'
        );
    }

    document.addEventListener('DOMContentLoaded', function () {
        var root = document.getElementById('ifsc-search-tabs');
        if (!root) {
            return;
        }

        var tabButtons = root.querySelectorAll('.ifsc-tab-btn');
        var panels = root.querySelectorAll('.ifsc-tab-panel');

        tabButtons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tab = btn.dataset.tab;
                tabButtons.forEach(function (b) { b.classList.toggle('is-active', b === btn); });
                panels.forEach(function (p) {
                    var active = p.dataset.panel === tab;
                    p.hidden = !active;
                    p.classList.toggle('is-active', active);
                });
            });
        });

        // --- IFSC tab ---
        var ifscForm = root.querySelector('#ifsc-form-ifsc');
        var ifscBox = ifscForm.querySelector('.ifsc-autosuggest');
        IfscAutosuggest.attach(ifscBox, function (item) {
            window.location.href = '/ifsc/' + encodeURIComponent(item.value);
        });
        ifscForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var val = ifscBox.querySelector('.ifsc-input').value.trim();
            if (val) {
                window.location.href = '/ifsc/' + encodeURIComponent(val.toUpperCase());
            }
        });

        // --- Bank + City tab ---
        var bankForm = root.querySelector('#ifsc-form-bank');
        var bankBox = bankForm.querySelector('[data-suggest-type="bank"]');
        var cityBox = bankForm.querySelector('[data-suggest-type="city"]');
        var bankSubmit = bankForm.querySelector('button[type="submit"]');
        var bankHint = bankForm.querySelector('.ifsc-hint');
        var selectedBank = null;

        function refreshBankSubmitState() {
            var cityVal = cityBox.querySelector('.ifsc-input').value.trim();
            bankSubmit.disabled = !selectedBank || !cityVal;
        }

        bankBox.querySelector('.ifsc-input').addEventListener('input', function () {
            selectedBank = null;
            if (bankHint) bankHint.hidden = !bankBox.querySelector('.ifsc-input').value.trim();
            refreshBankSubmitState();
        });
        cityBox.querySelector('.ifsc-input').addEventListener('input', refreshBankSubmitState);

        IfscAutosuggest.attach(bankBox, function (item, input) {
            input.value = item.label;
            selectedBank = item.value;
            if (bankHint) bankHint.hidden = true;
            refreshBankSubmitState();
        });
        IfscAutosuggest.attach(cityBox, function (item, input) {
            input.value = item.value;
            refreshBankSubmitState();
        });

        bankForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var cityVal = cityBox.querySelector('.ifsc-input').value.trim();
            if (selectedBank && cityVal) {
                window.location.href = '/bank/' + encodeURIComponent(slugify(selectedBank)) + '/' + encodeURIComponent(slugify(cityVal));
            }
        });

        // --- Branch name tab ---
        var branchForm = root.querySelector('#ifsc-form-branch');
        var branchBox = branchForm.querySelector('.ifsc-autosuggest');
        var branchResultsEl = document.getElementById('ifsc-branch-results');

        IfscAutosuggest.attach(branchBox, function (item) {
            window.location.href = '/ifsc/' + encodeURIComponent(item.value);
        });

        branchForm.addEventListener('submit', function (e) {
            e.preventDefault();
            var q = branchBox.querySelector('.ifsc-input').value.trim();
            if (!q) return;
            branchResultsEl.innerHTML = '<p class="ifsc-muted">Searching...</p>';
            fetch(window.IfscFinder.restUrl + 'branch/search?name=' + encodeURIComponent(q))
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    var results = data.results || [];
                    if (!results.length) {
                        branchResultsEl.innerHTML = '<p class="ifsc-muted">No branches found for &quot;' + escapeHtml(q) + '&quot;.</p>';
                    } else {
                        branchResultsEl.innerHTML = buildResultsTable(results);
                    }
                })
                .catch(function () {
                    branchResultsEl.innerHTML = '<p class="ifsc-muted">Something went wrong. Please try again.</p>';
                });
        });
    });
})();
