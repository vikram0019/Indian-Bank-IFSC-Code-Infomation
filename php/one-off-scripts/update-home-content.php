<?php
/**
 * One-off script: expands the Home page content for ifscbankfinder.com with
 * genuine explanatory sections below the search tool (which stays at the
 * top, unchanged). Run once via `php update-home-content.php` from the site
 * root (where wp-load.php lives), then delete this file.
 */
define('WP_USE_THEMES', false);
require('wp-load.php');

$front_id = (int) get_option('page_on_front');
if (!$front_id) {
    echo "No static front page set.\n";
    exit(1);
}

$new_content = <<<'HTML'
<h1>Find Any Indian Bank Branch's IFSC Code</h1>
<p>Search by IFSC code, bank + city, or branch name to get MICR, address, and NEFT/RTGS/IMPS/UPI details.</p>
[ifsc_search_tabs]
[ifsc_popular_banks]

<h2>How to Use This Tool</h2>
<p>Pick whichever search method matches what you already have on hand. If you know the IFSC code, type it in directly for an instant match. If you only know the bank and the city or town, use the "By Bank + City" tab to browse every branch of that bank there. If you just remember the branch name — say, "Fort Mumbai" or "MG Road" — the "By Branch Name" search will find it across all banks.</p>

<h2>What You'll Find on Each Result</h2>
<p>Every branch result includes the full IFSC code, the MICR code (for cheque clearing), the complete branch address, and which electronic transfer methods — NEFT, RTGS, IMPS, and UPI — that specific branch supports. This is the same information you'd otherwise need to dig up from a passbook, a cheque leaf, or the bank's own branch locator.</p>

<h2>Why People Use IFSC Bank Finder</h2>
<ul>
<li><strong>No sign-up required.</strong> Search and get your answer immediately — no account, no email, no app to install.</li>
<li><strong>Covers all major Indian banks</strong> and thousands of branches nationwide, kept in sync with the Reserve Bank of India's published branch directory.</li>
<li><strong>Built for speed.</strong> Autosuggest as you type, results in seconds, no clutter between you and the answer.</li>
</ul>

<h2>Want to Understand How It All Works?</h2>
<p>If you're curious about what an IFSC code actually does, how it differs from a SWIFT code, or how UPI compares to IMPS, our <a href="/ifsc-micr-code-guide/">IFSC &amp; MICR Code Guide</a> covers the fundamentals in depth, with links to more articles on banking basics — account verification, cancelled cheques, Aadhaar linking, and more.</p>
HTML;

$result = wp_update_post([
    'ID' => $front_id,
    'post_content' => $new_content,
], true);

if (is_wp_error($result)) {
    echo "Error: " . $result->get_error_message() . "\n";
} else {
    echo "Updated home page (ID {$result})\n";
}
