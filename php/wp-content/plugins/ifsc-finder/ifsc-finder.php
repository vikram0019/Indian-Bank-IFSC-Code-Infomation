<?php
/**
 * Plugin Name: IFSC Finder
 * Description: Search engine for Indian bank IFSC codes and branch details. Custom DB table + REST API, ported from the Node/Next.js version of this project.
 * Version: 1.0.0
 * Author: IFSC Finder
 * Text Domain: ifsc-finder
 */

if (!defined('ABSPATH')) {
    exit;
}

define('IFSC_FINDER_VERSION', '1.0.0');
define('IFSC_FINDER_DIR', plugin_dir_path(__FILE__));
define('IFSC_FINDER_URL', plugin_dir_url(__FILE__));
define('IFSC_FINDER_SOURCE_URL', 'https://github.com/razorpay/ifsc/releases/latest/download/IFSC.csv');
define('IFSC_FINDER_ADMIN_SLUG', 'ifsc-finder-admin');

require_once IFSC_FINDER_DIR . 'includes/class-ifsc-db.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-activator.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-deactivator.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-rewrite.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-importer.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-rest-controller.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-admin-page.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-sitemap.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-cron.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-jsonld.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-adsense.php';
require_once IFSC_FINDER_DIR . 'includes/class-ifsc-seo.php';

if (defined('WP_CLI') && WP_CLI) {
    require_once IFSC_FINDER_DIR . 'includes/class-ifsc-cli-command.php';
}

register_activation_hook(__FILE__, ['Ifsc_Activator', 'activate']);
register_deactivation_hook(__FILE__, ['Ifsc_Deactivator', 'deactivate']);

Ifsc_Rewrite::init();
Ifsc_REST_Controller::init();
Ifsc_Admin_Page::init();
Ifsc_Sitemap::init();
Ifsc_Cron::init();
Ifsc_JsonLd::init();
Ifsc_AdSense::init();

add_action('wp_enqueue_scripts', function () {
    wp_enqueue_style('ifsc-finder', IFSC_FINDER_URL . 'assets/css/ifsc-finder.css', [], IFSC_FINDER_VERSION);
    wp_enqueue_script('ifsc-finder-autosuggest', IFSC_FINDER_URL . 'assets/js/autosuggest.js', [], IFSC_FINDER_VERSION, true);
    wp_enqueue_script('ifsc-finder-search-tabs', IFSC_FINDER_URL . 'assets/js/search-tabs.js', ['ifsc-finder-autosuggest'], IFSC_FINDER_VERSION, true);
    wp_enqueue_script('ifsc-finder-bank-city-filter', IFSC_FINDER_URL . 'assets/js/bank-city-filter.js', [], IFSC_FINDER_VERSION, true);
    wp_enqueue_script('ifsc-finder-toc', IFSC_FINDER_URL . 'assets/js/table-of-contents.js', [], IFSC_FINDER_VERSION, true);
    wp_localize_script('ifsc-finder-autosuggest', 'IfscFinder', [
        'restUrl' => esc_url_raw(rest_url('ifsc/v1/')),
        'nonce' => wp_create_nonce('wp_rest'),
    ]);
});

/**
 * Placeholder ad slot, mirrors client/src/components/AdSlot.js. Swap the inner markup
 * for a real AdSense <ins> snippet once a publisher ID is available.
 */
function ifsc_finder_ad_slot($variant = 'sidebar')
{
    $variant = in_array($variant, ['sidebar', 'footer', 'inline'], true) ? $variant : 'sidebar';
    return '<div class="ifsc-ad-slot ifsc-ad-slot--' . esc_attr($variant) . '">Ad placeholder (' . esc_html($variant) . ')</div>';
}

require_once IFSC_FINDER_DIR . 'shortcodes/search-tabs.php';
require_once IFSC_FINDER_DIR . 'shortcodes/popular-banks.php';
require_once IFSC_FINDER_DIR . 'shortcodes/faq-accordion.php';
require_once IFSC_FINDER_DIR . 'shortcodes/ad-slot.php';
require_once IFSC_FINDER_DIR . 'data/faq-content.php';

/**
 * Site-wide footer ad slot on every front-end page, mirrors the persistent
 * <AdSlot variant="footer" /> in client/src/app/layout.js's root layout.
 */
add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    echo '<div class="ifsc-container">' . ifsc_finder_ad_slot('footer') . '</div>';

    $privacy_url = function_exists('get_privacy_policy_url') ? get_privacy_policy_url() : '';
    if ($privacy_url) {
        echo '<div class="ifsc-container ifsc-footer-links"><a href="' . esc_url($privacy_url) . '">Privacy Policy</a></div>';
    }
});

/**
 * Meta description + Open Graph/Twitter tags for the front page. Real WP
 * pages get a canonical automatically from core's rel_canonical(), so only
 * the custom routes in Ifsc_Seo need to supply their own.
 */
add_action('wp_head', function () {
    if (!is_front_page()) {
        return;
    }
    $description = 'Search any Indian bank branch by IFSC code, bank name, or city to instantly find MICR codes, addresses, and NEFT/RTGS/IMPS/UPI availability.';
    $title = wp_get_document_title();
    $url = home_url('/');
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
    echo '<meta name="twitter:card" content="summary">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
}, 5);
