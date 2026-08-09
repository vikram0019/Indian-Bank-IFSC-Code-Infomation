<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared head-tag output for the plugin's custom routes (/ifsc/{code},
 * /bank/{slug}, /bank/{slug}/{city}), which bypass WordPress's normal
 * singular-post query — so core's own canonical/OG output (rel_canonical(),
 * etc.) never fires for them and each template must supply its own.
 */
class Ifsc_Seo
{
    public static function head_tags($args)
    {
        $title = $args['title'];
        $description = $args['description'];
        $url = $args['url'];
        $noindex = !empty($args['noindex']);

        add_action('wp_head', function () use ($title, $description, $url, $noindex) {
            echo '<link rel="canonical" href="' . esc_url($url) . '">' . "\n";
            echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
            if ($noindex) {
                echo '<meta name="robots" content="noindex,follow">' . "\n";
            }
            echo '<meta property="og:type" content="website">' . "\n";
            echo '<meta property="og:site_name" content="' . esc_attr(get_bloginfo('name')) . '">' . "\n";
            echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
            echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
            echo '<meta property="og:url" content="' . esc_url($url) . '">' . "\n";
            echo '<meta name="twitter:card" content="summary">' . "\n";
            echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
            echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
        });
    }
}
