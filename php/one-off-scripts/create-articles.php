<?php
/**
 * One-off script: creates the three "Related Reading" article pages for
 * ifscbankfinder.com. Run once via `php create-articles.php` from the site
 * root (where wp-load.php lives), then delete this file — it's idempotent
 * (skips any page whose slug already exists) but has no reason to stick
 * around once it's done its job.
 */
define('WP_USE_THEMES', false);
require('wp-load.php');

$articles = [];

$articles[] = [
    'slug' => 'ifsc-vs-swift-code',
    'title' => "IFSC Code vs SWIFT Code: What's the Difference?",
    'content' => <<<'HTML'
<h1>IFSC Code vs SWIFT Code: What's the Difference?</h1>
<p>If you've ever sent money abroad, you've probably run into a second code you don't see for domestic transfers: SWIFT. Both IFSC and SWIFT codes identify a bank branch so a payment reaches the right place, but they exist for different networks, and mixing them up is one of the most common reasons an international transfer gets delayed or rejected.</p>

<h2>What an IFSC code does</h2>
<p>An IFSC (Indian Financial System Code) is an 11-character code issued by the Reserve Bank of India, used exclusively for domestic electronic transfers within India — NEFT, RTGS, and IMPS all route through it. Every bank branch in India that handles these transfers has exactly one IFSC, and it never applies outside India's banking system.</p>

<h2>What a SWIFT code does</h2>
<p>A SWIFT code (also called a BIC, or Bank Identifier Code) is an 8- or 11-character code used for international wire transfers, standardized globally by SWIFT (Society for Worldwide Interbank Financial Telecommunication). If someone overseas is sending you money, or you're sending money to a bank account outside India, the receiving bank needs a SWIFT code, not an IFSC — Indian banks that handle foreign remittances have their own SWIFT codes separate from their IFSCs.</p>

<h2>Side-by-side comparison</h2>
<div class="ifsc-table-wrap">
<table class="ifsc-content-table">
<thead><tr><th></th><th>IFSC</th><th>SWIFT</th></tr></thead>
<tbody>
<tr><td class="ifsc-muted">Used for</td><td>Domestic transfers within India</td><td>International wire transfers</td></tr>
<tr><td class="ifsc-muted">Length</td><td>11 characters</td><td>8 or 11 characters</td></tr>
<tr><td class="ifsc-muted">Issued by</td><td>Reserve Bank of India</td><td>SWIFT (global standard)</td></tr>
<tr><td class="ifsc-muted">Identifies</td><td>A specific branch</td><td>A bank (and sometimes branch)</td></tr>
<tr><td class="ifsc-muted">Networks</td><td>NEFT, RTGS, IMPS</td><td>International wire transfer networks</td></tr>
</tbody>
</table>
</div>

<h2>Which one do you need?</h2>
<p>If you're transferring money between two Indian bank accounts, you only need the IFSC — SWIFT doesn't come into it at all. If money is crossing India's border in either direction, the receiving bank will ask for a SWIFT code, and depending on the transfer method, you may need to provide both the SWIFT code and additional routing details for the receiving branch.</p>
<p>Not every bank branch has its own SWIFT code — many banks only register SWIFT codes for select branches (often a head office or a designated international-transactions branch), while every operational branch has its own unique IFSC. If your usual branch doesn't have one, your bank can tell you which branch's SWIFT code to use instead.</p>
HTML,
];

$articles[] = [
    'slug' => 'wrong-ifsc-code-what-happens',
    'title' => 'What Happens If You Enter the Wrong IFSC Code?',
    'content' => <<<'HTML'
<h1>What Happens If You Enter the Wrong IFSC Code?</h1>
<p>An IFSC code is what tells the banking system exactly which branch should receive a transfer — so getting even one character wrong can send your money somewhere you didn't intend. What actually happens next depends on whether the wrong code happens to be valid or not, and knowing the difference helps you react quickly if a transfer goes sideways.</p>

<h2>Case 1: The IFSC code doesn't exist</h2>
<p>Every IFSC follows a strict format and is checked against the Reserve Bank of India's list of valid codes before a transfer is processed. If you enter a code that doesn't match any real branch — a typo that breaks the format, or a code that was valid once but has since been retired after a bank merger — the transfer is rejected automatically, usually within minutes. The money doesn't leave your account, or if it was briefly debited, it's reversed. This is the safer failure mode: annoying, but not risky.</p>

<h2>Case 2: The IFSC code is valid, but wrong</h2>
<p>This is the case worth being careful about. If you enter a code that's technically valid but belongs to a different branch than the one you meant — easy to do if two branches of the same bank have similar-looking codes — the payment can go through successfully to that other branch. Because the account number and IFSC are usually cross-checked against the beneficiary's name only loosely (or not at all, depending on the transfer method), the money can land in an account you never intended to pay.</p>
<p>Recovering funds sent to the wrong account is not guaranteed. It depends on cooperation from the receiving bank and, ultimately, the account holder's consent to reverse it — banks can't unilaterally debit someone else's account just because a sender made a mistake.</p>

<h2>What to do if you suspect a wrong transfer</h2>
<ul>
<li><strong>Act immediately.</strong> Contact your bank's customer care or visit a branch as soon as you notice the error — the sooner a bank is notified, the better the chance of freezing or reversing the transaction before funds are withdrawn.</li>
<li><strong>Provide transaction details.</strong> Have your transaction reference number, the date and time, amount, and the incorrect IFSC/account details ready — this speeds up your bank's investigation.</li>
<li><strong>File a formal complaint if needed.</strong> If your bank can't resolve it directly, you can escalate through the RBI's Banking Ombudsman scheme.</li>
</ul>

<h2>How to avoid this in the first place</h2>
<p>The most reliable habit is to never type an IFSC code from memory or a screenshot without double-checking it against an authoritative source right before you send money — a passbook, a cheque leaf, or a lookup tool like this one. Many banks also show the beneficiary's registered name after you enter their account number and IFSC, before you confirm the transfer; always read that name carefully rather than clicking through it, since it's the last checkpoint before the money actually moves.</p>
HTML,
];

