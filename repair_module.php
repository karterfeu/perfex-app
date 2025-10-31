<?php
/**
 * Script de réparation pour le module Influencers Marketing
 * Accès direct via : https://hello.thefollowmovement.com/repair_module.php
 */

// Charger Perfex
define('REPAIR_MODE', true);
require_once('application/config/database.php');

// Connexion directe à la base de données
$db_config = $db['default'];

try {
    $conn = new PDO(
        "mysql:host={$db_config['hostname']};dbname={$db_config['database']};charset=utf8mb4",
        $db_config['username'],
        $db_config['password'],
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    echo "<!DOCTYPE html><html><head><meta charset='UTF-8'><title>Réparation Module IM</title>";
    echo "<style>body{font-family:Arial,sans-serif;max-width:1200px;margin:20px auto;padding:20px;}";
    echo ".success{color:green;}.error{color:red;}.info{color:blue;}";
    echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
    echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
    echo "th{background:#f2f2f2;}.action{margin:20px 0;padding:15px;background:#f9f9f9;border:1px solid #ddd;}";
    echo "button{padding:10px 20px;margin:5px;cursor:pointer;}</style></head><body>";

    echo "<h1>🔧 Réparation Module Influencers Marketing</h1>";

    // ÉTAPE 1: Vérifier l'état des tables
    echo "<div class='action'><h2>📊 ÉTAPE 1: État des tables</h2>";

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

    echo "<table><tr><th>Table</th><th>Statut</th><th>Nombre de lignes</th></tr>";

    $existing_tables = [];
    foreach ($tables as $table) {
        $full_table = 'tblc_' . $table;
        $stmt = $conn->query("SHOW TABLES LIKE '{$full_table}'");
        $exists = $stmt->rowCount() > 0;

        if ($exists) {
            $count_stmt = $conn->query("SELECT COUNT(*) as count FROM {$full_table}");
            $count = $count_stmt->fetch()['count'];
            echo "<tr><td>{$table}</td><td class='success'>✓ Existe</td><td>{$count}</td></tr>";
            $existing_tables[] = $table;
        } else {
            echo "<tr><td>{$table}</td><td class='error'>✗ Manquante</td><td>-</td></tr>";
        }
    }
    echo "</table>";
    echo "<p class='info'><strong>Tables existantes:</strong> " . count($existing_tables) . " / " . count($tables) . "</p>";
    echo "</div>";

    // ÉTAPE 2: Vérifier le module dans la base
    echo "<div class='action'><h2>🔍 ÉTAPE 2: État du module</h2>";
    $stmt = $conn->query("SELECT * FROM tblc_modules WHERE module_name = 'influencers_marketing'");
    $module = $stmt->fetch();

    if ($module) {
        echo "<table><tr><th>Propriété</th><th>Valeur</th></tr>";
        echo "<tr><td>Nom</td><td>{$module['module_name']}</td></tr>";
        echo "<tr><td>Activé</td><td>" . ($module['active'] ? '<span class="success">✓ OUI</span>' : '<span class="error">✗ NON</span>') . "</td></tr>";
        echo "<tr><td>Installé</td><td>" . ($module['installed'] ?? 'N/A') . "</td></tr>";
        echo "</table>";
    } else {
        echo "<p class='error'>✗ Module non trouvé dans la base de données</p>";
    }
    echo "</div>";

    // ÉTAPE 3: Vérifier les erreurs potentielles
    echo "<div class='action'><h2>⚠️ ÉTAPE 3: Diagnostic</h2>";

    $issues = [];

    if (count($existing_tables) > 0 && count($existing_tables) < count($tables)) {
        $issues[] = "Installation incomplète : " . count($existing_tables) . " tables sur " . count($tables) . " créées";
    }

    if ($module && $module['active'] == 1 && count($existing_tables) < count($tables)) {
        $issues[] = "Module marqué comme actif mais tables manquantes";
    }

    if (count($issues) > 0) {
        echo "<ul class='error'>";
        foreach ($issues as $issue) {
            echo "<li>{$issue}</li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='success'>✓ Aucun problème détecté</p>";
    }
    echo "</div>";

    // ÉTAPE 4: Actions de réparation
    echo "<div class='action'><h2>🛠️ ÉTAPE 4: Actions disponibles</h2>";

    if (isset($_GET['action'])) {
        $action = $_GET['action'];

        if ($action === 'drop_all') {
            echo "<h3>Suppression de toutes les tables...</h3>";
            foreach ($existing_tables as $table) {
                try {
                    $conn->exec("DROP TABLE IF EXISTS tblc_{$table}");
                    echo "<p class='success'>✓ Table {$table} supprimée</p>";
                } catch (Exception $e) {
                    echo "<p class='error'>✗ Erreur pour {$table}: " . $e->getMessage() . "</p>";
                }
            }
            echo "<p class='info'><strong>Terminé!</strong> <a href='repair_module.php'>Actualiser</a></p>";
        }

        if ($action === 'deactivate') {
            try {
                $conn->exec("UPDATE tblc_modules SET active = 0 WHERE module_name = 'influencers_marketing'");
                echo "<p class='success'>✓ Module désactivé</p>";
                echo "<p class='info'><a href='repair_module.php'>Actualiser</a></p>";
            } catch (Exception $e) {
                echo "<p class='error'>✗ Erreur: " . $e->getMessage() . "</p>";
            }
        }

        if ($action === 'recreate_all') {
            echo "<h3>Recréation de toutes les tables...</h3>";

            // Charger le script SQL complet
            $sql_file = __DIR__ . '/modules/influencers_marketing/install_complete.sql';
            if (file_exists($sql_file)) {
                $sql = file_get_contents($sql_file);

                // Séparer les requêtes
                $queries = array_filter(array_map('trim', explode(';', $sql)));

                foreach ($queries as $query) {
                    if (empty($query) || strpos($query, '--') === 0) continue;

                    try {
                        $conn->exec($query);
                        // Extraire le nom de la table de la requête
                        if (preg_match('/CREATE TABLE.*`tblc_(\w+)`/i', $query, $matches)) {
                            echo "<p class='success'>✓ Table {$matches[1]} créée</p>";
                        } elseif (preg_match('/DROP TABLE.*`tblc_(\w+)`/i', $query, $matches)) {
                            echo "<p class='info'>Suppression table {$matches[1]}</p>";
                        }
                    } catch (Exception $e) {
                        if (preg_match('/CREATE TABLE.*`tblc_(\w+)`/i', $query, $matches)) {
                            echo "<p class='error'>✗ Erreur pour {$matches[1]}: " . $e->getMessage() . "</p>";
                        } else {
                            echo "<p class='error'>✗ Erreur SQL: " . $e->getMessage() . "</p>";
                        }
                    }
                }
                echo "<p class='info'><strong>Terminé!</strong> <a href='repair_module.php'>Actualiser</a></p>";
            } else {
                echo "<p class='error'>✗ Fichier SQL non trouvé: {$sql_file}</p>";
            }
        }
    } else {
        echo "<form method='get'>";
        echo "<button type='submit' name='action' value='deactivate' style='background:#ff9800;color:white;border:none;'>Désactiver le module</button> ";
        echo "<button type='submit' name='action' value='drop_all' style='background:#f44336;color:white;border:none;' onclick='return confirm(\"Voulez-vous vraiment supprimer TOUTES les tables IM ?\")'>Supprimer toutes les tables</button> ";
        echo "<button type='submit' name='action' value='recreate_all' style='background:#4CAF50;color:white;border:none;'>Recréer toutes les tables</button>";
        echo "</form>";

        echo "<p class='info'><small>⚠️ <strong>Important:</strong> Sauvegardez votre base de données avant toute action!</small></p>";
    }
    echo "</div>";

    // ÉTAPE 5: Logs PHP
    echo "<div class='action'><h2>📝 ÉTAPE 5: Informations PHP</h2>";
    echo "<table><tr><th>Paramètre</th><th>Valeur</th></tr>";
    echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
    echo "<tr><td>MySQL Version</td><td>" . $conn->getAttribute(PDO::ATTR_SERVER_VERSION) . "</td></tr>";
    echo "<tr><td>display_errors</td><td>" . ini_get('display_errors') . "</td></tr>";
    echo "<tr><td>error_reporting</td><td>" . error_reporting() . "</td></tr>";
    echo "</table>";
    echo "</div>";

    echo "<p style='margin-top:30px;'><a href='admin/modules' style='padding:10px 20px;background:#2196F3;color:white;text-decoration:none;border-radius:4px;'>← Retour aux modules</a></p>";

} catch (Exception $e) {
    echo "<h1 style='color:red;'>❌ ERREUR CRITIQUE</h1>";
    echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
    echo "<p><strong>Fichier:</strong> " . $e->getFile() . "</p>";
    echo "<p><strong>Ligne:</strong> " . $e->getLine() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";
