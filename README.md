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
| Base de données | SQLite en local, base configurée par variables d'environnement sur Railway |
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

Prérequis : PHP 8.2+, Composer, Node.js.

```bash
composer install
npm install
npm run build

cp .env.example .env
php artisan key:generate

# Crée la base SQLite, les tables et les données de démonstration
touch database/database.sqlite
php artisan migrate --seed

php artisan serve
```

L'application est ensuite disponible sur http://localhost:8000.

## Comptes de démonstration

Créés par `php artisan migrate --seed` :

| Rôle | E-mail | Mot de passe |
|---|---|---|
| Utilisateur | demo@budgetflow.fr | Demo123! |
| Administrateur | admin@budgetflow.fr | Admin123! |

## Auteure

Hatouma Diawara — projet de fin de formation DWWM, 2026.
