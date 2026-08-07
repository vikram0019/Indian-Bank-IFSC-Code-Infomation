<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Shared CSV import/upsert/diff logic, used by the WP-CLI command and the admin
 * "Refresh Now" REST action. Mirrors server/scripts/refreshData.js in the Node app.
 */
class Ifsc_Importer
{
    const BATCH_SIZE = 1000;

    public static function run($args = [])
    {
        global $wpdb;
        $start = microtime(true);
        $limit = isset($args['limit']) && $args['limit'] ? (int) $args['limit'] : null;
        $source = !empty($args['source']) ? $args['source'] : IFSC_FINDER_SOURCE_URL;

        $table = Ifsc_DB::branches_table();
        $changelog_table = Ifsc_DB::changelog_table();

        try {
            $before_codes = $limit ? null : array_flip(Ifsc_DB::distinct_ifsc_codes());

            require_once ABSPATH . 'wp-admin/includes/file.php';
            $tmp_file = download_url($source, 120);
            if (is_wp_error($tmp_file)) {
                throw new Exception('Failed to download IFSC source: ' . $tmp_file->get_error_message());
            }

            $handle = fopen($tmp_file, 'r');
            if (!$handle) {
                @unlink($tmp_file);
                throw new Exception('Failed to open downloaded CSV file');
            }

            $header = fgetcsv($handle);
            if (!$header) {
                fclose($handle);
                @unlink($tmp_file);
                throw new Exception('CSV file appears empty');
            }
            $header = array_map('trim', $header);

            $row_count = 0;
            $upserted_count = 0;
            $modified_count = 0;
            $batch = [];

            $flush = function () use (&$batch, &$upserted_count, &$modified_count, $wpdb, $table) {
                if (empty($batch)) {
                    return;
                }
                self::upsert_batch($batch, $wpdb, $table, $upserted_count, $modified_count);
                $batch = [];
            };

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) !== count($header)) {
                    continue;
                }
                $assoc = array_combine($header, $row);
                if (empty($assoc['IFSC'])) {
                    continue;
                }
                $batch[] = self::row_to_branch($assoc);
                $row_count++;

                if (count($batch) >= self::BATCH_SIZE) {
                    $flush();
                }
                if ($limit && $row_count >= $limit) {
                    break;
                }
            }
            $flush();
            fclose($handle);
            @unlink($tmp_file);

            $added = [];
            $removed = [];
            if (!$limit && $before_codes !== null) {
                $after_codes = Ifsc_DB::distinct_ifsc_codes();
                $after_set = array_flip($after_codes);
                $added = array_keys(array_diff_key($after_set, $before_codes));
                $removed = array_keys(array_diff_key($before_codes, $after_set));
                if (!empty($removed)) {
                    self::delete_codes($removed, $wpdb, $table);
                }
            }

            $total_records_after = Ifsc_DB::total_branches();
            $duration_ms = (int) round((microtime(true) - $start) * 1000);

            $wpdb->insert($changelog_table, [
                'run_at' => current_time('mysql'),
                'added_count' => $upserted_count,
                'removed_count' => count($removed),
                'modified_count' => $modified_count,
                'total_records_after' => $total_records_after,
                'source_url' => $source,
                'duration_ms' => $duration_ms,
                'status' => 'success',
                'added_codes' => !empty($added) ? wp_json_encode($added) : null,
                'removed_codes' => !empty($removed) ? wp_json_encode($removed) : null,
            ]);

            return [
                'rowsProcessed' => $row_count,
                'addedCount' => $upserted_count,
                'removedCount' => count($removed),
                'modifiedCount' => $modified_count,
                'totalRecordsAfter' => $total_records_after,
                'durationMs' => $duration_ms,
                'changeLogId' => $wpdb->insert_id,
            ];
        } catch (Exception $e) {
            $duration_ms = (int) round((microtime(true) - $start) * 1000);
            $wpdb->insert($changelog_table, [
                'run_at' => current_time('mysql'),
                'source_url' => $source,
                'duration_ms' => $duration_ms,
                'status' => 'failed',
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * The source CSV has no BANKCODE column; IFSC's first 4 characters are the bank
     * code by spec (mirrors server/scripts/refreshData.js rowToBranch()).
     */
    private static function row_to_branch($row)
    {
        $ifsc = strtoupper(trim($row['IFSC']));
        $to_bool = function ($val) {
            return in_array(strtolower(trim((string) $val)), ['true', '1', 'yes'], true) ? 1 : 0;
        };

        return [
            'ifsc' => $ifsc,
            'bank' => $row['BANK'] ?? '',
            'bankcode' => substr($ifsc, 0, 4),
            'branch' => $row['BRANCH'] ?? '',
            'centre' => $row['CENTRE'] ?? '',
            'district' => $row['DISTRICT'] ?? '',
            'state' => $row['STATE'] ?? '',
            'address' => $row['ADDRESS'] ?? '',
            'city' => $row['CITY'] ?? '',
            'contact' => $row['CONTACT'] ?? '',
            'imps' => $to_bool($row['IMPS'] ?? ''),
            'rtgs' => $to_bool($row['RTGS'] ?? ''),
            'neft' => $to_bool($row['NEFT'] ?? ''),
            'upi' => $to_bool($row['UPI'] ?? ''),
            'iso3166' => $row['ISO3166'] ?? '',
            'micr' => $row['MICR'] ?? '',
            'swift' => !empty($row['SWIFT']) ? $row['SWIFT'] : null,
        ];
    }

    private static function upsert_batch($batch, $wpdb, $table, &$upserted_count, &$modified_count)
    {
        $columns = ['ifsc', 'bank', 'bankcode', 'branch', 'centre', 'district', 'state', 'address', 'city', 'contact', 'imps', 'rtgs', 'neft', 'upi', 'iso3166', 'micr', 'swift'];

        $placeholders = [];
        $values = [];
        foreach ($batch as $row) {
            $placeholders[] = '(' . implode(',', array_fill(0, count($columns), '%s')) . ')';
            foreach ($columns as $col) {
                $values[] = $row[$col];
            }
        }

        $update_clause = implode(', ', array_map(function ($col) {
            return "{$col} = VALUES({$col})";
        }, array_diff($columns, ['ifsc'])));

        $sql = "INSERT INTO {$table} (" . implode(',', $columns) . ") VALUES " . implode(',', $placeholders)
            . " ON DUPLICATE KEY UPDATE {$update_clause}, updated_at = NOW()";

        $result = $wpdb->query($wpdb->prepare($sql, $values));

        if ($result === false) {
            throw new Exception('Batch insert failed: ' . $wpdb->last_error);
        }

        // $wpdb doesn't expose per-row inserted-vs-updated counts for a multi-row
        // upsert; approximate: rows_affected is 1 per insert, 2 per update in MySQL.
        $affected = (int) $wpdb->rows_affected;
        $batch_size = count($batch);
        $updated_in_batch = max(0, $affected - $batch_size);
        $inserted_in_batch = $batch_size - $updated_in_batch;
        $upserted_count += $inserted_in_batch;
        $modified_count += $updated_in_batch;
    }

    private static function delete_codes($codes, $wpdb, $table)
    {
        foreach (array_chunk($codes, 500) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '%s'));
            $wpdb->query($wpdb->prepare("DELETE FROM {$table} WHERE ifsc IN ({$placeholders})", $chunk));
        }
    }
}
