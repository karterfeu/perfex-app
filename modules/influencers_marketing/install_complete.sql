-- =====================================================
-- Script SQL complet pour le module Influencers Marketing
-- À exécuter dans phpMyAdmin
-- =====================================================

-- NOTE: Remplacez 'tblc_' par votre préfixe de base de données si différent

-- 1. Supprimer toutes les tables existantes du module
DROP TABLE IF EXISTS `tblc_im_campaign_contents`;
DROP TABLE IF EXISTS `tblc_im_email_templates`;
DROP TABLE IF EXISTS `tblc_im_content_library`;
DROP TABLE IF EXISTS `tblc_im_influencer_tags`;
DROP TABLE IF EXISTS `tblc_im_tags`;
DROP TABLE IF EXISTS `tblc_im_interactions`;
DROP TABLE IF EXISTS `tblc_im_deliverables`;
DROP TABLE IF EXISTS `tblc_im_campaign_influencers`;
DROP TABLE IF EXISTS `tblc_im_campaigns`;
DROP TABLE IF EXISTS `tblc_im_metrics_history`;
DROP TABLE IF EXISTS `tblc_im_social_accounts`;
DROP TABLE IF EXISTS `tblc_im_influencers`;
DROP TABLE IF EXISTS `tblc_im_settings`;

-- 2. Créer toutes les tables avec la syntaxe correcte

