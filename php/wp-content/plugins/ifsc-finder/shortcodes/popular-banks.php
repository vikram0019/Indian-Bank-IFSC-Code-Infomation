<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * [ifsc_popular_banks] — mirrors client/src/components/PopularBanks.js.
 */
add_shortcode('ifsc_popular_banks', function () {
    $banks = ifsc_finder_get_popular_banks();
    ob_start();
    ?>
    <div class="ifsc-popular-banks">
        <h2>Popular Banks</h2>
        <div class="ifsc-bank-pills">
            <?php foreach ($banks as $bank) : ?>
                <a class="ifsc-bank-pill" href="<?php echo esc_url(home_url('/bank/' . $bank['slug'])); ?>">
                    <?php echo esc_html($bank['name']); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
});
