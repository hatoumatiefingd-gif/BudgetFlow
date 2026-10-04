# BudgetFlow

Application web de suivi de dépenses, réalisée dans le cadre du titre professionnel
**Développeur Web et Web Mobile (DWWM)**.

BudgetFlow permet à un utilisateur de suivre ses dépenses et ses revenus, de gérer ses
abonnements (dépenses récurrentes) et de recevoir des alertes sur son budget.
Un espace administrateur permet de consulter les comptes et les messages de contact.

## Fonctionnalités

**Espace utilisateur**
- Inscription, connexion, mot de passe oublié, modification du profil
- Tableau de bord : total des dépenses et des revenus du mois, budget restant,
  taux d'épargne, graphique de répartition, dernières dépenses
- Dépenses : ajout, modification, suppression, filtre par mois, année et catégorie
- Revenus : ajout, modification, suppression, filtre par mois
- Dépenses récurrentes : créées automatiquement à chaque échéance
  (mensuelle, hebdomadaire ou annuelle)
- Notifications : alertes automatiques (budget dépassé, bonne épargne, paiement à venir)

**Espace administrateur**
- Tableau de bord avec statistiques et activité récente
- Liste et recherche des utilisateurs
- Lecture des messages envoyés depuis le formulaire de contact

**Pages publiques** : accueil, contact, politique de confidentialité.

## Technologies

| Partie | Outils |
|---|---|
| Back-end | PHP 8.2+, Laravel 12, Eloquent (ORM) |
| Authentification | Laravel Breeze |
| Front-end | Blade, HTML, CSS (`public/css/style.css`), Chart.js |
| Base de données | MySQL : XAMPP et phpMyAdmin en local, base MySQL sur Railway en ligne |
| Hébergement | Railway |

## Sécurité

- Mots de passe hachés (bcrypt) et règles de complexité à l'inscription
- Protection CSRF sur tous les formulaires (`@csrf`)
- Protection XSS : Blade échappe automatiquement les données affichées avec `{{ }}`
- Chaque requête filtre sur l'utilisateur connecté (`where('idUtilisateur', auth()->id())`) :
  un utilisateur ne peut ni voir, ni modifier, ni supprimer les données d'un autre compte
- Espace admin protégé par le middleware `role:admin` (`app/Http/Middleware/RoleMiddleware.php`)
- Validation des formulaires côté serveur avec `$request->validate()`

## Organisation du code

```
app/Http/Controllers/     Contrôleurs (un par fonctionnalité)
app/Http/Middleware/      Middleware de vérification du rôle
app/Models/               Modèles Eloquent (Depense, Revenu, Categorie...)
database/migrations/      Création des tables
database/seeders/         Données de démonstration
resources/views/          Vues Blade (un dossier par fonctionnalité)
routes/web.php            Toutes les routes de l'application
public/css/style.css      Feuille de style, découpée en sections par page
```

## Base de données

| Table | Contenu |
|---|---|
| `users` | Comptes (nom, e-mail, mot de passe, rôle `utilisateur` ou `admin`) |
| `categorie` | Catégories de dépenses (Courses, Logement, Transport...) |
| `depense` | Dépenses d'un utilisateur, liées à une catégorie |
| `revenu` | Revenus d'un utilisateur |
| `depenserecurrente` | Abonnements et paiements réguliers |
| `notification` | Alertes affichées à l'utilisateur |
| `notification_admin` | Notifications pour l'administrateur |
| `contact_messages` | Messages du formulaire de contact |

## Installation en local

Prérequis : XAMPP (PHP 8.2+, Apache et MySQL), Composer, Node.js.

Avant de commencer : démarrer Apache et MySQL dans XAMPP, puis créer une base vide
nommée `budget_flow` dans phpMyAdmin.

```bash
composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate

# Dans le fichier .env, vérifier DB_DATABASE=budget_flow (utilisateur root, sans mot de passe avec XAMPP)

# Crée les tables et les données de démonstration
php artisan migrate --seed

php artisan serve
```

L'application est ensuite disponible sur http://localhost:8000.

## Déploiement sur Railway

Le site est en ligne à l'adresse : https://budgetflow-production-ead6.up.railway.app

Étapes de la mise en ligne :

1. **Relier GitHub à Railway** : sur Railway, créer un projet avec « Deploy from GitHub repo »,
   autoriser l'accès à GitHub puis choisir le dépôt BudgetFlow.
2. **Ajouter la base de données** : dans le même projet, ajouter un service **MySQL**.
   Il est séparé de la base locale de XAMPP et garde ses données dans un volume.
3. **Remplir les variables d'environnement** dans l'onglet *Variables* du service BudgetFlow
   (elles remplacent le fichier `.env`, qui n'est jamais envoyé sur GitHub) :

   | Variable | Valeur |
   |---|---|
   | `APP_ENV` | `production` |
   | `APP_DEBUG` | `false` |
   | `APP_KEY` | clé générée avec `php artisan key:generate --show` |
   | `APP_URL` | adresse du site en ligne |
   | `DB_CONNECTION` | `mysql` |
   | `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | informations de connexion données par le service MySQL de Railway |

4. **Créer les tables** : lancer les migrations sur la base en ligne avec
   `php artisan migrate --force` (l'option `--force` est obligatoire en production).
5. **Générer l'adresse du site** : dans *Settings* puis *Networking*, cliquer sur
   « Generate Domain ». Railway fournit automatiquement le HTTPS.

**Mises à jour** : à chaque fusion sur la branche `main` de GitHub, Railway récupère
le nouveau code et redéploie le site tout seul. L'historique des déploiements est
visible dans l'onglet *Deployments*.

**Limite connue** : l'offre gratuite de Railway bloque l'envoi d'e-mails, donc la fonction
« mot de passe oublié » fonctionne en local mais pas en ligne.

## Auteure

Hatouma Diawara — projet de fin de formation DWWM, 2026.
