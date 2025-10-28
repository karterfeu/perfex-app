<?php

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Installation script for Influencers Marketing module
 */

$CI = &get_instance();

if (!$CI->db->table_exists(db_prefix() . 'im_influencers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_influencers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `contact_id` int(11) DEFAULT NULL COMMENT "Lien vers tbl_contacts",
        `customer_id` int(11) DEFAULT NULL COMMENT "Lien vers tbl_customers si converti en client",
        `staff_id` int(11) DEFAULT NULL COMMENT "Commercial assigné",
        `firstname` varchar(100) NOT NULL,
        `lastname` varchar(100) NOT NULL,
        `email` varchar(150) DEFAULT NULL,
        `phone` varchar(50) DEFAULT NULL,
        `profile_picture` varchar(255) DEFAULT NULL,
        `bio` text DEFAULT NULL,
        `location` varchar(150) DEFAULT NULL,
        `country` varchar(100) DEFAULT NULL,
        `language` varchar(50) DEFAULT "fr",
        `category` varchar(100) DEFAULT NULL COMMENT "Niche (beauté, tech, lifestyle...)",
        `influence_score` decimal(5,2) DEFAULT 0.00 COMMENT "Score propriétaire 0-100",
        `status` varchar(50) DEFAULT "prospect" COMMENT "prospect, contacted, negotiating, active, inactive",
        `rating` int(1) DEFAULT 0 COMMENT "Note 1-5 étoiles",
        `tags` text DEFAULT NULL COMMENT "Tags séparés par virgules",
        `pricing_info` text DEFAULT NULL COMMENT "Informations tarifaires JSON",
        `notes` text DEFAULT NULL,
        `is_verified` tinyint(1) DEFAULT 0,
        `is_favorite` tinyint(1) DEFAULT 0,
        `fake_followers_score` decimal(5,2) DEFAULT NULL COMMENT "Score de détection de faux followers",
        `audience_quality` varchar(50) DEFAULT "unknown" COMMENT "excellent, good, average, poor",
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        `datecreated` datetime DEFAULT NULL,
        `addedfrom` int(11) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `contact_id` (`contact_id`),
        KEY `customer_id` (`customer_id`),
        KEY `staff_id` (`staff_id`),
        KEY `status` (`status`),
        KEY `influence_score` (`influence_score`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_social_accounts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_social_accounts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `influencer_id` int(11) NOT NULL,
        `platform` varchar(50) NOT NULL COMMENT "instagram, youtube, tiktok, facebook, twitter, linkedin, twitch, snapchat",
        `username` varchar(150) NOT NULL,
        `profile_url` varchar(255) DEFAULT NULL,
        `followers_count` bigint(20) DEFAULT 0,
        `following_count` bigint(20) DEFAULT 0,
        `posts_count` int(11) DEFAULT 0,
        `engagement_rate` decimal(5,2) DEFAULT 0.00 COMMENT "Taux engagement en %",
        `avg_likes` int(11) DEFAULT 0,
        `avg_comments` int(11) DEFAULT 0,
        `avg_views` bigint(20) DEFAULT 0 COMMENT "Pour vidéos",
        `avg_shares` int(11) DEFAULT 0,
        `is_verified` tinyint(1) DEFAULT 0,
        `is_primary` tinyint(1) DEFAULT 0 COMMENT "Compte principal",
        `last_post_date` datetime DEFAULT NULL,
        `growth_rate_30d` decimal(5,2) DEFAULT 0.00 COMMENT "Croissance sur 30 jours en %",
        `audience_demographics` text DEFAULT NULL COMMENT "JSON avec âge, genre, localisation",
        `best_posting_time` varchar(50) DEFAULT NULL,
        `top_hashtags` text DEFAULT NULL COMMENT "Hashtags les plus utilisés",
        `last_sync` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_platform_username` (`influencer_id`, `platform`, `username`),
        KEY `platform` (`platform`),
        KEY `followers_count` (`followers_count`),
        KEY `engagement_rate` (`engagement_rate`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_metrics_history')) {
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
}

if (!$CI->db->table_exists(db_prefix() . 'im_campaigns')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaigns` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `description` text DEFAULT NULL,
        `project_id` int(11) DEFAULT NULL COMMENT "Lien optionnel vers tbl_projects",
        `customer_id` int(11) DEFAULT NULL COMMENT "Client principal de la campagne",
        `status` varchar(50) DEFAULT "draft" COMMENT "draft, prospect, negotiation, active, completed, cancelled",
        `budget` decimal(15,2) DEFAULT 0.00,
        `actual_cost` decimal(15,2) DEFAULT 0.00,
        `currency` varchar(10) DEFAULT "EUR",
        `start_date` date DEFAULT NULL,
        `end_date` date DEFAULT NULL,
        `goals` text DEFAULT NULL COMMENT "Objectifs de la campagne JSON",
        `target_audience` text DEFAULT NULL COMMENT "Audience cible JSON",
        `brief` text DEFAULT NULL COMMENT "Brief de la campagne",
        `calendar_events` text DEFAULT NULL COMMENT "Calendrier éditorial JSON",
        `roi_tracking` text DEFAULT NULL COMMENT "Suivi ROI JSON (impressions, clics, conversions)",
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
        KEY `status` (`status`),
        KEY `start_date` (`start_date`),
        KEY `end_date` (`end_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_campaign_influencers')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaign_influencers` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `campaign_id` int(11) NOT NULL,
        `influencer_id` int(11) NOT NULL,
        `status` varchar(50) DEFAULT "invited" COMMENT "invited, accepted, rejected, completed",
        `compensation_type` varchar(50) DEFAULT "paid" COMMENT "paid, barter, affiliate, sponsored",
        `compensation_amount` decimal(15,2) DEFAULT 0.00,
        `currency` varchar(10) DEFAULT "EUR",
        `contract_signed` tinyint(1) DEFAULT 0,
        `contract_file` varchar(255) DEFAULT NULL,
        `performance_metrics` text DEFAULT NULL COMMENT "Métriques de performance JSON",
        `notes` text DEFAULT NULL,
        `invoice_id` int(11) DEFAULT NULL COMMENT "Lien vers tbl_invoices",
        `quote_id` int(11) DEFAULT NULL COMMENT "Lien vers tbl_estimates",
        `added_at` datetime DEFAULT NULL,
        `accepted_at` datetime DEFAULT NULL,
        `completed_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_campaign_influencer` (`campaign_id`, `influencer_id`),
        KEY `invoice_id` (`invoice_id`),
        KEY `quote_id` (`quote_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_deliverables')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_deliverables` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `campaign_id` int(11) NOT NULL,
        `influencer_id` int(11) NOT NULL,
        `task_id` int(11) DEFAULT NULL COMMENT "Lien vers tbl_tasks",
        `title` varchar(200) NOT NULL,
        `description` text DEFAULT NULL,
        `type` varchar(50) DEFAULT "post" COMMENT "post, story, video, reel, article, etc",
        `platform` varchar(50) DEFAULT NULL,
        `due_date` date DEFAULT NULL,
        `status` varchar(50) DEFAULT "pending" COMMENT "pending, in_review, approved, published, rejected",
        `content_url` varchar(255) DEFAULT NULL COMMENT "URL du contenu publié",
        `preview_file` varchar(255) DEFAULT NULL COMMENT "Fichier de prévisualisation",
        `metrics` text DEFAULT NULL COMMENT "Métriques du contenu JSON",
        `feedback` text DEFAULT NULL,
        `submitted_at` datetime DEFAULT NULL,
        `approved_at` datetime DEFAULT NULL,
        `published_at` datetime DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `campaign_id` (`campaign_id`),
        KEY `influencer_id` (`influencer_id`),
        KEY `task_id` (`task_id`),
        KEY `status` (`status`),
        KEY `due_date` (`due_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_interactions')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_interactions` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `influencer_id` int(11) NOT NULL,
        `staff_id` int(11) DEFAULT NULL COMMENT "Membre de l\'équipe",
        `type` varchar(50) NOT NULL COMMENT "email, call, meeting, note, message",
        `subject` varchar(200) DEFAULT NULL,
        `description` text DEFAULT NULL,
        `contact_date` datetime NOT NULL,
        `next_followup` date DEFAULT NULL,
        `status` varchar(50) DEFAULT "completed" COMMENT "scheduled, completed, cancelled",
        `attachments` text DEFAULT NULL COMMENT "Fichiers joints JSON",
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `influencer_id` (`influencer_id`),
        KEY `staff_id` (`staff_id`),
        KEY `type` (`type`),
        KEY `contact_date` (`contact_date`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_tags')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `color` varchar(20) DEFAULT "#3498db",
        `description` varchar(255) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_influencer_tags')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_influencer_tags` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `influencer_id` int(11) NOT NULL,
        `tag_id` int(11) NOT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `unique_influencer_tag` (`influencer_id`, `tag_id`),
        KEY `tag_id` (`tag_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_content_library')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_content_library` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `influencer_id` int(11) NOT NULL,
        `campaign_id` int(11) DEFAULT NULL,
        `deliverable_id` int(11) DEFAULT NULL,
        `title` varchar(200) NOT NULL,
        `description` text DEFAULT NULL,
        `type` varchar(50) DEFAULT "image" COMMENT "image, video, article, story",
        `platform` varchar(50) DEFAULT NULL,
        `file_path` varchar(255) DEFAULT NULL,
        `thumbnail` varchar(255) DEFAULT NULL,
        `url` varchar(255) DEFAULT NULL,
        `performance_metrics` text DEFAULT NULL COMMENT "Métriques JSON",
        `is_reusable` tinyint(1) DEFAULT 1,
        `tags` text DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `influencer_id` (`influencer_id`),
        KEY `campaign_id` (`campaign_id`),
        KEY `deliverable_id` (`deliverable_id`),
        KEY `type` (`type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_email_templates')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_email_templates` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `subject` varchar(255) NOT NULL,
        `body` text NOT NULL,
        `type` varchar(50) DEFAULT "outreach" COMMENT "outreach, followup, contract, thank_you",
        `variables` text DEFAULT NULL COMMENT "Variables disponibles JSON",
        `is_active` tinyint(1) DEFAULT 1,
        `created_by` int(11) DEFAULT NULL,
        `created_at` datetime DEFAULT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

if (!$CI->db->table_exists(db_prefix() . 'im_settings')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_settings` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(100) NOT NULL,
        `value` text DEFAULT NULL,
        `autoload` tinyint(1) DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `name` (`name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Insert default settings
$default_settings = [
    ['name' => 'im_default_currency', 'value' => 'EUR', 'autoload' => 1],
    ['name' => 'im_scoring_algorithm_version', 'value' => '1.0', 'autoload' => 1],
    ['name' => 'im_auto_sync_metrics', 'value' => '1', 'autoload' => 1],
    ['name' => 'im_sync_frequency_hours', 'value' => '24', 'autoload' => 1],
    ['name' => 'im_fake_followers_threshold', 'value' => '30', 'autoload' => 1],
    ['name' => 'im_enable_dark_mode', 'value' => '1', 'autoload' => 1],
    ['name' => 'im_default_view', 'value' => 'grid', 'autoload' => 1],
    ['name' => 'im_email_tracking', 'value' => '1', 'autoload' => 1],
];

foreach ($default_settings as $setting) {
    $exists = $CI->db->get_where(db_prefix() . 'im_settings', ['name' => $setting['name']])->row();
    if (!$exists) {
        $CI->db->insert(db_prefix() . 'im_settings', array_merge($setting, ['id' => NULL]));
    }
}

// Insert default email templates
$default_templates = [
    [
        'name' => 'Premier contact - Influenceur',
        'subject' => 'Collaboration avec {{company_name}}',
        'body' => 'Bonjour {{influencer_firstname}},\n\nNous avons découvert votre contenu sur {{platform}} et sommes impressionnés par votre engagement avec votre communauté.\n\nNous aimerions discuter d\'une potentielle collaboration pour notre marque {{company_name}}.\n\nSeriez-vous disponible pour un échange cette semaine ?\n\nCordialement,\n{{staff_firstname}} {{staff_lastname}}\n{{company_name}}',
        'type' => 'outreach',
        'variables' => json_encode(['influencer_firstname', 'influencer_lastname', 'platform', 'company_name', 'staff_firstname', 'staff_lastname']),
        'is_active' => 1,
    ],
    [
        'name' => 'Relance - Sans réponse',
        'subject' => 'Re: Collaboration avec {{company_name}}',
        'body' => 'Bonjour {{influencer_firstname}},\n\nJe me permets de vous relancer concernant ma proposition de collaboration.\n\nNous serions ravis de travailler avec vous et pensons que notre marque correspond parfaitement à votre univers.\n\nAvez-vous eu le temps de consulter ma proposition ?\n\nBien cordialement,\n{{staff_firstname}}',
        'type' => 'followup',
        'variables' => json_encode(['influencer_firstname', 'company_name', 'staff_firstname']),
        'is_active' => 1,
    ],
];

foreach ($default_templates as $template) {
    $exists = $CI->db->get_where(db_prefix() . 'im_email_templates', ['name' => $template['name']])->row();
    if (!$exists) {
        $template['created_at'] = date('Y-m-d H:i:s');
        $CI->db->insert(db_prefix() . 'im_email_templates', $template);
    }
}

// Insert default tags
$default_tags = [
    ['name' => 'Beauté', 'color' => '#e74c3c', 'description' => 'Influenceurs beauté et cosmétiques'],
    ['name' => 'Tech', 'color' => '#3498db', 'description' => 'Influenceurs technologie'],
    ['name' => 'Lifestyle', 'color' => '#9b59b6', 'description' => 'Influenceurs lifestyle'],
    ['name' => 'Sport', 'color' => '#2ecc71', 'description' => 'Influenceurs sport et fitness'],
    ['name' => 'Food', 'color' => '#f39c12', 'description' => 'Influenceurs cuisine et gastronomie'],
    ['name' => 'Travel', 'color' => '#1abc9c', 'description' => 'Influenceurs voyage'],
    ['name' => 'Fashion', 'color' => '#e91e63', 'description' => 'Influenceurs mode'],
    ['name' => 'Gaming', 'color' => '#9c27b0', 'description' => 'Influenceurs gaming'],
    ['name' => 'B2B', 'color' => '#34495e', 'description' => 'Influenceurs B2B'],
    ['name' => 'Végan', 'color' => '#27ae60', 'description' => 'Influenceurs végan'],
];

foreach ($default_tags as $tag) {
    $exists = $CI->db->get_where(db_prefix() . 'im_tags', ['name' => $tag['name']])->row();
    if (!$exists) {
        $tag['created_at'] = date('Y-m-d H:i:s');
        $CI->db->insert(db_prefix() . 'im_tags', $tag);
    }
}

// Add permissions
$capabilities = [
    'view',
    'create',
    'edit',
    'delete',
];

foreach ($capabilities as $capability) {
    add_permission('influencers_marketing', $capability);
}

// Create default folder for uploads
$upload_path = FCPATH . 'uploads/influencers_marketing';
if (!file_exists($upload_path)) {
    mkdir($upload_path, 0755, true);
    mkdir($upload_path . '/profiles', 0755, true);
    mkdir($upload_path . '/content', 0755, true);
    mkdir($upload_path . '/contracts', 0755, true);
    mkdir($upload_path . '/deliverables', 0755, true);
}
