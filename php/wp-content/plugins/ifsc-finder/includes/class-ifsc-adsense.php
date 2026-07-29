<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Google AdSense (Auto Ads) integration. Stores the publisher client ID
 * (e.g. "ca-pub-8648292919962811") as a WP option, editable from the IFSC
 * Finder admin page, and injects the Auto Ads <script> site-wide via wp_head.
 *
 * Auto Ads places ads automatically wherever Google's algorithm decides —
 * not necessarily inside the AdSlot placeholder divs (ifsc_finder_ad_slot()).
 * Those placeholders remain as layout reservations / a visual placeholder
 * when no publisher ID is set. To pin ads to those exact positions instead,
 * create manual "display ad units" in AdSense and swap ifsc_finder_ad_slot()
 * for real <ins class="adsbygoogle"> markup using their slot IDs.
 *
 * Ads will not actually render until Google has verified/approved the live
 * public domain this site is served from — they won't show on localhost.
 */
class Ifsc_AdSense
{
    const OPTION_KEY = 'ifsc_finder_adsense_client_id';

    public static function init()
    {
        add_action('wp_head', [__CLASS__, 'print_verification_meta'], 1);
        add_action('wp_head', [__CLASS__, 'print_auto_ads_script'], 1);
        add_action('admin_post_ifsc_finder_save_adsense', [__CLASS__, 'handle_save']);
    }

    public static function get_client_id()
    {
        return trim((string) get_option(self::OPTION_KEY, ''));
    }

    /**
     * Google's site-ownership verification tag for AdSense — separate from the
     * Auto Ads script, both driven by the same client ID.
     */
    public static function print_verification_meta()
    {
        $client_id = self::get_client_id();
        if (!$client_id) {
            return;
        }
        printf(
            '<meta name="google-adsense-account" content="%s">' . "\n",
            esc_attr($client_id)
        );
    }

    public static function print_auto_ads_script()
    {
        $client_id = self::get_client_id();
        if (!$client_id) {
            return;
        }
        printf(
            '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=%s" crossorigin="anonymous"></script>' . "\n",
            esc_attr($client_id)
        );
    }

    public static function handle_save()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }
        check_admin_referer('ifsc_finder_save_adsense');

        $client_id = isset($_POST['ifsc_finder_adsense_client_id'])
            ? sanitize_text_field(wp_unslash($_POST['ifsc_finder_adsense_client_id']))
            : '';

        update_option(self::OPTION_KEY, $client_id);

        wp_safe_redirect(admin_url('admin.php?page=' . IFSC_FINDER_ADMIN_SLUG . '&adsense_saved=1'));
        exit;
    }
}
