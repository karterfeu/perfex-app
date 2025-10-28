# Influencers Marketing - Module Perfex CRM

🎯 Module complet de gestion d'influenceurs marketing avec analytics avancés, gestion de campagnes, scoring propriétaire et intégration native Perfex CRM.

## 📋 Table des matières

- [Fonctionnalités](#-fonctionnalités)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Utilisation](#-utilisation)
- [Architecture](#-architecture)
- [API](#-api)
- [Support](#-support)

## ✨ Fonctionnalités

### 🎨 Interface Moderne & Intuitive

- **Dashboard visuel** avec cards interactives et graphiques en temps réel
- **Mode sombre/clair** pour confort visuel optimal
- **Vue Kanban** pour le suivi des campagnes (Prospect → Négociation → En cours → Terminé)
- **Drag & drop** pour organiser les influenceurs dans les campagnes
- **Recherche intelligente** avec filtres multiples et suggestions auto-complètes
- **Vues multiples** : grille avec photos de profil, liste détaillée, vue carte géographique

### 📊 Analytics Avancés & Scoring

- **Score d'influence propriétaire** calculé avec un algorithme basé sur :
  - Engagement (35%)
  - Portée/Reach (25%)
  - Croissance (20%)
  - Qualité d'audience (15%)
  - Activité régulière (5%)
- **Graphiques interactifs** : évolution followers, engagement rate par période
- **Comparateur d'influenceurs** côte à côte pour sélection campagne
- **Prédiction de performance** basée sur l'historique
- **Détection des fake followers** avec indicateurs de qualité d'audience
- **Analyse démographique** de l'audience (âge, genre, localisation)

### 🤖 Automatisation & IA

- **Import en masse** depuis CSV/Excel avec mapping intelligent
- **Enrichissement automatique** des profils via APIs
- **Alertes intelligentes** : baisse engagement, nouveau concurrent, pic de croissance
- **Suggestions d'influenceurs** pour une campagne selon critères
- **Templates de messages** personnalisables pour outreach
- **Rappels automatiques** de suivi

### 💼 Gestion des Relations (CRM Avancé)

- **Historique complet** des interactions (emails, appels, réunions)
- **Notes collaboratives** avec mentions d'équipe (@nom)
- **Statuts personnalisés** du pipeline de négociation
- **Documents attachés** (contrats, briefs, médias)
- **Tarification et conditions** par influenceur/plateforme
- **Système de tags** multiples et colorés

### 📱 Gestion Multi-Plateformes

Support complet pour :
- Instagram
- YouTube
- TikTok
- Facebook
- Twitter/X
- LinkedIn
- Twitch
- Snapchat

### 🎯 Gestion de Campagnes Premium

- **Briefs collaboratifs** avec validation par étapes
- **Calendrier éditorial** intégré avec preview des publications
- **Suivi des livrables** avec statuts (À faire, En révision, Validé, Publié)
- **Gestion budgétaire** : prévu vs réel, paiements en attente
- **ROI tracking** : impressions, clics, conversions, CA généré
- **Bibliothèque de contenus** créés par influenceurs (réutilisables)

### 💰 Intégration Facturation Perfex CRM

- **Conversion automatique** influenceur → client Perfex
- **Création de devis** directement depuis campagne
- **Génération de factures** par livrable ou campagne
- **Suivi des paiements** avec statut (En attente, Payé, En retard)
- **Historique financier** par influenceur
- **Export comptable** compatible avec le système Perfex

### 🔒 Sécurité & Conformité

- **RGPD compliant** : consentement, export de données, droit à l'oubli
- **Logs d'audit** des accès et modifications
- **Chiffrement** des données sensibles (tokens API, contacts)
- **Permissions granulaires** par rôle (Admin, Manager, Commercial, Viewer)

## 📦 Installation

### Prérequis

- Perfex CRM version 2.9.0 ou supérieure
- PHP 7.4 ou supérieur
- MySQL 5.7 ou supérieur
- Extensions PHP : curl, gd, mbstring

### Étapes d'installation

1. **Téléchargez** le module depuis votre espace client

2. **Extrayez** l'archive dans le dossier `modules/` de votre installation Perfex CRM :
   ```
   /path/to/perfex/modules/influencers_marketing/
   ```

3. **Configurez les permissions** :
   ```bash
   chmod -R 755 modules/influencers_marketing
   chown -R www-data:www-data modules/influencers_marketing
   ```

4. **Créez le dossier uploads** :
   ```bash
   mkdir -p uploads/influencers_marketing/{profiles,content,contracts,deliverables}
   chmod -R 755 uploads/influencers_marketing
   ```

5. **Activez le module** :
   - Connectez-vous à Perfex CRM en tant qu'administrateur
   - Allez dans `Configuration` → `Modules`
   - Trouvez "Influencers Marketing" et cliquez sur `Activer`

6. **Configuration initiale** :
   - Allez dans `Influenceurs Marketing` → `Paramètres`
   - Configurez vos préférences (devise, synchronisation, etc.)

## ⚙️ Configuration

### Paramètres Généraux

| Paramètre | Description | Valeur par défaut |
|-----------|-------------|-------------------|
| Devise par défaut | Devise utilisée pour les budgets et factures | EUR |
| Synchronisation auto | Actualisation automatique des métriques | Activé |
| Fréquence de sync | Intervalle de synchronisation (heures) | 24h |
| Seuil faux followers | Pourcentage pour alerter sur faux followers | 30% |
| Mode sombre | Activer le thème sombre par défaut | Activé |
| Vue par défaut | Vue affichée au chargement | Grille |

### Permissions

Le module utilise 4 niveaux de permissions :

| Permission | Description |
|------------|-------------|
| **Voir** | Consulter les influenceurs et campagnes |
| **Créer** | Ajouter de nouveaux influenceurs et campagnes |
| **Modifier** | Éditer les données existantes |
| **Supprimer** | Supprimer des éléments |

Configuration dans `Configuration` → `Rôles` → sélectionner un rôle → onglet "Influenceurs Marketing"

### Intégration APIs (optionnel)

Pour l'enrichissement automatique des profils, vous pouvez configurer des APIs :

```php
// Dans modules/influencers_marketing/config/api_config.php
define('IM_INSTAGRAM_API_KEY', 'votre_clé_api');
define('IM_YOUTUBE_API_KEY', 'votre_clé_api');
```

## 🚀 Utilisation

### Ajouter un Influenceur

1. Allez dans `Influenceurs Marketing` → `Influenceurs`
2. Cliquez sur `Nouvel influenceur`
3. Remplissez les informations de base
4. Ajoutez les comptes sociaux
5. Le score d'influence sera calculé automatiquement
6. Sauvegardez

### Créer une Campagne

1. Allez dans `Influenceurs Marketing` → `Campagnes`
2. Cliquez sur `Nouvelle campagne`
3. Définissez le nom, budget, dates
4. Ajoutez des influenceurs à la campagne
5. Définissez les livrables attendus
6. Créez automatiquement des devis/factures

### Analyser les Performances

1. Allez dans `Influenceurs Marketing` → `Analytics`
2. Sélectionnez un influenceur ou une campagne
3. Choisissez la période d'analyse
4. Consultez les graphiques :
   - Croissance des followers
   - Taux d'engagement
   - ROI par campagne
   - Comparaisons

### Convertir en Client Perfex

1. Ouvrez le profil d'un influenceur
2. Cliquez sur `Convertir en client`
3. L'influenceur devient un client Perfex CRM
4. Vous pouvez maintenant créer des factures/devis

## 🏗️ Architecture

### Structure des Dossiers

```
modules/influencers_marketing/
├── assets/
│   ├── css/
│   │   ├── influencers_marketing.css
│   │   └── dark-mode.css
│   └── js/
│       ├── influencers_marketing.js
│       ├── dark-mode.js
│       └── analytics.js
├── controllers/
│   └── Influencers_marketing.php
├── models/
│   ├── Influencers_model.php
│   └── Campaigns_model.php
├── views/
│   ├── dashboard.php
│   ├── influencers/
│   ├── campaigns/
│   ├── analytics/
│   └── settings.php
├── language/
│   └── french/
│       └── influencers_marketing_lang.php
├── install.php
├── influencers_marketing.php
└── README.md
```

### Base de Données

Le module crée 11 tables :

- `im_influencers` - Profils des influenceurs
- `im_social_accounts` - Comptes sociaux
- `im_metrics_history` - Historique des métriques
- `im_campaigns` - Campagnes marketing
- `im_campaign_influencers` - Relation campagnes/influenceurs
- `im_deliverables` - Livrables de campagnes
- `im_interactions` - Historique CRM
- `im_tags` - Tags disponibles
- `im_influencer_tags` - Tags des influenceurs
- `im_content_library` - Bibliothèque de contenus
- `im_email_templates` - Modèles d'emails
- `im_settings` - Paramètres du module

### Algorithme de Scoring

Le score d'influence (0-100) est calculé avec la formule :

```
Score = (Engagement × 0.35) + (Reach × 0.25) + (Growth × 0.20) + (Quality × 0.15) + (Activity × 0.05)
```

Avec bonus :
- +5 points si compte vérifié
- +10% si compte principal (primary)

### Détection de Faux Followers

Score de 0-100 où 100 = forte probabilité de faux followers :

| Critère | Poids |
|---------|-------|
| Ratio followers/following suspect | 30 points |
| Engagement < 0.5% | 40 points |
| Croissance > 50% en 30j | 20 points |
| Ratio posts/followers faible | 10 points |

Qualité de l'audience :
- **Excellent** : score < 20
- **Bon** : score 20-40
- **Moyen** : score 40-60
- **Faible** : score > 60

## 🔌 API

Le module expose une API REST pour intégrations tierces.

### Endpoints

#### Influenceurs

```
GET    /api/influencers              Liste des influenceurs
GET    /api/influencers/{id}         Détails d'un influenceur
POST   /api/influencers              Créer un influenceur
PUT    /api/influencers/{id}         Modifier un influenceur
DELETE /api/influencers/{id}         Supprimer un influenceur
```

#### Campagnes

```
GET    /api/campaigns                Liste des campagnes
GET    /api/campaigns/{id}           Détails d'une campagne
POST   /api/campaigns                Créer une campagne
PUT    /api/campaigns/{id}           Modifier une campagne
DELETE /api/campaigns/{id}           Supprimer une campagne
```

#### Analytics

```
GET    /api/analytics/influencer/{id}       Analytics d'un influenceur
GET    /api/analytics/campaign/{id}         Analytics d'une campagne
GET    /api/analytics/platform-distribution  Répartition par plateforme
```

### Authentification

Utilisez les tokens API Perfex CRM standard.

## 📊 Rapports

### Exports Disponibles

- **Liste influenceurs** (CSV, Excel, PDF)
- **Rapport de campagne** (PDF)
- **Analytics personnalisés** (CSV)
- **ROI global** (Excel)

### Rapports Automatiques

Configuration dans `Paramètres` → `Rapports automatiques` :
- Rapport hebdomadaire des performances
- Alertes sur baisse d'engagement
- Résumé mensuel des campagnes

## 🎨 Personnalisation

### Thème

Modifiez `assets/css/influencers_marketing.css` pour personnaliser :

```css
:root {
    --im-primary: #3498db;        /* Couleur principale */
    --im-success: #2ecc71;        /* Succès */
    --im-danger: #e74c3c;         /* Danger */
}
```

### Templates d'Emails

Créez vos propres templates dans `Influenceurs Marketing` → `Paramètres` → `Modèles d'emails`

Variables disponibles :
- `{{influencer_firstname}}`
- `{{influencer_lastname}}`
- `{{company_name}}`
- `{{staff_firstname}}`
- `{{campaign_name}}`

## 🐛 Dépannage

### Le module ne s'active pas

- Vérifiez les permissions des fichiers (755)
- Vérifiez les logs dans `application/logs/`
- Assurez-vous que la version de Perfex est compatible

### Les scores ne se calculent pas

- Vérifiez que les comptes sociaux ont bien des données (followers, engagement)
- Lancez une recalculation manuelle depuis le profil de l'influenceur

### Les graphiques ne s'affichent pas

- Vérifiez que Chart.js est bien chargé (console du navigateur)
- Videz le cache du navigateur
- Vérifiez les données dans `im_metrics_history`

## 📝 Changelog

### Version 1.0.0 (2024-01-15)

- ✨ Première version stable
- 🎨 Interface moderne avec mode sombre
- 📊 Système de scoring propriétaire
- 💰 Intégration complète Perfex CRM
- 🎯 Gestion avancée de campagnes
- 📈 Analytics en temps réel
- 🤖 Détection de faux followers
- 🌍 Localisation française 100%

## 🆘 Support

### Documentation

- Documentation complète : [https://docs.perfex.com/modules/influencers-marketing](https://docs.perfex.com)
- Vidéos tutoriels : [https://www.youtube.com/perfex](https://www.youtube.com)

### Contact

- Email : support@perfex.com
- Forum : https://forum.perfex.com
- Ticket : https://support.perfex.com

### Communauté

- Discord : https://discord.gg/perfex
- Facebook : https://facebook.com/groups/perfex

## 📄 Licence

© 2024 Perfex. Tous droits réservés.

Ce module est sous licence propriétaire. L'utilisation est limitée à une installation Perfex CRM par licence achetée.

## 🙏 Remerciements

Merci à tous les bêta-testeurs et à la communauté Perfex pour leurs retours précieux !

---

**Made with ❤️ by Perfex Team**
