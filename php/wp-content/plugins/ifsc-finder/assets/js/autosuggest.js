/**
 * Reusable debounced-autosuggest attachment for a `.ifsc-autosuggest` container,
 * mirrors client/src/components/SearchBar.js.
 */
(function () {
    function debounce(fn, delay) {
        var timer;
        return function () {
            var args = arguments;
            var ctx = this;
            clearTimeout(timer);
            timer = setTimeout(function () {
                fn.apply(ctx, args);
            }, delay);
        };
    }

    function attach(container, onSelect) {
        var input = container.querySelector('.ifsc-input');
        var list = container.querySelector('.ifsc-suggest-list');
        var type = container.dataset.suggestType;

        function renderList(items) {
            list.innerHTML = '';
            if (!items || !items.length) {
                list.hidden = true;
                return;
            }
            items.forEach(function (item) {
                var li = document.createElement('li');
                var btn = document.createElement('button');
                btn.type = 'button';
                btn.textContent = item.label;
                btn.addEventListener('mousedown', function (e) {
                    e.preventDefault();
                    list.hidden = true;
                    onSelect(item, input, container);
                });
                li.appendChild(btn);
                list.appendChild(li);
            });
            list.hidden = false;
        }

        var fetchSuggestions = debounce(function () {
            var q = input.value.trim();
            if (q.length < 2) {
                list.hidden = true;
                list.innerHTML = '';
                return;
            }
            var url = window.IfscFinder.restUrl + 'suggest?q=' + encodeURIComponent(q) + '&type=' + encodeURIComponent(type);
            fetch(url)
                .then(function (res) { return res.ok ? res.json() : []; })
                .then(renderList)
                .catch(function () { list.hidden = true; });
        }, 300);

        input.addEventListener('input', fetchSuggestions);
        input.addEventListener('focus', function () {
            if (list.children.length) {
                list.hidden = false;
            }
        });
        input.addEventListener('blur', function () {
            setTimeout(function () {
                list.hidden = true;
            }, 150);
        });
    }

    window.IfscAutosuggest = { attach: attach };
})();
