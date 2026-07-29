<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * wp-admin dashboard page, mirrors client/src/app/admin/page.js — but gated by
 * WordPress's own current_user_can('manage_options') instead of the Node app's
 * shared-secret header hack. Deliberate improvement, not a literal port.
 */
class Ifsc_Admin_Page
{
    public static function init()
    {
        add_action('admin_menu', [__CLASS__, 'register_menu']);
        add_action('admin_post_ifsc_finder_refresh', [__CLASS__, 'handle_refresh']);
    }

    public static function register_menu()
    {
        add_menu_page(
            'IFSC Finder',
            'IFSC Finder',
            'manage_options',
            IFSC_FINDER_ADMIN_SLUG,
            [__CLASS__, 'render_page'],
            'dashicons-search'
        );
    }

    public static function handle_refresh()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }
        check_admin_referer('ifsc_finder_refresh');

        $mode = isset($_POST['mode']) && $_POST['mode'] === 'full' ? 'full' : 'sample';
        $limit = $mode === 'full' ? null : 500;

        if ($mode === 'full') {
            // A full import (~182k rows) runs comfortably within a couple of
            // minutes, but some shared-hosting PHP configs cap execution time
            // lower than that (and may forbid raising it) — this best-effort
            // call just gives it more headroom where the host allows it.
            if (function_exists('set_time_limit')) {
                @set_time_limit(300);
            }
            @ini_set('memory_limit', '256M');
        }

        $message = '';
        try {
            $summary = Ifsc_Importer::run(['limit' => $limit]);
            $message = sprintf(
                'refresh_ok&added=%d&modified=%d&removed=%d',
                $summary['addedCount'],
                $summary['modifiedCount'],
                $summary['removedCount']
            );
        } catch (Exception $e) {
            $message = 'refresh_error&msg=' . rawurlencode($e->getMessage());
        }

        wp_safe_redirect(admin_url('admin.php?page=' . IFSC_FINDER_ADMIN_SLUG . '&' . $message));
        exit;
    }

    public static function render_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized', 403);
        }

        $total = Ifsc_DB::total_branches();
        $last_run = Ifsc_DB::last_changelog_run();
        $recent = Ifsc_DB::recent_changelogs(10);
        ?>
        <div class="wrap">
            <h1>IFSC Finder</h1>

            <?php if (isset($_GET['refresh_ok'])) : ?>
                <div class="notice notice-success"><p>
                    Refresh complete: <?php echo (int) $_GET['added']; ?> added,
                    <?php echo (int) $_GET['modified']; ?> modified,
                    <?php echo (int) $_GET['removed']; ?> removed.
                </p></div>
            <?php elseif (isset($_GET['refresh_error'])) : ?>
                <div class="notice notice-error"><p>Error: <?php echo esc_html($_GET['msg'] ?? 'Unknown error'); ?></p></div>
            <?php endif; ?>

            <?php if (isset($_GET['adsense_saved'])) : ?>
                <div class="notice notice-success"><p>AdSense settings saved.</p></div>
            <?php endif; ?>

            <div style="display:flex; gap:16px; margin: 16px 0;">
                <div style="border:1px solid #dcdcde; border-radius:6px; padding:16px; min-width:180px;">
                    <p style="margin:0; color:#646970;">Total Branches</p>
                    <p style="margin:0; font-size:24px; font-weight:600;"><?php echo esc_html(number_format_i18n($total)); ?></p>
                </div>
                <div style="border:1px solid #dcdcde; border-radius:6px; padding:16px; min-width:220px;">
                    <p style="margin:0; color:#646970;">Last Refresh</p>
                    <p style="margin:0; font-size:14px; font-weight:600;">
                        <?php echo $last_run ? esc_html(mysql2date('n/j/Y, g:i:s A', $last_run['run_at'])) : 'Never'; ?>
                    </p>
                </div>
            </div>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block; margin-right:8px;">
                <input type="hidden" name="action" value="ifsc_finder_refresh">
                <input type="hidden" name="mode" value="sample">
                <?php wp_nonce_field('ifsc_finder_refresh'); ?>
                <button type="submit" class="button button-primary">Refresh Now (sample: 500 rows)</button>
            </form>

            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" style="display:inline-block;"
                onsubmit="return confirm('This imports the full dataset (~182,000 rows) in one request. On some shared-hosting PHP configs this can take a minute or two, or time out if execution-time limits are strict. Continue?');">
                <input type="hidden" name="action" value="ifsc_finder_refresh">
                <input type="hidden" name="mode" value="full">
                <?php wp_nonce_field('ifsc_finder_refresh'); ?>
                <button type="submit" class="button">Full Import (all records)</button>
            </form>
            <p class="description" style="margin-top:6px;">
                Use the sample import to verify everything works first. If the full import times out
                on this host, raise PHP's max execution time in cPanel's MultiPHP INI Editor (or ask
                HostingRaja support) and try again.
            </p>

            <h2 style="margin-top:24px;">Recent Change Log</h2>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th>Run At</th>
                        <th>Status</th>
                        <th>Added</th>
                        <th>Modified</th>
                        <th>Removed</th>
                        <th>Total After</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)) : ?>
                        <tr><td colspan="6">No refresh runs yet.</td></tr>
                    <?php else : ?>
                        <?php foreach ($recent as $log) : ?>
                            <tr>
                                <td><?php echo esc_html(mysql2date('n/j/Y, g:i:s A', $log['run_at'])); ?></td>
                                <td>
                                    <span style="color: <?php echo $log['status'] === 'success' ? '#00a32a' : '#d63638'; ?>; font-weight:600;">
                                        <?php echo esc_html($log['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo esc_html($log['added_count']); ?></td>
                                <td><?php echo esc_html($log['modified_count']); ?></td>
                                <td><?php echo esc_html($log['removed_count']); ?></td>
                                <td><?php echo esc_html($log['total_records_after']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>

            <h2 style="margin-top:24px;">AdSense</h2>
            <p class="description">
                Enter your AdSense client ID (from the site tag Google gave you, e.g.
                <code>ca-pub-8648292919962811</code>) to enable Auto Ads site-wide. Auto Ads
                places ads automatically wherever Google's algorithm decides — it doesn't
                necessarily use the reserved sidebar/footer/inline placeholder spots on this
                site. Ads won't render until Google has verified this domain in your AdSense
                account (they won't show on <code>localhost</code>).
            </p>
            <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                <input type="hidden" name="action" value="ifsc_finder_save_adsense">
                <?php wp_nonce_field('ifsc_finder_save_adsense'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="ifsc_finder_adsense_client_id">AdSense client ID</label></th>
                        <td>
                            <input
                                type="text"
                                id="ifsc_finder_adsense_client_id"
                                name="ifsc_finder_adsense_client_id"
                                value="<?php echo esc_attr(Ifsc_AdSense::get_client_id()); ?>"
                                placeholder="ca-pub-XXXXXXXXXXXXXXXX"
                                class="regular-text"
                            >
                        </td>
                    </tr>
                </table>
                <button type="submit" class="button button-primary">Save AdSense Settings</button>
            </form>
        </div>
        <?php
    }
}
