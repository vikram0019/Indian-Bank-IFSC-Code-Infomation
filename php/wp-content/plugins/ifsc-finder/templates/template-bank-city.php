<?php
/**
 * /bank/{bank-slug}/{city-slug} — mirrors client/src/app/bank/[bankcode]/[city]/page.js
 */
if (!defined('ABSPATH')) {
    exit;
}
require_once IFSC_FINDER_DIR . 'templates/template-parts/results-table.php';

$bank_slug = sanitize_text_field(get_query_var('bank_slug'));
$city_slug = sanitize_text_field(get_query_var('city_slug'));
$bank_name_prefix = Ifsc_Rewrite::unslugify($bank_slug);
$city_name = Ifsc_Rewrite::unslugify($city_slug);

$data = Ifsc_DB::search_by_bank_and_city($bank_name_prefix, $city_name, 1, 100);

if (empty($data['results'])) {
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

$bank_name = $data['results'][0]['bank'] ?: $bank_name_prefix;
$page_title = "{$bank_name} Branches in {$city_name} — IFSC Codes";
$description = "Browse all {$bank_name} branches in {$city_name} with IFSC codes, addresses, and MICR codes.";
$canonical_url = home_url('/bank/' . $bank_slug . '/' . $city_slug);

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
            <a class="ifsc-back-link" href="<?php echo esc_url(home_url('/')); ?>">&larr; Back to search</a>
            <h1 class="ifsc-mt"><?php echo esc_html($bank_name); ?> Branches in <?php echo esc_html($city_name); ?></h1>
            <p class="ifsc-muted ifsc-mb"><?php echo esc_html($data['total']); ?> branch(es) found</p>

            <?php ifsc_finder_results_table($data['results']); ?>
        </div>
    </div>
</main>
<?php get_footer(); ?>
