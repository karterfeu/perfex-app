<?php

defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Influencers Marketing
Description: Module complet de gestion d'influenceurs marketing avec analytics avancés, gestion de campagnes, scoring et intégration native Perfex CRM
Version: 1.0.0
Requires at least: 2.9.0
Author: Perfex
Author URI: https://perfex.com
*/

define('INFLUENCERS_MARKETING_MODULE_NAME', 'influencers_marketing');
define('INFLUENCERS_MARKETING_VERSION', '1.0.0');

hooks()->add_action('admin_init', 'influencers_marketing_module_init_menu_items');
hooks()->add_action('app_admin_head', 'influencers_marketing_add_head_components');
hooks()->add_action('app_admin_footer', 'influencers_marketing_load_js');

/**
 * Register activation module hook
 */
register_activation_hook(INFLUENCERS_MARKETING_MODULE_NAME, 'influencers_marketing_module_activation_hook');

function influencers_marketing_module_activation_hook()
{
    $CI = &get_instance();
    require_once(__DIR__ . '/install.php');
}

/**
 * Register language files
 */
register_language_files(INFLUENCERS_MARKETING_MODULE_NAME, [INFLUENCERS_MARKETING_MODULE_NAME]);

/**
 * Initialize menu items
 */
function influencers_marketing_module_init_menu_items()
{
    $CI = &get_instance();

    // Check if user has permission
    if (has_permission('influencers_marketing', '', 'view')) {
        $CI->app_menu->add_sidebar_menu_item('influencers-marketing', [
            'name'     => _l('influencers_marketing'),
            'icon'     => 'fa fa-star',
            'position' => 15,
            'href'     => admin_url('influencers_marketing'),
        ]);

        // Dashboard submenu
        $CI->app_menu->add_sidebar_children_item('influencers-marketing', [
            'slug'     => 'influencers-marketing-dashboard',
            'name'     => _l('im_dashboard'),
            'icon'     => 'fa fa-tachometer',
            'href'     => admin_url('influencers_marketing/dashboard'),
            'position' => 1,
        ]);

        // Influencers list submenu
        $CI->app_menu->add_sidebar_children_item('influencers-marketing', [
            'slug'     => 'influencers-list',
            'name'     => _l('im_influencers'),
            'icon'     => 'fa fa-users',
            'href'     => admin_url('influencers_marketing/influencers'),
            'position' => 2,
        ]);

        // Campaigns submenu
        $CI->app_menu->add_sidebar_children_item('influencers-marketing', [
            'slug'     => 'campaigns-list',
            'name'     => _l('im_campaigns'),
            'icon'     => 'fa fa-bullhorn',
            'href'     => admin_url('influencers_marketing/campaigns'),
            'position' => 3,
        ]);

        // Analytics submenu
        $CI->app_menu->add_sidebar_children_item('influencers-marketing', [
            'slug'     => 'analytics',
            'name'     => _l('im_analytics'),
            'icon'     => 'fa fa-bar-chart',
            'href'     => admin_url('influencers_marketing/analytics'),
            'position' => 4,
        ]);

        // Settings submenu (admin only)
        if (is_admin()) {
            $CI->app_menu->add_sidebar_children_item('influencers-marketing', [
                'slug'     => 'influencers-settings',
                'name'     => _l('settings'),
                'icon'     => 'fa fa-cog',
                'href'     => admin_url('influencers_marketing/settings'),
                'position' => 5,
            ]);
        }
    }
}

/**
 * Add CSS and head components
 */
function influencers_marketing_add_head_components()
{
    $CI = &get_instance();
    $isRTL = (is_rtl(true) ? 'true' : 'false');

    echo '<link rel="stylesheet" type="text/css" href="' . module_dir_url(INFLUENCERS_MARKETING_MODULE_NAME, 'assets/css/influencers_marketing.css') . '?v=' . INFLUENCERS_MARKETING_VERSION . '">';
    echo '<link rel="stylesheet" type="text/css" href="' . module_dir_url(INFLUENCERS_MARKETING_MODULE_NAME, 'assets/css/dark-mode.css') . '?v=' . INFLUENCERS_MARKETING_VERSION . '">';

    // Chart.js for analytics
    echo '<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>';
}

/**
 * Load JavaScript files
 */
function influencers_marketing_load_js()
{
    $CI = &get_instance();

    echo '<script src="' . module_dir_url(INFLUENCERS_MARKETING_MODULE_NAME, 'assets/js/influencers_marketing.js') . '?v=' . INFLUENCERS_MARKETING_VERSION . '"></script>';
    echo '<script src="' . module_dir_url(INFLUENCERS_MARKETING_MODULE_NAME, 'assets/js/dark-mode.js') . '?v=' . INFLUENCERS_MARKETING_VERSION . '"></script>';
    echo '<script src="' . module_dir_url(INFLUENCERS_MARKETING_MODULE_NAME, 'assets/js/analytics.js') . '?v=' . INFLUENCERS_MARKETING_VERSION . '"></script>';
}

/**
 * Initialize permissions
 */
function influencers_marketing_permissions()
{
    $capabilities = [];

    $capabilities['capabilities'] = [
        'view'   => _l('permission_view'),
        'create' => _l('permission_create'),
        'edit'   => _l('permission_edit'),
        'delete' => _l('permission_delete'),
    ];

    return $capabilities;
}

/**
 * Get influencer score calculation
 */
function calculate_influencer_score($influencer_id)
{
    $CI = &get_instance();
    $CI->load->model('influencers_marketing/influencers_model');

    return $CI->influencers_model->calculate_influence_score($influencer_id);
}

/**
 * Get influencer engagement rate
 */
function get_engagement_rate($social_account_id)
{
    $CI = &get_instance();
    $CI->load->model('influencers_marketing/influencers_model');

    return $CI->influencers_model->calculate_engagement_rate($social_account_id);
}

/**
 * Check if influencer has fake followers
 */
function detect_fake_followers($social_account_id)
{
    $CI = &get_instance();
    $CI->load->model('influencers_marketing/influencers_model');

    return $CI->influencers_model->detect_fake_followers($social_account_id);
}

/**
 * Create invoice from campaign
 */
function create_invoice_from_campaign($campaign_id, $influencer_id)
{
    $CI = &get_instance();
    $CI->load->model('influencers_marketing/campaigns_model');

    return $CI->campaigns_model->create_invoice($campaign_id, $influencer_id);
}

/**
 * Get module version
 */
function influencers_marketing_get_version()
{
    return INFLUENCERS_MARKETING_VERSION;
}
