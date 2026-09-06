<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Paramètres</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="admin-app">
<aside class="admin-sidebar">
<div class="admin-logo">
<strong>BudgetFlow</strong>
<span>ADMIN</span>
</div>
<nav>
<a href="{{ route('admin.dashboard') }}">▦ Tableau de bord</a>
<a href="{{ route('admin.utilisateurs') }}">👥 Utilisateurs</a>
<a href="{{ route('admin.notifications') }}">🔔 Notifications</a>
<a class="active" href="{{ route('admin.parametres') }}">⚙ Paramètres</a>
</nav>
<div class="admin-logout">
<form method="POST" action="{{ route('logout') }}">
               @csrf
<button type="submit">Déconnexion</button>
</form>
</div>
</aside>
<main class="admin-content">
<div class="admin-top-row">
<div>
<h1>Paramètres</h1>
<p>Informations générales de l’application</p>
</div>
</div>
<div class="admin-form-card">
<form>
<label>Nom du site</label>
<input type="text" value="BudgetFlow">
<label>Email de contact</label>
<input type="email" value="contact@budgetflow.com">
<label>Description</label>
<textarea class="admin-textarea">Application web de suivi des dépenses, revenus et notifications financières.</textarea>
<button type="button">Enregistrer</button>
</form>
</div>
<br>
<div class="admin-form-card">
<h2>Sécurité</h2>
<p>Modifier le mot de passe administrateur</p>
<button type="button" class="admin-password-btn">
       Modifier le mot de passe
</button>
</div>
</main>
</div>
</body>
</html>