-- Table: im_influencers
CREATE TABLE `tblc_im_influencers` (
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
    `language` varchar(50) DEFAULT 'fr',
    `category` varchar(100) DEFAULT NULL,
    `influence_score` decimal(5,2) DEFAULT 0.00,
    `status` varchar(50) DEFAULT 'prospect',
    `rating` int(1) DEFAULT 0,
    `tags` text,
    `pricing_info` text,
    `notes` text,
    `is_verified` tinyint(1) DEFAULT 0,
    `is_favorite` tinyint(1) DEFAULT 0,
    `fake_followers_score` decimal(5,2) DEFAULT NULL,
    `audience_quality` varchar(50) DEFAULT 'unknown',
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    `datecreated` datetime DEFAULT NULL,
    `addedfrom` int(11) DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `contact_id` (`contact_id`),
    KEY `customer_id` (`customer_id`),
    KEY `staff_id` (`staff_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_social_accounts
CREATE TABLE `tblc_im_social_accounts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `influencer_id` int(11) NOT NULL,
    `platform` varchar(50) NOT NULL,
    `username` varchar(100) DEFAULT NULL,
    `profile_url` varchar(255) DEFAULT NULL,
    `followers_count` int(11) DEFAULT 0,
    `following_count` int(11) DEFAULT 0,
    `posts_count` int(11) DEFAULT 0,
    `engagement_rate` decimal(5,2) DEFAULT 0.00,
    `avg_likes` int(11) DEFAULT 0,
    `avg_comments` int(11) DEFAULT 0,
    `is_verified` tinyint(1) DEFAULT 0,
    `is_primary` tinyint(1) DEFAULT 0,
    `last_sync` datetime DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `influencer_id` (`influencer_id`),
    KEY `platform` (`platform`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_metrics_history
CREATE TABLE `tblc_im_metrics_history` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `social_account_id` int(11) NOT NULL,
    `followers_count` int(11) DEFAULT 0,
    `following_count` int(11) DEFAULT 0,
    `posts_count` int(11) DEFAULT 0,
    `engagement_rate` decimal(5,2) DEFAULT 0.00,
    `recorded_at` datetime NOT NULL,
    PRIMARY KEY (`id`),
    KEY `social_account_id` (`social_account_id`),
    KEY `recorded_at` (`recorded_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_campaigns
CREATE TABLE `tblc_im_campaigns` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `description` text,
    `brief` text,
    `goals` text,
    `project_id` int(11) DEFAULT NULL,
    `customer_id` int(11) DEFAULT NULL,
    `status` varchar(50) DEFAULT 'draft',
    `budget` decimal(15,2) DEFAULT 0.00,
    `actual_cost` decimal(15,2) DEFAULT 0.00,
    `currency` varchar(10) DEFAULT 'EUR',
    `start_date` date DEFAULT NULL,
    `end_date` date DEFAULT NULL,
    `roi` decimal(10,2) DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `project_id` (`project_id`),
    KEY `customer_id` (`customer_id`),
    KEY `status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_campaign_influencers
CREATE TABLE `tblc_im_campaign_influencers` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `campaign_id` int(11) NOT NULL,
    `influencer_id` int(11) NOT NULL,
    `status` varchar(50) DEFAULT 'invited',
    `compensation_type` varchar(50) DEFAULT 'paid',
    `compensation_amount` decimal(15,2) DEFAULT 0.00,
    `currency` varchar(10) DEFAULT 'EUR',
    `contract_signed` tinyint(1) DEFAULT 0,
    `invoice_id` int(11) DEFAULT NULL,
    `quote_id` int(11) DEFAULT NULL,
    `added_at` datetime DEFAULT NULL,
    `accepted_at` datetime DEFAULT NULL,
    `completed_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_campaign_influencer` (`campaign_id`, `influencer_id`),
    KEY `invoice_id` (`invoice_id`),
    KEY `quote_id` (`quote_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_deliverables
CREATE TABLE `tblc_im_deliverables` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `campaign_id` int(11) NOT NULL,
    `influencer_id` int(11) NOT NULL,
    `task_id` int(11) DEFAULT NULL,
    `title` varchar(200) NOT NULL,
    `description` text,
    `type` varchar(50) DEFAULT 'post',
    `platform` varchar(50) DEFAULT NULL,
    `due_date` date DEFAULT NULL,
    `status` varchar(50) DEFAULT 'pending',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_interactions
CREATE TABLE `tblc_im_interactions` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `influencer_id` int(11) NOT NULL,
    `staff_id` int(11) DEFAULT NULL,
    `type` varchar(50) NOT NULL,
    `subject` varchar(200) DEFAULT NULL,
    `description` text,
    `contact_date` datetime NOT NULL,
    `next_action` text,
    `next_action_date` date DEFAULT NULL,
    `status` varchar(50) DEFAULT 'completed',
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `influencer_id` (`influencer_id`),
    KEY `staff_id` (`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_tags
CREATE TABLE `tblc_im_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `color` varchar(20) DEFAULT '#3498db',
    `description` text,
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_influencer_tags
CREATE TABLE `tblc_im_influencer_tags` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `influencer_id` int(11) NOT NULL,
    `tag_id` int(11) NOT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_influencer_tag` (`influencer_id`, `tag_id`),
    KEY `tag_id` (`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_content_library
CREATE TABLE `tblc_im_content_library` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(200) NOT NULL,
    `description` text,
    `type` varchar(50) DEFAULT 'image',
    `file_path` varchar(255) DEFAULT NULL,
    `url` varchar(255) DEFAULT NULL,
    `thumbnail` varchar(255) DEFAULT NULL,
    `tags` text,
    `campaign_id` int(11) DEFAULT NULL,
    `influencer_id` int(11) DEFAULT NULL,
    `created_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`),
    KEY `campaign_id` (`campaign_id`),
    KEY `influencer_id` (`influencer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_email_templates
CREATE TABLE `tblc_im_email_templates` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(200) NOT NULL,
    `subject` varchar(255) NOT NULL,
    `body` text NOT NULL,
    `type` varchar(50) DEFAULT 'outreach',
    `variables` text,
    `is_active` tinyint(1) DEFAULT 1,
    `created_by` int(11) DEFAULT NULL,
    `created_at` datetime DEFAULT NULL,
    `updated_at` datetime DEFAULT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_settings
CREATE TABLE `tblc_im_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL,
    `value` text,
    `autoload` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Table: im_campaign_contents
CREATE TABLE `tblc_im_campaign_contents` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `campaign_id` int(11) NOT NULL,
    `campaign_influencer_id` int(11) DEFAULT NULL,
    `influencer_id` int(11) NOT NULL,
    `platform` varchar(50) NOT NULL,
    `content_type` varchar(50) DEFAULT 'post',
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Activer le module
UPDATE `tblc_modules`
SET `active` = 1
WHERE `module_name` = 'influencers_marketing';

-- FIN DU SCRIPT
-- Si aucune erreur, le module devrait être activé et fonctionnel
