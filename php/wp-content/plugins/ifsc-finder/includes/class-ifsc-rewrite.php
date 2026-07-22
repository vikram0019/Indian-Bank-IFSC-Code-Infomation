<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_Rewrite
{
    public static function init()
    {
        add_action('init', [__CLASS__, 'register_rules']);
        add_filter('query_vars', [__CLASS__, 'register_query_vars']);
        add_action('template_redirect', [__CLASS__, 'handle_template_redirect']);
    }

    public static function register_rules()
    {
        // Most-specific first: two-segment bank+city before single-segment bank overview.
        add_rewrite_rule(
            '^ifsc/([A-Za-z0-9]+)/?$',
            'index.php?ifsc_view=detail&ifsc_code=$matches[1]',
            'top'
        );
        add_rewrite_rule(
            '^bank/([^/]+)/([^/]+)/?$',
            'index.php?ifsc_view=bank_city&bank_slug=$matches[1]&city_slug=$matches[2]',
            'top'
        );
        add_rewrite_rule(
            '^bank/([^/]+)/?$',
            'index.php?ifsc_view=bank_overview&bank_slug=$matches[1]',
            'top'
        );
    }

    public static function register_query_vars($vars)
    {
        $vars[] = 'ifsc_view';
        $vars[] = 'ifsc_code';
        $vars[] = 'bank_slug';
        $vars[] = 'city_slug';
        return $vars;
    }

    public static function handle_template_redirect()
    {
        $view = get_query_var('ifsc_view');
        if (!$view) {
            return;
        }

        status_header(200);

        switch ($view) {
            case 'detail':
                include IFSC_FINDER_DIR . 'templates/template-ifsc-detail.php';
                break;
            case 'bank_overview':
                include IFSC_FINDER_DIR . 'templates/template-bank-overview.php';
                break;
            case 'bank_city':
                include IFSC_FINDER_DIR . 'templates/template-bank-city.php';
                break;
            default:
                return;
        }
        exit;
    }

    /**
     * Mirrors client/src/components/SearchTabs.js slugify(): lowercase, spaces -> dashes.
     */
    public static function slugify($value)
    {
        $value = trim($value);
        $value = strtolower($value);
        $value = preg_replace('/\s+/', '-', $value);
        return $value;
    }

    /**
     * Mirrors client's unslugify(): dashes -> spaces. Used as a LIKE/exact match value,
     * never treated as an exact bank name (bank_slug is matched with a prefix LIKE).
     */
    public static function unslugify($slug)
    {
        $slug = urldecode($slug);
        return str_replace('-', ' ', $slug);
    }
}
