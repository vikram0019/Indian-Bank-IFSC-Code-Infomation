<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_Deactivator
{
    public static function deactivate()
    {
        // Never drop tables on deactivation — data is preserved for reactivation.
        wp_clear_scheduled_hook('ifsc_scheduled_refresh');
        flush_rewrite_rules();
    }
}
