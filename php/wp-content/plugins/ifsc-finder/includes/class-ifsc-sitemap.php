<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Registers a WP_Sitemaps_Provider (WP 5.5+) so /wp-sitemap.xml auto-lists our
 * IFSC-detail-page sitemap, chunked the same way as client/src/app/sitemap.js
 * (CHUNK_SIZE = 5000). Bank/city pages are excluded, matching current behavior.
 */
class Ifsc_Sitemap
{
    const CHUNK_SIZE = 5000;

    public static function init()
    {
        add_action('init', [__CLASS__, 'register_provider']);
        add_filter('robots_txt', [__CLASS__, 'filter_robots'], 10, 1);
    }

    public static function register_provider()
    {
        if (!class_exists('WP_Sitemaps_Provider') || !function_exists('wp_register_sitemap_provider')) {
            return; // WP < 5.5
        }
        // Core's rewrite regex for single-level provider URLs is
        // ^wp-sitemap-([a-z]+?)-(\d+?)\.xml$ — letters only, no hyphens — so a
        // provider name containing a hyphen (e.g. "ifsc-branches") gets misparsed
        // as provider "ifsc" + subtype "branches". Must be a plain [a-z]+ name.
        wp_register_sitemap_provider('ifscbranches', new Ifsc_Sitemap_Provider());
    }

    public static function filter_robots($output)
    {
        $output .= "Disallow: /wp-admin/admin.php?page=" . IFSC_FINDER_ADMIN_SLUG . "\n";
        return $output;
    }
}

if (class_exists('WP_Sitemaps_Provider')) {
    class Ifsc_Sitemap_Provider extends WP_Sitemaps_Provider
    {
        public $name = 'ifscbranches';
        public $object_type = 'ifsc_branch';

        public function get_url_list($page_num, $object_subtype = '')
        {
            $offset = ($page_num - 1) * Ifsc_Sitemap::CHUNK_SIZE;
            $page = Ifsc_DB::sitemap_page($offset, Ifsc_Sitemap::CHUNK_SIZE);

            $url_list = [];
            foreach ($page['items'] as $item) {
                $url_list[] = [
                    'loc' => home_url('/ifsc/' . $item['ifsc']),
                    'changefreq' => 'monthly',
                ];
            }
            return $url_list;
        }

        public function get_max_num_pages($object_subtype = '')
        {
            $total = Ifsc_DB::total_branches();
            return (int) max(ceil($total / Ifsc_Sitemap::CHUNK_SIZE), 1);
        }
    }
}
