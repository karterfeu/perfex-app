<?php

/**
 * Script de test pour l'installation
 * Accédez à : https://votre-domaine.com/modules/influencers_marketing/test_install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Charger Perfex sans la vérification BASEPATH
$_SERVER['CI_ENV'] = 'development';

require_once(__DIR__ . '/../../index.php');

// Le reste du code après le chargement
$CI = &get_instance();

// Vérifier que l'utilisateur est admin
if (!is_admin()) {
    die('<h1>Accès refusé</h1><p>Vous devez être administrateur.</p>');
}

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Test Installation</title></head><body>";
echo "<h2>Test de l'installation du module Influencers Marketing</h2>";
echo "<hr>";

// Désactiver le module d'abord
echo "<h3>1. Désactivation du module...</h3>";
$CI->db->where('module_name', 'influencers_marketing');
$CI->db->update(db_prefix() . 'modules', ['active' => 0]);
echo "<p style='color: green;'>✓ Module désactivé</p>";

// Supprimer la table problématique si elle existe
echo "<h3>2. Nettoyage des tables existantes...</h3>";
$tables_to_drop = [
    'im_campaign_contents' // Seulement la nouvelle table
];

foreach ($tables_to_drop as $table) {
    $full_name = db_prefix() . $table;
    if ($CI->db->table_exists($full_name)) {
        $CI->db->query("DROP TABLE `$full_name`");
        echo "<p style='color: orange;'>⚠ Table $full_name supprimée</p>";
    }
}

// Maintenant essayer de créer juste la table problématique
echo "<h3>3. Test de création de la table im_campaign_contents...</h3>";

try {
    $sql = 'CREATE TABLE `' . db_prefix() . 'im_campaign_contents` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';';

    echo "<h4>Requête SQL :</h4>";
    echo "<pre>" . htmlspecialchars($sql) . "</pre>";

    $CI->db->query($sql);

    echo "<p style='color: green; font-size: 18px; font-weight: bold;'>✓ Table créée avec succès !</p>";

} catch (Exception $e) {
    echo "<div style='background: #ffdddd; padding: 20px; border: 2px solid red; margin: 20px 0;'>";
    echo "<h3 style='color: red;'>❌ ERREUR</h3>";
    echo "<p><strong>Message :</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}

// Afficher l'erreur de la base de données
$db_error = $CI->db->error();
if (!empty($db_error['message'])) {
    echo "<div style='background: #ffdddd; padding: 20px; border: 2px solid red; margin: 20px 0;'>";
    echo "<h3 style='color: red;'>❌ Erreur MySQL</h3>";
    echo "<p><strong>Code :</strong> " . $db_error['code'] . "</p>";
    echo "<p><strong>Message :</strong></p>";
    echo "<pre>" . htmlspecialchars($db_error['message']) . "</pre>";
    echo "</div>";
}

// Vérifier que la table existe
echo "<h3>4. Vérification...</h3>";
if ($CI->db->table_exists(db_prefix() . 'im_campaign_contents')) {
    echo "<p style='color: green; font-size: 18px;'>✓ La table existe maintenant !</p>";

    // Réactiver le module
    echo "<h3>5. Réactivation du module...</h3>";
    $CI->db->where('module_name', 'influencers_marketing');
    $CI->db->update(db_prefix() . 'modules', ['active' => 1]);
    echo "<p style='color: green;'>✓ Module réactivé</p>";

    echo "<hr>";
    echo "<h2 style='color: green;'>✓ SUCCÈS !</h2>";
    echo "<p>Le module est maintenant activé et fonctionnel.</p>";
    echo "<p><a href='" . admin_url('influencers_marketing') . "' style='background: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Accéder au module</a></p>";

} else {
    echo "<p style='color: red; font-size: 18px;'>✗ La table n'a pas été créée</p>";
    echo "<p>Consultez les erreurs ci-dessus pour plus de détails.</p>";
}

echo "<hr>";
echo "<p><strong>IMPORTANT :</strong> Supprimez ce fichier après utilisation pour des raisons de sécurité :</p>";
echo "<code>rm " . __FILE__ . "</code>";

echo "</body></html>";
