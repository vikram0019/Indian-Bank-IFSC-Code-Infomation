<?php
/**
 * /bank/{bank-slug} — mirrors client/src/app/bank/[bankcode]/page.js
 * Note: the slug is a slugified BANK NAME, not a bankcode, matching the Next app.
 */
if (!defined('ABSPATH')) {
    exit;
}

$bank_slug = sanitize_text_field(get_query_var('bank_slug'));
$bank_name_prefix = Ifsc_Rewrite::unslugify($bank_slug);
$data = Ifsc_DB::cities_for_bank($bank_name_prefix);

if (empty($data['cities'])) {
    get_header();
    echo '<main class="ifsc-main"><div class="ifsc-container ifsc-notfound">';
    echo '<h1>Not found</h1><p>We couldn\'t find what you were looking for.</p>';
    echo '<p><a href="' . esc_url(home_url('/')) . '">Back to search</a></p>';
    echo '</div></main>';
    get_footer();
    return;
}

$bank_name = $data['bank'];
$page_title = "{$bank_name} IFSC Codes — Find Branches by City";
$description = "Browse {$bank_name} branches by city or town and find IFSC codes, MICR codes, and NEFT/RTGS/IMPS availability.";

add_filter('pre_get_document_title', function () use ($page_title) {
    return $page_title . ' | ' . get_bloginfo('name');
});
add_action('wp_head', function () use ($description) {
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
});

get_header();
?>
<main class="ifsc-main">
    <div class="ifsc-container ifsc-layout">
        <div>
            <a class="ifsc-back-link" href="<?php echo esc_url(home_url('/')); ?>">&larr; Back to search</a>
            <h1 class="ifsc-mt"><?php echo esc_html($bank_name); ?></h1>
            <p class="ifsc-muted ifsc-mb">Select a city or town to see <?php echo esc_html($bank_name); ?> branches and their IFSC codes.</p>

            <input
                type="text"
                id="ifsc-city-filter-input"
                class="ifsc-input"
                placeholder="Search city or town&hellip;"
                autocomplete="off"
            >

            <div id="ifsc-city-filter-list" class="ifsc-city-grid">
                <?php foreach ($data['cities'] as $c) : ?>
                    <a
                        class="ifsc-city-link"
                        data-city="<?php echo esc_attr(strtolower($c['city'])); ?>"
                        data-state="<?php echo esc_attr(strtolower($c['state'])); ?>"
                        href="<?php echo esc_url(home_url('/bank/' . $bank_slug . '/' . Ifsc_Rewrite::slugify($c['city']))); ?>"
                    >
                        <span><?php echo esc_html($c['city']); ?><span class="ifsc-muted">, <?php echo esc_html($c['state']); ?></span></span>
                        <span class="ifsc-muted"><?php echo esc_html($c['count']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
            <p id="ifsc-city-filter-empty" class="ifsc-muted" style="display:none;">No matching city or town found.</p>
            <nav id="ifsc-city-pagination" class="ifsc-pagination" aria-label="City pagination" hidden></nav>
        </div>
        <aside class="ifsc-sidebar"><?php echo ifsc_finder_ad_slot('sidebar'); ?></aside>
    </div>
</main>
<?php get_footer(); ?>
