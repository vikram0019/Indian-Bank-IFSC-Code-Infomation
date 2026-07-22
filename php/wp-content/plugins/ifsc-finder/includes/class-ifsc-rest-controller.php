<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_REST_Controller
{
    const NS = 'ifsc/v1';

    public static function init()
    {
        add_action('rest_api_init', [__CLASS__, 'register_routes']);
    }

    public static function register_routes()
    {
        register_rest_route(self::NS, '/ifsc/(?P<code>[A-Za-z0-9]+)', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_ifsc'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/search', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'search'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/branch/search', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'branch_search'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/suggest', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'suggest'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/bank-cities', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'bank_cities'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/sitemap-data', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'sitemap_data'],
            'permission_callback' => '__return_true',
        ]);

        register_rest_route(self::NS, '/admin/refresh', [
            'methods' => 'POST',
            'callback' => [__CLASS__, 'admin_refresh'],
            'permission_callback' => [__CLASS__, 'admin_permission'],
        ]);

        register_rest_route(self::NS, '/admin/stats', [
            'methods' => 'GET',
            'callback' => [__CLASS__, 'admin_stats'],
            'permission_callback' => [__CLASS__, 'admin_permission'],
        ]);
    }

    public static function admin_permission()
    {
        return current_user_can('manage_options');
    }

    public static function get_ifsc($request)
    {
        $code = $request->get_param('code');
        $branch = Ifsc_DB::get_by_ifsc($code);
        if (!$branch) {
            return new WP_REST_Response(['error' => "No branch found for IFSC code " . strtoupper($code)], 404);
        }
        return new WP_REST_Response($branch, 200);
    }

    public static function search($request)
    {
        $bank = $request->get_param('bank');
        $city = $request->get_param('city');
        if (!$bank || !$city) {
            return new WP_REST_Response(['error' => 'Both bank and city query params are required'], 400);
        }
        $page = (int) $request->get_param('page') ?: 1;
        $limit = (int) $request->get_param('limit') ?: 20;
        return new WP_REST_Response(Ifsc_DB::search_by_bank_and_city($bank, $city, $page, $limit), 200);
    }

    public static function branch_search($request)
    {
        $name = $request->get_param('name');
        if (!$name) {
            return new WP_REST_Response(['error' => 'name query param is required'], 400);
        }
        $page = (int) $request->get_param('page') ?: 1;
        $limit = (int) $request->get_param('limit') ?: 20;
        return new WP_REST_Response(Ifsc_DB::search_by_branch_name($name, $page, $limit), 200);
    }

    public static function suggest($request)
    {
        $q = trim((string) $request->get_param('q'));
        $type = $request->get_param('type');
        if (!$q) {
            return new WP_REST_Response([], 200);
        }
        return new WP_REST_Response(Ifsc_DB::suggest($q, $type), 200);
    }

    public static function bank_cities($request)
    {
        $bank = $request->get_param('bank');
        if (!$bank) {
            return new WP_REST_Response(['error' => 'bank query param is required'], 400);
        }
        return new WP_REST_Response(Ifsc_DB::cities_for_bank($bank), 200);
    }

    public static function sitemap_data($request)
    {
        $cursor = (int) $request->get_param('cursor') ?: 0;
        $limit = (int) $request->get_param('limit') ?: 5000;
        return new WP_REST_Response(Ifsc_DB::sitemap_page($cursor, $limit), 200);
    }

    public static function admin_refresh($request)
    {
        $body = $request->get_json_params();
        $limit = !empty($body['limit']) ? (int) $body['limit'] : null;
        try {
            $summary = Ifsc_Importer::run(['limit' => $limit]);
            return new WP_REST_Response($summary, 200);
        } catch (Exception $e) {
            return new WP_REST_Response(['error' => $e->getMessage()], 500);
        }
    }

    public static function admin_stats($request)
    {
        return new WP_REST_Response([
            'totalBranches' => Ifsc_DB::total_branches(),
            'lastRun' => Ifsc_DB::last_changelog_run(),
            'recentChangeLogs' => Ifsc_DB::recent_changelogs(10),
        ], 200);
    }
}
