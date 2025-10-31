<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Installation script for Influencers Marketing module
 * Enhanced with error handling and logging
 */

$CI = &get_instance();

// Create log file for debugging
$log_file = FCPATH . 'modules/influencers_marketing/install_log.txt';
$log = [];

function write_log($message, &$log) {
    $log[] = date('Y-m-d H:i:s') . ' - ' . $message;
}

try {
    write_log('Starting installation...', $log);

    // Table 1: Influencers
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_influencers')) {
            write_log('Creating table: im_influencers', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_influencers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `contact_id` int(11) DEFAULT NULL,
                `customer_id` int(11) DEFAULT NULL,
                `staff_id` int(11) DEFAULT NULL,
                `firstname` varchar(100) NOT NULL,
                `lastname` varchar(100) NOT NULL,
                `email` varchar(150) DEFAULT NULL,
                `phone` varchar(50) DEFAULT NULL,
                `profile_picture` varchar(255) DEFAULT NULL,
                `bio` text,
                `location` varchar(150) DEFAULT NULL,
                `country` varchar(100) DEFAULT NULL,
                `language` varchar(50) DEFAULT \'fr\',
                `category` varchar(100) DEFAULT NULL,
                `influence_score` decimal(5,2) DEFAULT 0.00,
                `status` varchar(50) DEFAULT \'prospect\',
                `rating` int(1) DEFAULT 0,
                `tags` text,
                `pricing_info` text,
                `notes` text,
                `is_verified` tinyint(1) DEFAULT 0,
                `is_favorite` tinyint(1) DEFAULT 0,
                `fake_followers_score` decimal(5,2) DEFAULT NULL,
                `audience_quality` varchar(50) DEFAULT \'unknown\',
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `datecreated` datetime DEFAULT NULL,
                `addedfrom` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `contact_id` (`contact_id`),
                KEY `customer_id` (`customer_id`),
                KEY `staff_id` (`staff_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_influencers created successfully', $log);
        } else {
            write_log('Table im_influencers already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_influencers: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 2: Social Accounts
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_social_accounts')) {
            write_log('Creating table: im_social_accounts', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_social_accounts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `influencer_id` int(11) NOT NULL,
                `platform` varchar(50) NOT NULL,
                `username` varchar(150) NOT NULL,
                `profile_url` varchar(255) DEFAULT NULL,
                `followers_count` bigint(20) DEFAULT 0,
                `following_count` bigint(20) DEFAULT 0,
                `posts_count` int(11) DEFAULT 0,
                `engagement_rate` decimal(5,2) DEFAULT 0.00,
                `avg_likes` int(11) DEFAULT 0,
                `avg_comments` int(11) DEFAULT 0,
                `avg_views` bigint(20) DEFAULT 0,
                `avg_shares` int(11) DEFAULT 0,
                `is_verified` tinyint(1) DEFAULT 0,
                `is_primary` tinyint(1) DEFAULT 0,
                `last_post_date` datetime DEFAULT NULL,
                `growth_rate_30d` decimal(5,2) DEFAULT 0.00,
                `audience_demographics` text,
                `best_posting_time` varchar(50) DEFAULT NULL,
                `top_hashtags` text,
                `last_sync` datetime DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_platform_username` (`influencer_id`, `platform`, `username`),
                KEY `platform` (`platform`),
                KEY `followers_count` (`followers_count`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_social_accounts created successfully', $log);
        } else {
            write_log('Table im_social_accounts already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_social_accounts: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 3: Metrics History
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_metrics_history')) {
            write_log('Creating table: im_metrics_history', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_metrics_history` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `social_account_id` int(11) NOT NULL,
                `followers_count` bigint(20) DEFAULT 0,
                `following_count` bigint(20) DEFAULT 0,
                `posts_count` int(11) DEFAULT 0,
                `engagement_rate` decimal(5,2) DEFAULT 0.00,
                `avg_likes` int(11) DEFAULT 0,
                `avg_comments` int(11) DEFAULT 0,
                `avg_views` bigint(20) DEFAULT 0,
                `recorded_date` date NOT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_account_date` (`social_account_id`, `recorded_date`),
                KEY `recorded_date` (`recorded_date`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_metrics_history created successfully', $log);
        } else {
            write_log('Table im_metrics_history already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_metrics_history: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 4: Campaigns
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_campaigns')) {
            write_log('Creating table: im_campaigns', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaigns` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(200) NOT NULL,
                `description` text,
                `project_id` int(11) DEFAULT NULL,
                `customer_id` int(11) DEFAULT NULL,
                `status` varchar(50) DEFAULT \'draft\',
                `budget` decimal(15,2) DEFAULT 0.00,
                `actual_cost` decimal(15,2) DEFAULT 0.00,
                `currency` varchar(10) DEFAULT \'EUR\',
                `start_date` date DEFAULT NULL,
                `end_date` date DEFAULT NULL,
                `goals` text,
                `target_audience` text,
                `brief` text,
                `calendar_events` text,
                `roi_tracking` text,
                `total_impressions` bigint(20) DEFAULT 0,
                `total_clicks` int(11) DEFAULT 0,
                `total_conversions` int(11) DEFAULT 0,
                `revenue_generated` decimal(15,2) DEFAULT 0.00,
                `created_by` int(11) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `project_id` (`project_id`),
                KEY `customer_id` (`customer_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_campaigns created successfully', $log);
        } else {
            write_log('Table im_campaigns already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_campaigns: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 5: Campaign Influencers
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_campaign_influencers')) {
            write_log('Creating table: im_campaign_influencers', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaign_influencers` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `campaign_id` int(11) NOT NULL,
                `influencer_id` int(11) NOT NULL,
                `status` varchar(50) DEFAULT \'invited\',
                `compensation_type` varchar(50) DEFAULT \'paid\',
                `compensation_amount` decimal(15,2) DEFAULT 0.00,
                `currency` varchar(10) DEFAULT \'EUR\',
                `contract_signed` tinyint(1) DEFAULT 0,
                `contract_file` varchar(255) DEFAULT NULL,
                `performance_metrics` text,
                `notes` text,
                `invoice_id` int(11) DEFAULT NULL,
                `quote_id` int(11) DEFAULT NULL,
                `added_at` datetime DEFAULT NULL,
                `accepted_at` datetime DEFAULT NULL,
                `completed_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_campaign_influencer` (`campaign_id`, `influencer_id`),
                KEY `invoice_id` (`invoice_id`),
                KEY `quote_id` (`quote_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_campaign_influencers created successfully', $log);
        } else {
            write_log('Table im_campaign_influencers already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_campaign_influencers: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 6: Deliverables
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_deliverables')) {
            write_log('Creating table: im_deliverables', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_deliverables` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `campaign_id` int(11) NOT NULL,
                `influencer_id` int(11) NOT NULL,
                `task_id` int(11) DEFAULT NULL,
                `title` varchar(200) NOT NULL,
                `description` text,
                `type` varchar(50) DEFAULT \'post\',
                `platform` varchar(50) DEFAULT NULL,
                `due_date` date DEFAULT NULL,
                `status` varchar(50) DEFAULT \'pending\',
                `content_url` varchar(255) DEFAULT NULL,
                `preview_file` varchar(255) DEFAULT NULL,
                `metrics` text,
                `feedback` text,
                `submitted_at` datetime DEFAULT NULL,
                `approved_at` datetime DEFAULT NULL,
                `published_at` datetime DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `influencer_id` (`influencer_id`),
                KEY `task_id` (`task_id`),
                KEY `status` (`status`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_deliverables created successfully', $log);
        } else {
            write_log('Table im_deliverables already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_deliverables: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 7: Interactions
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_interactions')) {
            write_log('Creating table: im_interactions', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_interactions` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `influencer_id` int(11) NOT NULL,
                `staff_id` int(11) DEFAULT NULL,
                `type` varchar(50) NOT NULL,
                `subject` varchar(200) DEFAULT NULL,
                `description` text,
                `contact_date` datetime NOT NULL,
                `next_followup` date DEFAULT NULL,
                `status` varchar(50) DEFAULT \'completed\',
                `attachments` text,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `influencer_id` (`influencer_id`),
                KEY `staff_id` (`staff_id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_interactions created successfully', $log);
        } else {
            write_log('Table im_interactions already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_interactions: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 8: Tags
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_tags')) {
            write_log('Creating table: im_tags', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_tags` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `color` varchar(20) DEFAULT \'#3498db\',
                `description` varchar(255) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_tags created successfully', $log);
        } else {
            write_log('Table im_tags already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_tags: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 9: Influencer Tags
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_influencer_tags')) {
            write_log('Creating table: im_influencer_tags', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_influencer_tags` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `influencer_id` int(11) NOT NULL,
                `tag_id` int(11) NOT NULL,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                UNIQUE KEY `unique_influencer_tag` (`influencer_id`, `tag_id`),
                KEY `tag_id` (`tag_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_influencer_tags created successfully', $log);
        } else {
            write_log('Table im_influencer_tags already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_influencer_tags: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 10: Content Library
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_content_library')) {
            write_log('Creating table: im_content_library', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_content_library` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `influencer_id` int(11) NOT NULL,
                `campaign_id` int(11) DEFAULT NULL,
                `deliverable_id` int(11) DEFAULT NULL,
                `title` varchar(200) NOT NULL,
                `description` text,
                `type` varchar(50) DEFAULT \'image\',
                `platform` varchar(50) DEFAULT NULL,
                `file_path` varchar(255) DEFAULT NULL,
                `thumbnail` varchar(255) DEFAULT NULL,
                `url` varchar(255) DEFAULT NULL,
                `performance_metrics` text,
                `is_reusable` tinyint(1) DEFAULT 1,
                `tags` text,
                `created_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `influencer_id` (`influencer_id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `type` (`type`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_content_library created successfully', $log);
        } else {
            write_log('Table im_content_library already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_content_library: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 11: Email Templates
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_email_templates')) {
            write_log('Creating table: im_email_templates', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_email_templates` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(200) NOT NULL,
                `subject` varchar(255) NOT NULL,
                `body` text NOT NULL,
                `type` varchar(50) DEFAULT \'outreach\',
                `variables` text,
                `is_active` tinyint(1) DEFAULT 1,
                `created_by` int(11) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_email_templates created successfully', $log);
        } else {
            write_log('Table im_email_templates already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_email_templates: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 12: Settings
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_settings')) {
            write_log('Creating table: im_settings', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_settings` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(100) NOT NULL,
                `value` text,
                `autoload` tinyint(1) DEFAULT 1,
                PRIMARY KEY (`id`),
                UNIQUE KEY `name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_settings created successfully', $log);
        } else {
            write_log('Table im_settings already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_settings: ' . $e->getMessage(), $log);
        throw $e;
    }

    // Table 13: Campaign Contents
    try {
        if (!$CI->db->table_exists(db_prefix() . 'im_campaign_contents')) {
            write_log('Creating table: im_campaign_contents', $log);
            $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaign_contents` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `campaign_id` int(11) NOT NULL,
                `campaign_influencer_id` int(11) DEFAULT NULL,
                `influencer_id` int(11) NOT NULL,
                `platform` varchar(50) NOT NULL,
                `content_type` varchar(50) DEFAULT \'post\',
                `content_url` varchar(500) NOT NULL,
                `title` varchar(255) DEFAULT NULL,
                `description` text,
                `posted_at` datetime DEFAULT NULL,
                `views_count` bigint(20) DEFAULT NULL,
                `likes_count` int(11) DEFAULT NULL,
                `comments_count` int(11) DEFAULT NULL,
                `shares_count` int(11) DEFAULT NULL,
                `saves_count` int(11) DEFAULT NULL,
                `clicks_count` int(11) DEFAULT NULL,
                `engagement_rate` decimal(5,2) DEFAULT NULL,
                `reach` bigint(20) DEFAULT NULL,
                `impressions` bigint(20) DEFAULT NULL,
                `last_metrics_sync` datetime DEFAULT NULL,
                `thumbnail_url` varchar(500) DEFAULT NULL,
                `is_sponsored` tinyint(1) DEFAULT NULL,
                `created_at` datetime DEFAULT NULL,
                `updated_at` datetime DEFAULT NULL,
                `created_by` int(11) DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `campaign_id` (`campaign_id`),
                KEY `campaign_influencer_id` (`campaign_influencer_id`),
                KEY `influencer_id` (`influencer_id`),
                KEY `platform` (`platform`),
                KEY `posted_at` (`posted_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
            write_log('✓ Table im_campaign_contents created successfully', $log);
        } else {
            write_log('Table im_campaign_contents already exists', $log);
        }
    } catch (Exception $e) {
        write_log('ERROR creating im_campaign_contents: ' . $e->getMessage(), $log);
        throw $e;
    }

    write_log('All tables created/checked successfully', $log);

    // Insert default settings
    try {
        write_log('Inserting default settings...', $log);
        $default_settings = [
            ['name' => 'im_default_currency', 'value' => 'EUR', 'autoload' => 1],
            ['name' => 'im_scoring_algorithm_version', 'value' => '1.0', 'autoload' => 1],
            ['name' => 'im_auto_sync_metrics', 'value' => '1', 'autoload' => 1],
            ['name' => 'im_sync_frequency_hours', 'value' => '24', 'autoload' => 1],
            ['name' => 'im_fake_followers_threshold', 'value' => '30', 'autoload' => 1],
            ['name' => 'im_enable_dark_mode', 'value' => '0', 'autoload' => 1],
            ['name' => 'im_default_view', 'value' => 'grid', 'autoload' => 1],
            ['name' => 'im_email_tracking', 'value' => '1', 'autoload' => 1],
        ];

        foreach ($default_settings as $setting) {
            $exists = $CI->db->get_where(db_prefix() . 'im_settings', ['name' => $setting['name']])->row();
            if (!$exists) {
                $CI->db->insert(db_prefix() . 'im_settings', $setting);
            }
        }
        write_log('✓ Default settings inserted', $log);
    } catch (Exception $e) {
        write_log('ERROR inserting default settings: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    // Insert default email templates
    try {
        write_log('Inserting default email templates...', $log);
        $default_templates = [
            [
                'name' => 'Premier contact - Influenceur',
                'subject' => 'Collaboration avec {{company_name}}',
                'body' => "Bonjour {{influencer_firstname}},\n\nNous avons découvert votre contenu sur {{platform}} et sommes impressionnés par votre engagement avec votre communauté.\n\nNous aimerions discuter d'une potentielle collaboration pour notre marque {{company_name}}.\n\nSeriez-vous disponible pour un échange cette semaine ?\n\nCordialement,\n{{staff_firstname}} {{staff_lastname}}\n{{company_name}}",
                'type' => 'outreach',
                'variables' => '["influencer_firstname","influencer_lastname","platform","company_name","staff_firstname","staff_lastname"]',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'name' => 'Relance - Sans réponse',
                'subject' => 'Re: Collaboration avec {{company_name}}',
                'body' => "Bonjour {{influencer_firstname}},\n\nJe me permets de vous relancer concernant ma proposition de collaboration.\n\nNous serions ravis de travailler avec vous et pensons que notre marque correspond parfaitement à votre univers.\n\nAvez-vous eu le temps de consulter ma proposition ?\n\nBien cordialement,\n{{staff_firstname}}",
                'type' => 'followup',
                'variables' => '["influencer_firstname","company_name","staff_firstname"]',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ];

        foreach ($default_templates as $template) {
            $exists = $CI->db->get_where(db_prefix() . 'im_email_templates', ['name' => $template['name']])->row();
            if (!$exists) {
                $CI->db->insert(db_prefix() . 'im_email_templates', $template);
            }
        }
        write_log('✓ Default email templates inserted', $log);
    } catch (Exception $e) {
        write_log('ERROR inserting default email templates: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    // Insert default tags
    try {
        write_log('Inserting default tags...', $log);
        $default_tags = [
            ['name' => 'Beauté', 'color' => '#e74c3c', 'description' => 'Influenceurs beauté et cosmétiques', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Tech', 'color' => '#3498db', 'description' => 'Influenceurs technologie', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Lifestyle', 'color' => '#9b59b6', 'description' => 'Influenceurs lifestyle', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Sport', 'color' => '#2ecc71', 'description' => 'Influenceurs sport et fitness', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Food', 'color' => '#f39c12', 'description' => 'Influenceurs cuisine et gastronomie', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Travel', 'color' => '#1abc9c', 'description' => 'Influenceurs voyage', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Fashion', 'color' => '#e91e63', 'description' => 'Influenceurs mode', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Gaming', 'color' => '#9c27b0', 'description' => 'Influenceurs gaming', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'B2B', 'color' => '#34495e', 'description' => 'Influenceurs B2B', 'created_at' => date('Y-m-d H:i:s')],
            ['name' => 'Végan', 'color' => '#27ae60', 'description' => 'Influenceurs végan', 'created_at' => date('Y-m-d H:i:s')],
        ];

        foreach ($default_tags as $tag) {
            $exists = $CI->db->get_where(db_prefix() . 'im_tags', ['name' => $tag['name']])->row();
            if (!$exists) {
                $CI->db->insert(db_prefix() . 'im_tags', $tag);
            }
        }
        write_log('✓ Default tags inserted', $log);
    } catch (Exception $e) {
        write_log('ERROR inserting default tags: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    // Add permissions
    try {
        write_log('Adding permissions...', $log);
        $capabilities = ['view', 'create', 'edit', 'delete'];

        foreach ($capabilities as $capability) {
            if (function_exists('add_permission')) {
                add_permission('influencers_marketing', $capability);
            }
        }
        write_log('✓ Permissions added', $log);
    } catch (Exception $e) {
        write_log('ERROR adding permissions: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    // Enable view permission by default for all staff roles
    try {
        write_log('Enabling view permission for all staff roles...', $log);
        $CI->db->select('roleid');
        $roles = $CI->db->get(db_prefix() . 'roles')->result_array();

        foreach ($roles as $role) {
            $CI->db->where('permissionid', 'influencers_marketing');
            $CI->db->where('roleid', $role['roleid']);
            $exists = $CI->db->get(db_prefix() . 'staff_permissions')->row();

            if (!$exists) {
                $CI->db->insert(db_prefix() . 'staff_permissions', [
                    'permissionid' => 'influencers_marketing',
                    'roleid' => $role['roleid'],
                    'view' => 1,
                    'view_own' => 1,
                    'create' => 0,
                    'edit' => 0,
                    'delete' => 0,
                ]);
            }
        }
        write_log('✓ View permissions enabled for all staff roles', $log);
    } catch (Exception $e) {
        write_log('ERROR enabling view permissions: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    // Create default folder for uploads
    try {
        write_log('Creating upload directories...', $log);
        $upload_path = FCPATH . 'uploads/influencers_marketing';
        if (!file_exists($upload_path)) {
            @mkdir($upload_path, 0755, true);
            @mkdir($upload_path . '/profiles', 0755, true);
            @mkdir($upload_path . '/content', 0755, true);
            @mkdir($upload_path . '/contracts', 0755, true);
            @mkdir($upload_path . '/deliverables', 0755, true);
        }
        write_log('✓ Upload directories created', $log);
    } catch (Exception $e) {
        write_log('ERROR creating upload directories: ' . $e->getMessage(), $log);
        // Continue anyway
    }

    write_log('Installation completed successfully!', $log);

} catch (Exception $e) {
    write_log('CRITICAL ERROR: ' . $e->getMessage(), $log);
    write_log('Stack trace: ' . $e->getTraceAsString(), $log);
} finally {
    // Write log to file
    @file_put_contents($log_file, implode("\n", $log));
}
