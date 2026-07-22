<?php
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Scheduled data refresh, mirrors server/src/cron/refreshScheduler.js's ~15-day
 * node-cron job. WP-Cron is request-triggered (fires on page load), not wall-clock
 * precise — same caveat the Node app notes about node-cron vs. real system cron;
 * for production, disable WP-Cron's pseudo-cron and drive `wp cron event run` from
 * a real system crontab instead.
 */
class Ifsc_Cron
{
    const HOOK = 'ifsc_scheduled_refresh';

    public static function init()
    {
        add_filter('cron_schedules', [__CLASS__, 'add_schedule']);
        add_action(self::HOOK, [__CLASS__, 'run']);
    }

    public static function add_schedule($schedules)
    {
        $schedules['every_fifteen_days'] = [
            'interval' => 15 * DAY_IN_SECONDS,
            'display' => 'Every 15 days',
        ];
        return $schedules;
    }

    public static function schedule()
    {
        if (!wp_next_scheduled(self::HOOK)) {
            wp_schedule_event(time(), 'every_fifteen_days', self::HOOK);
        }
    }

    public static function run()
    {
        try {
            Ifsc_Importer::run();
        } catch (Exception $e) {
            error_log('IFSC scheduled refresh failed: ' . $e->getMessage());
        }
    }
}
