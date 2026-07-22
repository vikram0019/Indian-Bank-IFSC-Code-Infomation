<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * wp ifsc import [--limit=<n>] [--source=<url>]
 */
class Ifsc_CLI_Command
{
    public function import($args, $assoc_args)
    {
        $limit = isset($assoc_args['limit']) ? (int) $assoc_args['limit'] : null;
        $source = isset($assoc_args['source']) ? $assoc_args['source'] : null;

        WP_CLI::log('Starting IFSC refresh' . ($limit ? " (limit: {$limit})" : '') . '...');

        try {
            $summary = Ifsc_Importer::run(['limit' => $limit, 'source' => $source]);
            WP_CLI::success('Refresh complete: ' . wp_json_encode($summary));
        } catch (Exception $e) {
            WP_CLI::error('Refresh failed: ' . $e->getMessage());
        }
    }
}

WP_CLI::add_command('ifsc', 'Ifsc_CLI_Command');
