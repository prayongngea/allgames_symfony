# 📚 DOCUMENTATION COMPLÈTE — AllGames
## Projet Symfony 7.2 — Guide de déploiement multi-appareils

---

## 🗂️ TABLE DES MATIÈRES

1. [Vue d'ensemble du projet](#1-vue-densemble)
2. [Stack technique](#2-stack-technique)
3. [Architecture du projet](#3-architecture)
4. [Entités et base de données](#4-base-de-données)
5. [Prérequis système](#5-prérequis)
6. [Installation complète pas à pas](#6-installation)
7. [Configuration critique](#7-configuration)
8. [Extensions PHP requises](#8-extensions-php)
9. [Problèmes connus et solutions](#9-problèmes-connus)
10. [Routes et fonctionnalités](#10-routes)
11. [Comptes utilisateurs](#11-comptes)
12. [Checklist déploiement](#12-checklist)

---

## 1. VUE D'ENSEMBLE

**AllGames** est une application web de type Steam/Epic Games Store permettant de :
- Parcourir un catalogue de jeux vidéo
- Gérer une wishlist personnelle
- Laisser des avis (recommandé / non recommandé)
- Administrer tout le contenu via un back-office EasyAdmin

**URL locale :** http://127.0.0.1:8000  
**URL admin :** http://127.0.0.1:8000/admin

---

## 2. STACK TECHNIQUE

| Composant | Version | Rôle |
|-----------|---------|------|
| PHP | 8.2+ | Langage serveur |
| Symfony | 7.2.* | Framework PHP |
| Doctrine ORM | 3.x | ORM / gestion BDD |
| MySQL | 8.0+ | Base de données |
| EasyAdmin | 4.x | Interface d'administration |
| VichUploader | 2.x | Upload d'images |
| Twig | 3.x | Moteur de templates |
| Tailwind CSS | CDN | CSS (pas de compilation) |

> ⚠️ **IMPORTANT** : Tailwind est chargé via CDN (`https://cdn.tailwindcss.com`).  
> Une connexion internet est nécessaire pour que le style s'affiche.  
> Sur un réseau sans internet, ajouter Tailwind en local.

---

## 3. ARCHITECTURE DU PROJET

```
all_games/
├── bin/
│   └── console                    ← CLI Symfony (php bin/console ...)
├── config/
│   ├── bundles.php                ← Liste des bundles activés
│   ├── routes.yaml                ← Configuration du routage
│   ├── services.yaml              ← Injection de dépendances
│   └── packages/
│       ├── doctrine.yaml          ← Config base de données
│       ├── doctrine_migrations.yaml
│       ├── framework.yaml         ← Config Symfony core
│       ├── monolog.yaml           ← Config logs
│       ├── security.yaml          ← Auth, firewall, roles
│       ├── twig.yaml              ← Config templates
│       ├── validator.yaml         ← Config validation
│       ├── vich_uploader.yaml     ← Config upload images
│       └── web_profiler.yaml      ← Barre de debug (dev only)
├── migrations/
│   └── Version20250101000000.php  ← Migration BDD initiale
├── public/
│   ├── index.php                  ← Point d'entrée de l'app
│   ├── build/
│   │   └── manifest.json         ← Fichier requis (vide = OK)
│   └── images/
│       ├── placeholder.jpg        ← Image par défaut
│       └── games/                 ← Dossier uploads images jeux
├── src/
│   ├── Controller/
│   │   ├── Admin/
│   │   │   ├── DashboardController.php
│   │   │   ├── EditorCrudController.php
│   │   │   ├── GameCrudController.php
│   │   │   ├── GenreCrudController.php
│   │   │   ├── ReviewCrudController.php
│   │   │   ├── UserCrudController.php
│   │   │   └── WishlistItemCrudController.php
│   │   ├── GameController.php
│   │   ├── GenreController.php
│   │   ├── PageController.php
│   │   ├── ReviewController.php
│   │   ├── SecurityController.php
│   │   └── WishlistController.php
│   ├── DataFixtures/
│   │   └── AppFixtures.php        ← Données de démonstration
│   ├── Entity/
│   │   ├── Editor.php
│   │   ├── Game.php               ← Avec VichUploader pour images
│   │   ├── Genre.php
│   │   ├── Review.php
│   │   ├── User.php
│   │   └── WishlistItem.php
│   ├── Form/
│   │   └── ReviewType.php
│   ├── Repository/                ← 6 repositories Doctrine
│   └── Kernel.php
├── templates/
│   ├── base.html.twig             ← Layout principal
│   ├── admin/dashboard.html.twig
│   ├── game/
│   │   ├── _game_item.html.twig   ← Carte jeu réutilisable
│   │   ├── list.html.twig
│   │   └── show.html.twig
│   ├── genre/show.html.twig
│   ├── page/
│   │   ├── index.html.twig        ← Page d'accueil
│   │   └── about.html.twig
│   ├── review/form.html.twig
│   ├── security/login.html.twig
│   └── wishlist/list.html.twig
├── .env                           ← Variables d'env (ne pas commit)
├── .env.local                     ← Config locale (à créer)
├── .env.local.example             ← Modèle de config
├── composer.json                  ← Dépendances PHP
├── INSTALL.ps1                    ← Script install Windows
└── FIX_EXISTING.ps1               ← Script correction Flex
```

---

## 4. BASE DE DONNÉES

### Tables créées

```sql
user           -- Utilisateurs (id, username, email, roles, password)
editor         -- Éditeurs de jeux (id, name)
genre          -- Genres (id, name)
game           -- Jeux (id, name, description, release_date, image_name, editor_id)
game_genre     -- Table pivot ManyToMany (game_id, genre_id)
wishlist_item  -- Wishlist (id, user_id, game_id, created_at)
review         -- Avis (id, user_id, game_id, review, comment, created_at)
```

### Relations

```
User ──< WishlistItem >── Game
User ──< Review >── Game
Game >── Editor
Game ──< game_genre >── Genre
```

### Contraintes uniques
- `wishlist_user_game_unique` : un user ne peut ajouter un jeu qu'une fois
- `review_user_game_unique` : un user ne peut noter un jeu qu'une fois

### Données de démo (fixtures)
- **6 éditeurs** : CD Projekt Red, Rockstar Games, Valve, Epic Games, Bethesda, Supercell
- **8 genres** : Action, RPG, FPS, Open World, Survival, Stratégie, Sport, Puzzle
- **12 jeux** : Cyberpunk 2077, RDR2, Witcher 3, GTA V, CS2, Skyrim, Fortnite, Rocket League, Minecraft, Half-Life 2, Clash Royale, Among Us
- **2 utilisateurs** : admin + user
- **3 wishlists** et **5 avis** de démonstration

---

## 5. PRÉREQUIS SYSTÈME

### ✅ Obligatoires (TOUS nécessaires)

| Logiciel | Version min | Vérification |
|----------|-------------|--------------|
| PHP | 8.2+ | `php -v` |
| Composer | 2.x | `composer -V` |
| MySQL | 8.0+ | via XAMPP/WAMP/MySQL |
| Symfony CLI | dernière | `symfony version` |

### Extensions PHP requises

```
ext-ctype    ← manipulation de caractères
ext-iconv    ← conversion d'encodage
ext-pdo      ← connexion base de données
ext-pdo_mysql ← driver MySQL spécifique
ext-json     ← manipulation JSON (roles User)
ext-mbstring ← chaînes multi-octets
```

### Extensions PHP optionnelles (améliore l'expérience)

```
ext-intl     ← Formatage dates dans EasyAdmin
             ← SANS cette extension : les dates s'affichent en d/m/Y
             ← AVEC cette extension : formatage localisé complet
             ← Activation : décommenter "extension=intl" dans php.ini
```

### Vérifier les extensions actives

```powershell
php -m
# ou pour une extension spécifique :
php -m | findstr intl
php -m | findstr pdo_mysql
```

---

## 6. INSTALLATION COMPLÈTE PAS À PAS

### Étape 1 — Vérifier les prérequis

```powershell
php -v          # Doit afficher PHP 8.2.x ou supérieur
composer -V     # Doit afficher Composer 2.x
symfony version # Doit afficher Symfony CLI
```

### Étape 2 — Extraire le projet

```powershell
# Extraire le ZIP dans C:\Users\VotreNom\
# Vous devez avoir le dossier : C:\Users\VotreNom\all_games\
cd C:\Users\VotreNom\all_games
```

### Étape 3 — Configurer la base de données

```powershell
# Copier le fichier de config
cp .env.local.example .env.local

# Ouvrir .env.local et modifier DATABASE_URL
# Exemple XAMPP sans mot de passe :
DATABASE_URL="mysql://root:@127.0.0.1:3306/all_games?serverVersion=8.0.32&charset=utf8mb4"

# Exemple XAMPP avec mot de passe :
DATABASE_URL="mysql://root:monmotdepasse@127.0.0.1:3306/all_games?serverVersion=8.0.32&charset=utf8mb4"

# Exemple WAMP :
DATABASE_URL="mysql://root:@127.0.0.1:3306/all_games?serverVersion=8.0.32&charset=utf8mb4"

# Exemple MAMP (Mac) :
DATABASE_URL="mysql://root:root@127.0.0.1:8889/all_games?serverVersion=8.0.32&charset=utf8mb4"
```

### Étape 4 — Démarrer MySQL

```
XAMPP : Ouvrir XAMPP Control Panel → Start Apache + MySQL
WAMP  : Clic gauche sur icône WAMP → Start All Services
MAMP  : Ouvrir MAMP → Start Servers
```

### Étape 5 — Installer les dépendances PHP

```powershell
composer install
```

> ⚠️ Si Composer affiche des warnings sur les "recipes" Symfony Flex,
> répondez **n** (No) à toutes les questions.
> Si des fichiers `ux_turbo.yaml` ou `asset_mapper.yaml` sont créés,
> supprimez-les ! (voir section 9)

### Étape 6 — Créer la base de données

```powershell
php bin/console doctrine:database:create --if-not-exists
```

### Étape 7 — Lancer les migrations

```powershell
php bin/console doctrine:migrations:migrate --no-interaction
```

> Si erreur "already executed" : normal, ignorer et continuer.

### Étape 8 — Charger les données de démo

```powershell
php bin/console doctrine:fixtures:load --no-interaction
```

> ⚠️ Cette commande **efface toutes les données existantes** et recharge les fixtures.

### Étape 9 — Vider le cache

```powershell
php bin/console cache:clear
```

### Étape 10 — Lancer le serveur

```powershell
symfony server:start
```

Ouvrir : **http://127.0.0.1:8000**

---

## 7. CONFIGURATION CRITIQUE

### Fichier `.env.local` (À créer sur chaque machine)

```bash
APP_ENV=dev
APP_SECRET=a3f8b2c1d4e5f6a7b8c9d0e1f2a3b4c5

# ← ADAPTER selon votre installation MySQL
DATABASE_URL="mysql://root:@127.0.0.1:3306/all_games?serverVersion=8.0.32&charset=utf8mb4"
```

> Ce fichier n'est **jamais** dans le ZIP / versionné (`.gitignore`).
> Il doit être créé manuellement sur chaque machine.

### Fichier `config/packages/vich_uploader.yaml`

```yaml
vich_uploader:
    db_driver: orm
    mappings:
        games:
            uri_prefix: /images/games
            upload_destination: '%kernel.project_dir%/public/images/games'
            namer: Vich\UploaderBundle\Naming\SmartUniqueNamer
```

> Le dossier `public/images/games/` doit exister et être **accessible en écriture**.

### Permissions du dossier d'upload

```powershell
# Windows : généralement pas de problème de permissions
# Linux/Mac :
chmod 777 public/images/games/
chmod 777 var/cache/
chmod 777 var/log/
```

---

## 8. EXTENSIONS PHP — ACTIVATION PAR ENVIRONNEMENT

### XAMPP (Windows)

1. Ouvrir `C:\xampp\php\php.ini` dans le Bloc-notes
2. Rechercher (Ctrl+H) et activer :

```ini
; Avant :
;extension=pdo_mysql
;extension=intl
;extension=mbstring

; Après (retirer le ;) :
extension=pdo_mysql
extension=intl
extension=mbstring
```

3. Redémarrer : XAMPP Control Panel → Stop MySQL → Start MySQL
4. Redémarrer le serveur Symfony : `Ctrl+C` puis `symfony server:start`

### WAMP (Windows)

1. Clic gauche sur icône WAMP dans la barre des tâches
2. PHP → Extensions PHP → cocher `php_pdo_mysql`, `php_intl`, `php_mbstring`
3. WAMP se redémarre automatiquement

### Homebrew PHP (Mac)

```bash
brew install php@8.2
brew install php-intl
# Ou via pecl :
pecl install intl
```

### Ubuntu/Debian (Linux)

```bash
sudo apt install php8.2-mysql php8.2-intl php8.2-mbstring php8.2-xml
sudo service apache2 restart
```

---

## 9. PROBLÈMES CONNUS ET SOLUTIONS

### ❌ Erreur : "Array to string conversion" dans Admin

**Cause** : EasyAdmin tente d'afficher un tableau PHP (ex: `roles`) comme texte.  
**Solution** : Remplacer `src/Controller/Admin/UserCrudController.php` par la version avec `ChoiceField` et badges.

---

### ❌ Erreur : "extension=intl" manquante

**Symptôme** :
```
LogicException: When using date/time fields in EasyAdmin backends, 
you must install and enable the PHP Intl extension
```
**Solution** : Activer `extension=intl` dans `php.ini` (voir section 8).  
**Alternative** : Utiliser `TextField` avec `formatValue()` à la place de `DateTimeField`.

---

### ❌ Erreur : "no extension able to load turbo"

**Symptôme** :
```
There is no extension able to load the configuration for "turbo" 
in config/packages/ux_turbo.yaml
```
**Cause** : Symfony Flex a auto-généré ce fichier lors du `composer install`.  
**Solution** :
```powershell
# Supprimer le fichier problématique :
Remove-Item config\packages\ux_turbo.yaml -Force
Remove-Item config\packages\asset_mapper.yaml -Force
Remove-Item config\packages\stimulus.yaml -Force
php bin/console cache:clear
```

---

### ❌ Erreur : "Could not open input file: bin/console"

**Cause** : Le fichier `bin/console` est absent.  
**Solution** : Vérifier que le fichier existe dans `all_games/bin/console`.  
S'il manque, le recréer depuis le ZIP original.

---

### ❌ Erreur : Connexion base de données refusée

**Symptôme** :
```
SQLSTATE[HY000] [2002] Aucune connexion n'a pu être établie
```
**Causes possibles** :
1. MySQL n'est pas démarré → Ouvrir XAMPP et démarrer MySQL
2. Mauvais port → XAMPP utilise `3306`, MAMP utilise `8889`
3. Mauvais mot de passe → Vérifier dans `.env.local`

---

### ❌ Les images uploadées disparaissent après redéploiement

**Cause** : Le dossier `public/images/games/` n'est pas dans le ZIP/Git.  
**Solution** : Sauvegarder ce dossier séparément et le restaurer après déploiement.

---

### ❌ Le CSS ne s'affiche pas (page sans style)

**Cause** : Tailwind CSS est chargé depuis CDN — pas de connexion internet.  
**Solution** :
```html
<!-- Dans base.html.twig, remplacer le CDN par une version locale -->
<!-- Télécharger tailwind.min.css sur https://tailwindcss.com/docs/installation -->
<!-- Placer dans public/css/tailwind.min.css -->
<link rel="stylesheet" href="{{ asset('css/tailwind.min.css') }}">
```

---

### ❌ Erreur 500 après transfert sur une autre machine

**Checklist** :
1. Le fichier `.env.local` existe et a le bon `DATABASE_URL`
2. MySQL est démarré
3. La base de données `all_games` existe
4. Les migrations ont été lancées : `php bin/console doctrine:migrations:migrate -n`
5. Le cache a été vidé : `php bin/console cache:clear`
6. Le dossier `vendor/` est présent (sinon : `composer install`)
7. Les permissions de `var/` et `public/images/games/` sont correctes

---

## 10. ROUTES ET FONCTIONNALITÉS

### Routes publiques

| URL | Nom | Description |
|-----|-----|-------------|
| `/` | `app_home` | Page d'accueil (3 derniers jeux) |
| `/about` | `app_about` | À propos |
| `/game/list` | `app_game_list` | Liste de tous les jeux |
| `/game/{id}` | `app_game_show` | Détail d'un jeu |
| `/genre/{id}` | `app_genre_show` | Jeux par genre |
| `/login` | `app_login` | Formulaire de connexion |
| `/logout` | `app_logout` | Déconnexion |

### Routes authentifiées (ROLE_USER)

| URL | Nom | Description |
|-----|-----|-------------|
| `/wishlist` | `app_wishlist` | Ma liste de souhaits |
| `/wishlist/{id}/toggle` | `app_game_wishlist_toggle` | Ajouter/Retirer un jeu |
| `/review/{id}` | `app_review_form` | Laisser un avis |

### Routes admin (ROLE_ADMIN)

| URL | Description |
|-----|-------------|
| `/admin` | Dashboard EasyAdmin (redirige vers liste jeux) |
| `/admin?crudController=GameCrudController` | Gestion jeux |
| `/admin?crudController=EditorCrudController` | Gestion éditeurs |
| `/admin?crudController=GenreCrudController` | Gestion genres |
| `/admin?crudController=UserCrudController` | Gestion utilisateurs |
| `/admin?crudController=WishlistItemCrudController` | Gestion wishlists |
| `/admin?crudController=ReviewCrudController` | Gestion avis |

---

## 11. COMPTES UTILISATEURS

| Rôle | Username | Mot de passe | Accès |
|------|----------|--------------|-------|
| Admin | `admin` | `admin123` | Tout + `/admin` |
| User | `user` | `user123` | Wishlist + Avis |

### Créer un nouvel admin via console

```powershell
php bin/console security:hash-password
# Entrer le mot de passe souhaité
# Copier le hash et l'insérer en BDD manuellement ou via les fixtures
```

---

## 12. CHECKLIST DÉPLOIEMENT SUR UNE NOUVELLE MACHINE

```
[ ] PHP 8.2+ installé et dans le PATH
[ ] Composer 2.x installé
[ ] Symfony CLI installé
[ ] MySQL 8.0+ installé et démarré
[ ] Extensions PHP actives : pdo_mysql, ctype, iconv, json, mbstring
[ ] (Optionnel) extension intl activée dans php.ini
[ ] Dossier all_games/ extrait
[ ] Fichier .env.local créé avec le bon DATABASE_URL
[ ] composer install exécuté
[ ] Fichiers Flex indésirables supprimés (ux_turbo.yaml, etc.)
[ ] php bin/console doctrine:database:create --if-not-exists
[ ] php bin/console doctrine:migrations:migrate -n
[ ] php bin/console doctrine:fixtures:load -n
[ ] php bin/console cache:clear
[ ] Dossier public/images/games/ existe et est accessible en écriture
[ ] symfony server:start
[ ] Tester http://127.0.0.1:8000
[ ] Tester http://127.0.0.1:8000/admin avec admin/admin123
```

---

## 📦 COMMANDES UTILES AU QUOTIDIEN

```powershell
# Démarrer le serveur
symfony server:start

# Arrêter le serveur
Ctrl+C

# Vider le cache
php bin/console cache:clear

# Recharger les données de démo (EFFACE TOUT)
php bin/console doctrine:fixtures:load -n

# Voir toutes les routes
php bin/console debug:router

# Voir la config Doctrine
php bin/console doctrine:mapping:info

# Créer une migration après modification d'entité
php bin/console make:migration

# Appliquer les migrations
php bin/console doctrine:migrations:migrate -n

# Voir les services disponibles
php bin/console debug:container
```

---

*Documentation fait pour AllGames v1.0 — Symfony 7.2*
