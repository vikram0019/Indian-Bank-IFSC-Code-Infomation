<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * [ifsc_ad_slot variant="sidebar|footer|inline"] — for use inside plain WP Page
 * content (e.g. the Home page), mirrors the ad placement client/src/app/page.js
 * has via <AdSlot variant="sidebar" />. The custom templates (ifsc/bank pages)
 * call ifsc_finder_ad_slot() directly instead of going through the shortcode.
 */
add_shortcode('ifsc_ad_slot', function ($atts) {
    $atts = shortcode_atts(['variant' => 'sidebar'], $atts);
    return ifsc_finder_ad_slot($atts['variant']);
});
