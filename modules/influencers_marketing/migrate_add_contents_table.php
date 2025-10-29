<?php

/**
 * Script de migration pour ajouter la table im_campaign_contents
 *
 * Instructions :
 * 1. Accédez à cette URL UNE SEULE FOIS en tant qu'admin:
 *    https://votre-domaine.com/modules/influencers_marketing/migrate_add_contents_table.php
 * 2. Supprimez ce fichier après utilisation
 */

define('INFLUENCERS_MARKETING_MIGRATION', true);

// Charger Perfex CRM
require_once(__DIR__ . '/../../application/config/app-config.php');
require_once(FCPATH . 'application/libraries/App_init_base.php');

$app = new App_init_base();
$CI = &get_instance();

// Vérifier que l'utilisateur est admin
if (!is_admin()) {
    die('<h1>Accès refusé</h1><p>Vous devez être administrateur.</p>');
}

echo "<h2>Migration : Ajout de la table im_campaign_contents</h2>";
echo "<hr>";

// Vérifier si la table existe déjà
if ($CI->db->table_exists(db_prefix() . 'im_campaign_contents')) {
    echo "<p style='color: orange;'>⚠️ La table existe déjà. Aucune action nécessaire.</p>";
} else {
    echo "<p>Création de la table im_campaign_contents...</p>";

    try {
        $CI->db->query('CREATE TABLE `' . db_prefix() . 'im_campaign_contents` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `campaign_id` int(11) NOT NULL,
            `campaign_influencer_id` int(11) DEFAULT NULL,
            `influencer_id` int(11) NOT NULL,
            `platform` varchar(50) NOT NULL,
            `content_type` varchar(50) DEFAULT "post",
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

        echo "<p style='color: green;'>✓ Table créée avec succès !</p>";
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Erreur lors de la création de la table :</p>";
        echo "<pre>" . $e->getMessage() . "</pre>";
    }
}

echo "<hr>";
echo "<h3>Migration terminée !</h3>";
echo "<p><strong style='color: red;'>IMPORTANT :</strong> Supprimez maintenant ce fichier pour des raisons de sécurité :</p>";
echo "<code>rm " . __FILE__ . "</code>";
echo "<hr>";
echo "<p><a href='" . admin_url('influencers_marketing') . "'>← Retour au module</a></p>";
