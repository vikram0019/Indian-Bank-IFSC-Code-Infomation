<?php
if (!defined('ABSPATH')) {
    exit;
}

class Ifsc_JsonLd
{
    public static function init()
    {
        // Templates call render_* directly and echo into wp_head-equivalent spots
        // themselves (they run before get_header()), so no wp_head hook is needed here.
    }

    public static function bank_or_credit_union($branch)
    {
        $site_url = home_url();
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'BankOrCreditUnion',
            'name' => !empty($branch['bank']) ? $branch['bank'] : $branch['bankcode'],
            'branchCode' => $branch['ifsc'],
            'url' => $site_url . '/ifsc/' . $branch['ifsc'],
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => $branch['address'],
                'addressLocality' => $branch['city'],
                'addressRegion' => $branch['state'],
                'addressCountry' => 'IN',
            ],
        ];
        if (!empty($branch['contact'])) {
            $data['telephone'] = $branch['contact'];
        }
        return $data;
    }

    public static function faq_page($faqs)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(function ($faq) {
                return [
                    '@type' => 'Question',
                    'name' => $faq['question'],
                    'acceptedAnswer' => [
                        '@type' => 'Answer',
                        'text' => $faq['answer'],
                    ],
                ];
            }, $faqs),
        ];
    }

    public static function render_script($data)
    {
        echo '<script type="application/ld+json">' . wp_json_encode($data) . '</script>';
    }
}
