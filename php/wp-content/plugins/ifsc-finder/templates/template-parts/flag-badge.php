<?php
/**
 * Usage: ifsc_finder_flag_badge('NEFT', (bool) $branch['neft']);
 * Mirrors client/src/components/FlagBadge.js — pill badge with light/dark-aware colors.
 */
if (!defined('ABSPATH')) {
    exit;
}

function ifsc_finder_flag_badge($label, $active)
{
    $class = $active
        ? 'ifsc-badge ifsc-badge--active'
        : 'ifsc-badge ifsc-badge--inactive';
    echo '<span class="' . esc_attr($class) . '">' . esc_html($label) . '</span>';
}
