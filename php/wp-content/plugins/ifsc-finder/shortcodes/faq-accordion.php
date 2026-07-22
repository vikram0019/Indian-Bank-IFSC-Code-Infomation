<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * [ifsc_faq_accordion] — mirrors client/src/components/FaqAccordion.js, including
 * the FAQPage JSON-LD script tag.
 */
add_shortcode('ifsc_faq_accordion', function () {
    $faqs = ifsc_finder_get_faqs();
    ob_start();
    ?>
    <div class="ifsc-faq-accordion">
        <?php Ifsc_JsonLd::render_script(Ifsc_JsonLd::faq_page($faqs)); ?>
        <?php foreach ($faqs as $i => $faq) : ?>
            <div class="ifsc-faq-item">
                <button type="button" class="ifsc-faq-question" aria-expanded="false">
                    <?php echo esc_html($faq['question']); ?>
                    <span class="ifsc-faq-icon">+</span>
                </button>
                <p class="ifsc-faq-answer" hidden><?php echo esc_html($faq['answer']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
});

/**
 * [ifsc_guide] — the full "IFSC & MICR Code Guide" page, mirrors
 * client/src/app/faq/page.js. Content is original wording (previously rewritten
 * after a copyright concern), ported verbatim — do not regenerate.
 */
add_shortcode('ifsc_guide', function () {
    $toc = ifsc_finder_get_toc();
    ob_start();
    ?>
    <div class="ifsc-guide">
        <a class="ifsc-back-link" href="<?php echo esc_url(home_url('/')); ?>">&larr; Back to Home</a>
        <h1 class="ifsc-mt">IFSC &amp; MICR Code Guide</h1>

        <div class="ifsc-guide-layout">
            <div class="ifsc-guide-content">

                <section id="ifsc-explained" class="ifsc-section">
                    <h2>IFSC codes, explained</h2>
                    <p>Every bank branch in India that participates in electronic payments carries an eleven-character code called an IFSC (Indian Financial System Code), issued by the Reserve Bank of India. Read left to right, it packs in three pieces of information: the first four letters name the bank, a fixed "0" sits in the fifth spot as a separator, and the remaining six characters pin down the exact branch. Banking systems rely on this code, rather than a branch address, to route NEFT, RTGS, and IMPS transfers to the right place.</p>
                </section>

                <section id="finding-your-code" class="ifsc-section">
                    <h2>Finding your code</h2>
                    <p>The fastest option is usually the one you're already using: type a bank name and city, a branch name, or the code itself into the search box on this page and you'll get the branch record directly.</p>
                    <p>If you'd rather confirm it another way, it's also printed on the first page of your passbook and along the bottom of every cheque leaf, and most banks list it in the branch locator on their own website.</p>
                </section>

                <section id="why-it-matters" class="ifsc-section">
                    <h2>Why it matters</h2>
                    <p>Two branches of the same bank — even two branches in the same city — never share an IFSC, so the code is the one piece of routing information a transfer can't succeed without. Get the account number right but the IFSC wrong and the payment either bounces back or, worse, lands at an unintended branch. Entering it correctly is what lets NEFT, RTGS, and IMPS confirm a transfer in minutes rather than needing manual verification.</p>
                </section>

                <section id="micr-code" class="ifsc-section">
                    <h2>MICR: the cheque code</h2>
                    <p>MICR (Magnetic Ink Character Recognition) solves a similar problem for paper cheques instead of electronic transfers. It's a nine-digit number printed in special magnetic ink so that cheque-sorting machines can read it optically without manual data entry, which is what lets banks clear large volumes of cheques quickly and with fewer errors.</p>
                    <div class="ifsc-table-wrap">
                        <table class="ifsc-content-table">
                            <thead><tr><th></th><th>IFSC</th><th>MICR</th></tr></thead>
                            <tbody>
                                <tr><td class="ifsc-muted">Used for</td><td>Online transfers &mdash; NEFT, RTGS, IMPS</td><td>Cheque clearing</td></tr>
                                <tr><td class="ifsc-muted">Format</td><td>11 characters, letters + digits</td><td>9 digits</td></tr>
                                <tr><td class="ifsc-muted">First part identifies</td><td>The bank</td><td>The city</td></tr>
                                <tr><td class="ifsc-muted">Last part identifies</td><td>The branch</td><td>The branch</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="transfer-methods" class="ifsc-section">
                    <h2>NEFT vs RTGS vs IMPS</h2>
                    <p>All three move money using the same IFSC, but they differ in when they run and what a bank is allowed to charge for them. Figures below are typical fee ceilings — your bank sets its own charges under these limits, and many banks now waive NEFT fees entirely for savings accounts, so treat this as a rough guide rather than a quote.</p>
                    <div class="ifsc-table-wrap">
                        <table class="ifsc-content-table">
                            <thead><tr><th>Method</th><th>Available</th><th>Typical fee (up to &#8377;10k)</th><th>Typical fee (above &#8377;2L)</th></tr></thead>
                            <tbody>
                                <tr><td>NEFT</td><td>8 AM&ndash;7 PM, weekdays</td><td>&#8377;2&ndash;3</td><td>up to &#8377;25</td></tr>
                                <tr><td>RTGS</td><td>9 AM&ndash;4:30 PM, weekdays</td><td>not applicable (&#8377;2L minimum)</td><td>up to &#8377;50</td></tr>
                                <tr><td>IMPS</td><td>24&times;7, every day</td><td>&#8377;5&ndash;10</td><td>up to &#8377;15</td></tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <section id="how-it-works" class="ifsc-section">
                    <h2>How a transfer is routed</h2>
                    <p>When you send money, your bank's system reads the recipient's IFSC in two stages: the bank-identifying prefix tells it which clearing network to hand the payment off to, and once it arrives at that bank, the branch-identifying suffix tells that bank's internal systems which branch — and therefore which account — should receive it. That's the whole reason the code needs to be exact: a single wrong character can point a payment at a completely different branch.</p>
                </section>

                <section id="faq" class="ifsc-section">
                    <h2>Frequently Asked Questions</h2>
                    <?php echo do_shortcode('[ifsc_faq_accordion]'); ?>
                </section>

                <section id="popular-banks" class="ifsc-section">
                    <?php echo do_shortcode('[ifsc_popular_banks]'); ?>
                </section>

            </div>

            <nav class="ifsc-toc">
                <p class="ifsc-toc-title">On this page</p>
                <ul>
                    <?php foreach ($toc as $item) : ?>
                        <li><a href="#<?php echo esc_attr($item['id']); ?>"><?php echo esc_html($item['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        </div>
    </div>
    <?php
    return ob_get_clean();
});
