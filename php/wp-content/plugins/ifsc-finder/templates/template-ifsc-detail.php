<?php
/**
 * /ifsc/{code} — mirrors client/src/app/ifsc/[code]/page.js
 */
if (!defined('ABSPATH')) {
    exit;
}
require_once IFSC_FINDER_DIR . 'templates/template-parts/flag-badge.php';

$ifsc_code = strtoupper(sanitize_text_field(get_query_var('ifsc_code')));
$branch = Ifsc_DB::get_by_ifsc($ifsc_code);

if (!$branch) {
    add_filter('pre_get_document_title', function () use ($ifsc_code) {
        return "IFSC {$ifsc_code} not found | " . get_bloginfo('name');
    });
    add_action('wp_head', function () {
        echo '<meta name="robots" content="noindex,follow">' . "\n";
    });
    get_header();
    echo '<main class="ifsc-main"><div class="ifsc-container ifsc-notfound">';
    echo '<h1>Not found</h1><p>We couldn\'t find what you were looking for.</p>';
    echo '<p><a href="' . esc_url(home_url('/')) . '">Back to search</a></p>';
    echo '</div></main>';
    get_footer();
    return;
}

$bank_name = $branch['bank'] ?: $branch['bankcode'];
$page_title = "{$branch['ifsc']} — {$bank_name}, {$branch['branch']}";
$description = sprintf(
    'IFSC code %s for %s %s branch in %s, %s. MICR: %s. NEFT: %s, RTGS: %s, IMPS: %s.',
    $branch['ifsc'],
    $bank_name,
    $branch['branch'],
    $branch['city'],
    $branch['state'],
    $branch['micr'] ?: 'N/A',
    $branch['neft'] ? 'Yes' : 'No',
    $branch['rtgs'] ? 'Yes' : 'No',
    $branch['imps'] ? 'Yes' : 'No'
);
$canonical_url = home_url('/ifsc/' . $branch['ifsc']);

add_filter('pre_get_document_title', function () use ($page_title) {
    return $page_title . ' | ' . get_bloginfo('name');
});
Ifsc_Seo::head_tags([
    'title' => $page_title,
    'description' => $description,
    'url' => $canonical_url,
]);

get_header();
?>
<main class="ifsc-main">
    <div class="ifsc-container">
        <div>
            <?php Ifsc_JsonLd::render_script(Ifsc_JsonLd::bank_or_credit_union($branch)); ?>

            <a class="ifsc-back-link" href="<?php echo esc_url(home_url('/')); ?>">&larr; Back to search</a>

            <p class="ifsc-eyebrow">IFSC CODE</p>
            <p class="ifsc-code-large"><?php echo esc_html($branch['ifsc']); ?></p>
            <h1><?php echo esc_html($bank_name); ?></h1>
            <p class="ifsc-muted"><?php echo esc_html($branch['branch']); ?></p>

            <div class="ifsc-detail-box">
                <div class="ifsc-detail-row"><span>Address</span><span><?php echo esc_html($branch['address'] ?: 'N/A'); ?></span></div>
                <div class="ifsc-detail-row"><span>City</span><span><?php echo esc_html($branch['city'] ?: 'N/A'); ?></span></div>
                <div class="ifsc-detail-row"><span>District</span><span><?php echo esc_html($branch['district'] ?: 'N/A'); ?></span></div>
                <div class="ifsc-detail-row"><span>State</span><span><?php echo esc_html($branch['state'] ?: 'N/A'); ?></span></div>
                <div class="ifsc-detail-row"><span>MICR Code</span><span><?php echo esc_html($branch['micr'] ?: 'N/A'); ?></span></div>
                <div class="ifsc-detail-row"><span>Contact</span><span><?php echo esc_html($branch['contact'] ?: 'N/A'); ?></span></div>
            </div>

            <h2>Transaction Modes</h2>
            <div class="ifsc-detail-box">
                <div class="ifsc-flag-row"><span>NEFT</span><?php ifsc_finder_flag_badge($branch['neft'] ? 'Available' : 'Not available', (bool) $branch['neft']); ?></div>
                <div class="ifsc-flag-row"><span>RTGS</span><?php ifsc_finder_flag_badge($branch['rtgs'] ? 'Available' : 'Not available', (bool) $branch['rtgs']); ?></div>
                <div class="ifsc-flag-row"><span>IMPS</span><?php ifsc_finder_flag_badge($branch['imps'] ? 'Available' : 'Not available', (bool) $branch['imps']); ?></div>
                <div class="ifsc-flag-row"><span>UPI</span><?php ifsc_finder_flag_badge($branch['upi'] ? 'Available' : 'Not available', (bool) $branch['upi']); ?></div>
            </div>
        </div>
    </div>
</main>
<?php get_footer(); ?>
