<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_Activator
{
    public static function activate()
    {
        self::create_tables();
        Ifsc_Rewrite::register_rules();
        Ifsc_Cron::schedule();
        flush_rewrite_rules();
    }

    private static function create_tables()
    {
        global $wpdb;
        require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        $charset_collate = $wpdb->get_charset_collate();
        $branches_table = Ifsc_DB::branches_table();
        $changelog_table = Ifsc_DB::changelog_table();

        $sql_branches = "CREATE TABLE {$branches_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            ifsc VARCHAR(11) NOT NULL,
            bank VARCHAR(255) NULL,
            bankcode VARCHAR(4) NULL,
            branch VARCHAR(255) NULL,
            centre VARCHAR(255) NULL,
            district VARCHAR(255) NULL,
            state VARCHAR(255) NULL,
            address TEXT NULL,
            city VARCHAR(255) NULL,
            contact VARCHAR(64) NULL,
            imps TINYINT(1) NOT NULL DEFAULT 0,
            rtgs TINYINT(1) NOT NULL DEFAULT 0,
            neft TINYINT(1) NOT NULL DEFAULT 0,
            upi TINYINT(1) NOT NULL DEFAULT 0,
            iso3166 VARCHAR(8) NULL,
            micr VARCHAR(9) NULL,
            swift VARCHAR(11) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            UNIQUE KEY uniq_ifsc (ifsc),
            KEY idx_bank (bank(191)),
            KEY idx_city (city(191)),
            KEY idx_branch (branch(191)),
            KEY idx_bank_city (bank(80), city(80))
        ) {$charset_collate};";

        $sql_changelog = "CREATE TABLE {$changelog_table} (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            run_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            added_count INT UNSIGNED NOT NULL DEFAULT 0,
            removed_count INT UNSIGNED NOT NULL DEFAULT 0,
            modified_count INT UNSIGNED NOT NULL DEFAULT 0,
            total_records_after INT UNSIGNED NOT NULL DEFAULT 0,
            source_url VARCHAR(512) NULL,
            duration_ms INT UNSIGNED NULL,
            status ENUM('success','failed') NOT NULL DEFAULT 'success',
            error TEXT NULL,
            added_codes LONGTEXT NULL,
            removed_codes LONGTEXT NULL,
            PRIMARY KEY (id),
            KEY idx_run_at (run_at)
        ) {$charset_collate};";

        dbDelta($sql_branches);
        dbDelta($sql_changelog);
    }
}
