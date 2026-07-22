/**
 * FAQ accordion toggle + purely-CSS-anchor table of contents (no JS needed for
 * the TOC itself — anchors are plain <a href="#id">). This file only wires the
 * accordion open/close behavior, mirrors client/src/components/FaqAccordion.js.
 */
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.ifsc-faq-item').forEach(function (item) {
            var btn = item.querySelector('.ifsc-faq-question');
            var answer = item.querySelector('.ifsc-faq-answer');
            btn.addEventListener('click', function () {
                var isOpen = item.classList.contains('is-open');
                item.classList.toggle('is-open', !isOpen);
                answer.hidden = isOpen;
                btn.setAttribute('aria-expanded', String(!isOpen));
            });
        });
    });
})();
