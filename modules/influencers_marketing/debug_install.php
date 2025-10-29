<?php

/**
 * Script de débogage pour l'installation du module
 *
 * Accédez à : https://votre-domaine.com/modules/influencers_marketing/debug_install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('INFLUENCERS_MARKETING_DEBUG', true);

// Charger Perfex CRM
require_once(__DIR__ . '/../../application/config/app-config.php');
require_once(FCPATH . 'application/libraries/App_init_base.php');

$app = new App_init_base();
$CI = &get_instance();

// Vérifier que l'utilisateur est admin
if (!is_admin()) {
    die('<h1>Accès refusé</h1><p>Vous devez être administrateur.</p>');
}

echo "<h2>Débogage de l'installation du module Influencers Marketing</h2>";
echo "<hr>";

echo "<h3>Exécution du script install.php...</h3>";

try {
    ob_start();
    require_once(__DIR__ . '/install.php');
    $output = ob_get_clean();

    echo "<p style='color: green;'>✓ Installation réussie !</p>";

    if ($output) {
        echo "<h4>Output :</h4>";
        echo "<pre>" . htmlspecialchars($output) . "</pre>";
    }

    // Vérifier les tables créées
    echo "<h3>Vérification des tables :</h3>";
    $tables = [
        'im_influencers',
        'im_social_accounts',
        'im_metrics_history',
        'im_campaigns',
        'im_campaign_influencers',
        'im_deliverables',
        'im_interactions',
        'im_tags',
        'im_influencer_tags',
        'im_content_library',
        'im_email_templates',
        'im_settings',
        'im_campaign_contents'
    ];

    echo "<ul>";
    foreach ($tables as $table) {
        $exists = $CI->db->table_exists(db_prefix() . $table);
        $color = $exists ? 'green' : 'red';
        $status = $exists ? '✓' : '✗';
        echo "<li style='color: $color;'>$status " . db_prefix() . $table . "</li>";
    }
    echo "</ul>";

} catch (Exception $e) {
    echo "<div style='background: #ffdddd; padding: 20px; border: 2px solid red; margin: 20px 0;'>";
    echo "<h3 style='color: red;'>❌ ERREUR DÉTECTÉE</h3>";
    echo "<p><strong>Message :</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "<p><strong>Fichier :</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Ligne :</strong> " . $e->getLine() . "</p>";
    echo "<p><strong>Stack trace :</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
    echo "</div>";

    // Afficher la dernière requête SQL
    echo "<h4>Dernière requête SQL :</h4>";
    echo "<pre>" . htmlspecialchars($CI->db->last_query()) . "</pre>";

    // Afficher l'erreur MySQL
    $db_error = $CI->db->error();
    if (!empty($db_error['message'])) {
        echo "<h4>Erreur MySQL :</h4>";
        echo "<pre>";
        echo "Code: " . $db_error['code'] . "\n";
        echo "Message: " . htmlspecialchars($db_error['message']);
        echo "</pre>";
    }
}

echo "<hr>";
echo "<p><a href='" . admin_url('modules') . "'>← Retour aux modules</a></p>";
