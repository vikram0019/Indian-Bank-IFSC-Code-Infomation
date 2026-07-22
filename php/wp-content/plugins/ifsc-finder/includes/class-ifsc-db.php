<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_DB
{
    public static function branches_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'ifsc_branches';
    }

    public static function changelog_table()
    {
        global $wpdb;
        return $wpdb->prefix . 'ifsc_changelog';
    }

    public static function get_by_ifsc($code)
    {
        global $wpdb;
        $table = self::branches_table();
        $code = strtoupper(trim($code));
        return $wpdb->get_row($wpdb->prepare("SELECT * FROM {$table} WHERE ifsc = %s", $code), ARRAY_A);
    }

    /**
     * Bank search matches the BANK name field, never bankcode: some IFSC codes carry
     * another bank's routing prefix (e.g. small banks' IMPS-only codes starting with
     * "HDFC" because they settle via HDFC's rails), so bankcode alone would incorrectly
     * pull in unrelated banks. See client/src/server search.controller.js in the Node app.
     */
    public static function search_by_bank_and_city($bank, $city, $page = 1, $limit = 20)
    {
        global $wpdb;
        $table = self::branches_table();
        $page = max((int) $page, 1);
        $limit = min(max((int) $limit, 1), 500);
        $offset = ($page - 1) * $limit;

        $bank_like = $wpdb->esc_like($bank) . '%';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE bank LIKE %s AND city = %s ORDER BY branch ASC LIMIT %d OFFSET %d",
            $bank_like,
            $city,
            $limit,
            $offset
        ), ARRAY_A);

        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE bank LIKE %s AND city = %s",
            $bank_like,
            $city
        ));

        return ['page' => $page, 'limit' => $limit, 'total' => $total, 'results' => $results];
    }

    public static function search_by_branch_name($name, $page = 1, $limit = 20)
    {
        global $wpdb;
        $table = self::branches_table();
        $page = max((int) $page, 1);
        $limit = min(max((int) $limit, 1), 100);
        $offset = ($page - 1) * $limit;

        $name_like = '%' . $wpdb->esc_like($name) . '%';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$table} WHERE branch LIKE %s ORDER BY branch ASC LIMIT %d OFFSET %d",
            $name_like,
            $limit,
            $offset
        ), ARRAY_A);

        $total = (int) $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$table} WHERE branch LIKE %s",
            $name_like
        ));

        return ['page' => $page, 'limit' => $limit, 'total' => $total, 'results' => $results];
    }

    public static function suggest($q, $type = null)
    {
        global $wpdb;
        $table = self::branches_table();
        $limit = 10;
        $results = [];

        if (!$type || $type === 'ifsc') {
            $prefix = strtoupper($wpdb->esc_like($q)) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT ifsc, bank, branch FROM {$table} WHERE ifsc LIKE %s LIMIT %d",
                $prefix,
                $limit
            ), ARRAY_A);
            foreach ($rows as $r) {
                $results[] = [
                    'type' => 'ifsc',
                    'label' => "{$r['ifsc']} — {$r['bank']}, {$r['branch']}",
                    'value' => $r['ifsc'],
                ];
            }
        }

        if (!$type || $type === 'bank') {
            $prefix = $wpdb->esc_like($q) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT DISTINCT bank FROM {$table} WHERE bank LIKE %s LIMIT %d",
                $prefix,
                $limit
            ), ARRAY_A);
            foreach ($rows as $r) {
                if (empty($r['bank'])) {
                    continue;
                }
                $results[] = ['type' => 'bank', 'label' => $r['bank'], 'value' => $r['bank']];
            }
        }

        if (!$type || $type === 'branch') {
            $prefix = $wpdb->esc_like($q) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT ifsc, bank, branch FROM {$table} WHERE branch LIKE %s LIMIT %d",
                $prefix,
                $limit
            ), ARRAY_A);
            foreach ($rows as $r) {
                $results[] = [
                    'type' => 'branch',
                    'label' => "{$r['branch']} ({$r['bank']})",
                    'value' => $r['ifsc'],
                ];
            }
        }

        if ($type === 'city') {
            $prefix = $wpdb->esc_like($q) . '%';
            $rows = $wpdb->get_results($wpdb->prepare(
                "SELECT DISTINCT city, state FROM {$table} WHERE city LIKE %s LIMIT %d",
                $prefix,
                $limit
            ), ARRAY_A);
            foreach ($rows as $r) {
                if (empty($r['city'])) {
                    continue;
                }
                $results[] = ['type' => 'city', 'label' => "{$r['city']}, {$r['state']}", 'value' => $r['city']];
            }
        }

        return array_slice($results, 0, $limit);
    }

    public static function cities_for_bank($bank)
    {
        global $wpdb;
        $table = self::branches_table();
        $prefix = $wpdb->esc_like($bank) . '%';

        $cities = $wpdb->get_results($wpdb->prepare(
            "SELECT city, state, COUNT(*) as count FROM {$table} WHERE bank LIKE %s GROUP BY city, state ORDER BY city ASC LIMIT 500",
            $prefix
        ), ARRAY_A);

        $canonical = $wpdb->get_var($wpdb->prepare(
            "SELECT bank FROM {$table} WHERE bank LIKE %s LIMIT 1",
            $prefix
        ));

        return ['bank' => $canonical ?: $bank, 'cities' => $cities];
    }

    public static function sitemap_page($cursor = 0, $limit = 5000)
    {
        global $wpdb;
        $table = self::branches_table();
        $cursor = max((int) $cursor, 0);
        $limit = min((int) $limit ?: 5000, 5000);

        $rows = $wpdb->get_results($wpdb->prepare(
            "SELECT ifsc, bankcode, city FROM {$table} ORDER BY ifsc ASC LIMIT %d OFFSET %d",
            $limit,
            $cursor
        ), ARRAY_A);

        $total = self::total_branches();
        $next_cursor = ($cursor + count($rows) < $total) ? $cursor + count($rows) : null;

        return ['total' => $total, 'nextCursor' => $next_cursor, 'items' => $rows];
    }

    public static function total_branches()
    {
        global $wpdb;
        $table = self::branches_table();
        return (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
    }

    public static function distinct_ifsc_codes()
    {
        global $wpdb;
        $table = self::branches_table();
        return $wpdb->get_col("SELECT ifsc FROM {$table}");
    }

    public static function last_changelog_run()
    {
        global $wpdb;
        $table = self::changelog_table();
        return $wpdb->get_row("SELECT * FROM {$table} ORDER BY run_at DESC LIMIT 1", ARRAY_A);
    }

    public static function recent_changelogs($limit = 10)
    {
        global $wpdb;
        $table = self::changelog_table();
        return $wpdb->get_results($wpdb->prepare("SELECT * FROM {$table} ORDER BY run_at DESC LIMIT %d", $limit), ARRAY_A);
    }
}