$articles[] = [
    'slug' => 'upi-vs-imps',
    'title' => 'UPI vs IMPS: Which Should You Use for Instant Money Transfers?',
    'content' => <<<'HTML'
<h1>UPI vs IMPS: Which Should You Use for Instant Money Transfers?</h1>
<p>Both UPI and IMPS move money between Indian bank accounts instantly, 24 hours a day, every day of the year — so it's easy to assume they're interchangeable. In practice they solve slightly different problems, and knowing when each one fits better can save you a failed payment or an unnecessary trip to net banking.</p>

<h2>What IMPS is</h2>
<p>IMPS (Immediate Payment Service) is a bank-to-bank transfer method that predates UPI. To send money via IMPS, you need the recipient's account number and IFSC code (or, on some apps, their registered mobile number linked via MMID). It works through your bank's own app or net banking, and it's the underlying rail that many older banking apps still rely on for instant transfers.</p>

<h2>What UPI is</h2>
<p>UPI (Unified Payments Interface) is a newer layer built on top of the banking system, designed to make sending money as simple as an address. Instead of an account number and IFSC, you send money to a UPI ID (like <code>name@bank</code>) or by scanning a QR code, and UPI resolves that behind the scenes to the right bank account — no need to know or enter the recipient's IFSC at all. Apps like Google Pay, PhonePe, and Paytm are all UPI apps, but so is every major bank's own app.</p>

<h2>Side-by-side comparison</h2>
<div class="ifsc-table-wrap">
<table class="ifsc-content-table">
<thead><tr><th></th><th>UPI</th><th>IMPS</th></tr></thead>
<tbody>
<tr><td class="ifsc-muted">What you need</td><td>UPI ID, phone number, or QR code</td><td>Account number + IFSC (or MMID)</td></tr>
<tr><td class="ifsc-muted">Availability</td><td>24&times;7, every day</td><td>24&times;7, every day</td></tr>
<tr><td class="ifsc-muted">Typical use</td><td>Everyday payments, small businesses, P2P</td><td>Bank-to-bank transfers, larger amounts</td></tr>
<tr><td class="ifsc-muted">Per-transaction limit</td><td>Commonly &#8377;1 lakh (varies by bank/app)</td><td>Commonly up to &#8377;5 lakh (varies by bank)</td></tr>
<tr><td class="ifsc-muted">Needs IFSC?</td><td>No</td><td>Yes (unless using MMID)</td></tr>
</tbody>
</table>
</div>
<p>Limits above are typical ranges — your bank and payment app both set their own daily/per-transaction caps, so check your specific app for the exact numbers.</p>

<h2>When to use which</h2>
<p>For everyday spending — splitting a bill, paying a shopkeeper, sending money to a friend — UPI is almost always simpler, since you skip account numbers and IFSCs entirely and most payments settle in seconds. IMPS is still the more natural choice for larger or more formal transfers, like paying rent to a landlord's bank account or moving money between your own accounts at different banks, especially when you already have the recipient's account details handy but not a UPI ID. If a large transfer fails or is capped on UPI, falling back to IMPS or NEFT/RTGS through net banking is usually the next step.</p>
HTML,
];

foreach ($articles as $a) {
    $existing = get_page_by_path($a['slug']);
    if ($existing) {
        echo "Skipped (already exists): {$a['slug']} (ID {$existing->ID})\n";
        continue;
    }

    $post_id = wp_insert_post([
        'post_title' => $a['title'],
        'post_name' => $a['slug'],
        'post_content' => $a['content'],
        'post_status' => 'publish',
        'post_type' => 'page',
    ], true);

    if (is_wp_error($post_id)) {
        echo "Error creating {$a['slug']}: " . $post_id->get_error_message() . "\n";
    } else {
        echo "Created: {$a['slug']} (ID {$post_id})\n";
    }
}
