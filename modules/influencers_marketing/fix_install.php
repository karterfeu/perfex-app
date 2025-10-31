<?php

/**
 * Script de réparation pour l'installation du module
 * Accédez à : https://votre-domaine.com/modules/influencers_marketing/fix_install.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><meta charset='utf-8'><title>Fix Installation</title>";
echo "<style>body{font-family:Arial;max-width:900px;margin:50px auto;padding:20px;}";
echo ".success{background:#d4edda;padding:15px;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}";
echo ".error{background:#f8d7da;padding:15px;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}";
echo ".warning{background:#fff3cd;padding:15px;border:1px solid #ffeaa7;border-radius:5px;margin:10px 0;}";
echo "pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}</style></head><body>";

echo "<h1>Réparation du module Influencers Marketing</h1><hr>";

// Charger la configuration de la base de données
$config_file = __DIR__ . '/../../application/config/database.php';

if (!file_exists($config_file)) {
    die('<div class="error"><h3>❌ Erreur</h3><p>Fichier de configuration non trouvé : ' . $config_file . '</p></div></body></html>');
}

require($config_file);

$db_config = $db['default'];

// Connexion à la base de données
try {
    $pdo = new PDO(
        "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    echo '<div class="success">✓ Connexion à la base de données réussie</div>';

    $prefix = $db_config['dbprefix'];

} catch (PDOException $e) {
    die('<div class="error"><h3>❌ Erreur de connexion</h3><p>' . htmlspecialchars($e->getMessage()) . '</p></div></body></html>');
}

// Étape 1 : Désactiver le module
echo "<h2>1. Désactivation du module</h2>";
try {
    $stmt = $pdo->prepare("UPDATE `{$prefix}modules` SET `active` = 0 WHERE `module_name` = 'influencers_marketing'");
    $stmt->execute();
    echo '<div class="success">✓ Module désactivé</div>';
} catch (PDOException $e) {
    echo '<div class="warning">⚠ ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Étape 2 : Supprimer la table si elle existe
echo "<h2>2. Suppression de la table im_campaign_contents</h2>";
try {
    $stmt = $pdo->query("DROP TABLE IF EXISTS `{$prefix}im_campaign_contents`");
    echo '<div class="success">✓ Table supprimée (si elle existait)</div>';
} catch (PDOException $e) {
    echo '<div class="warning">⚠ ' . htmlspecialchars($e->getMessage()) . '</div>';
}

// Étape 3 : Créer la table
echo "<h2>3. Création de la table im_campaign_contents</h2>";

$sql = "CREATE TABLE `{$prefix}im_campaign_contents` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

echo "<h4>Requête SQL :</h4>";
echo "<pre>" . htmlspecialchars($sql) . "</pre>";

try {
    $pdo->exec($sql);
    echo '<div class="success"><h3>✓ Table créée avec succès !</h3></div>';

    // Vérifier que la table existe
    $stmt = $pdo->query("SHOW TABLES LIKE '{$prefix}im_campaign_contents'");
    if ($stmt->rowCount() > 0) {
        echo '<div class="success">✓ Vérification : La table existe bien dans la base de données</div>';

        // Réactiver le module
        echo "<h2>4. Réactivation du module</h2>";
        $stmt = $pdo->prepare("UPDATE `{$prefix}modules` SET `active` = 1 WHERE `module_name` = 'influencers_marketing'");
        $stmt->execute();
        echo '<div class="success">✓ Module réactivé</div>';

        echo "<hr><h2 style='color:green;'>✓✓✓ SUCCÈS COMPLET ! ✓✓✓</h2>";
        echo "<p>Le module Influencers Marketing est maintenant correctement installé et activé.</p>";
        echo "<p><a href='../../admin/modules' style='background:#4CAF50;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;margin:10px 0;'>Retour aux modules</a></p>";
        echo "<p><a href='../../admin/influencers_marketing' style='background:#2196F3;color:white;padding:10px 20px;text-decoration:none;border-radius:5px;display:inline-block;'>Accéder au module</a></p>";

    } else {
        echo '<div class="error">✗ La table n\'a pas été créée</div>';
    }

} catch (PDOException $e) {
    echo '<div class="error"><h3>❌ Erreur lors de la création de la table</h3>';
    echo "<p><strong>Code erreur :</strong> " . $e->getCode() . "</p>";
    echo "<p><strong>Message :</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getMessage()) . "</pre>";
    echo "</div>";
}

echo "<hr>";
echo "<div class='warning'><strong>SÉCURITÉ :</strong> Supprimez ce fichier après utilisation :<br>";
echo "<code>rm " . __FILE__ . "</code></div>";

echo "</body></html>";
