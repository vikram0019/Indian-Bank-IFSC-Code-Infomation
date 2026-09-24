<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Google reCAPTCHA v2 ("I'm not a robot" checkbox) for the contact form.
 * Keys are stored as WP options (set from the IFSC Finder admin page) rather
 * than hardcoded, since this plugin's source lives in a public GitHub repo.
 */
class Ifsc_Recaptcha
{
    const SITE_KEY_OPTION = 'ifsc_finder_recaptcha_site_key';
    const SECRET_KEY_OPTION = 'ifsc_finder_recaptcha_secret_key';

    public static function init()
    {
        add_action('admin_post_ifsc_finder_save_recaptcha', [__CLASS__, 'handle_save']);
    }

    public static function get_site_key()
    {
        return trim((string) get_option(self::SITE_KEY_OPTION, ''));
    }

    public static function get_secret_key()
    {
        return trim((string) get_option(self::SECRET_KEY_OPTION, ''));
    }

    public static function handle_save()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }
        check_admin_referer('ifsc_finder_save_recaptcha');

        update_option(self::SITE_KEY_OPTION, isset($_POST['recaptcha_site_key'])
            ? sanitize_text_field(wp_unslash($_POST['recaptcha_site_key']))
            : '');
        update_option(self::SECRET_KEY_OPTION, isset($_POST['recaptcha_secret_key'])
            ? sanitize_text_field(wp_unslash($_POST['recaptcha_secret_key']))
            : '');

        wp_safe_redirect(admin_url('admin.php?page=' . IFSC_FINDER_ADMIN_SLUG . '&recaptcha_saved=1'));
        exit;
    }

    /**
     * Verifies a g-recaptcha-response token against Google's siteverify API.
     * Returns true if no secret key is configured yet, so the form still
     * works (protected only by the honeypot) before reCAPTCHA is set up.
     */
    public static function verify($response_token)
    {
        $secret = self::get_secret_key();
        if (!$secret) {
            return true;
        }
        if (!$response_token) {
            return false;
        }

        $result = wp_remote_post('https://www.google.com/recaptcha/api/siteverify', [
            'body' => [
                'secret' => $secret,
                'response' => $response_token,
                'remoteip' => isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field($_SERVER['REMOTE_ADDR']) : '',
            ],
            'timeout' => 10,
        ]);

        if (is_wp_error($result)) {
            return false;
        }

        $body = json_decode(wp_remote_retrieve_body($result), true);
        return !empty($body['success']);
    }
}
