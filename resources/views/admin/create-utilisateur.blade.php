<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Ajouter un utilisateur</title>
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
<a class="active" href="{{ route('admin.utilisateurs') }}">👥 Utilisateurs</a>
<a href="{{ route('admin.notifications') }}">🔔 Notifications</a>
<a href="{{ route('admin.parametres') }}">⚙ Paramètres</a>
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
<h1>Ajouter un utilisateur</h1>
<p>Créer un nouveau compte</p>
</div>
<a href="{{ route('admin.utilisateurs') }}" class="admin-add-btn">
               ← Retour
</a>
</div>
<div class="admin-form-card">
<form method="POST" action="{{ route('admin.utilisateurs.store') }}">
               @csrf
<label>Nom complet</label>
<input type="text" name="name" required>
<label>Email</label>
<input type="email" name="email" required>
<label>Mot de passe</label>
<input type="password" name="password" required>
<label>Rôle</label>
<select name="role" required>
<option value="utilisateur">Utilisateur</option>
<option value="admin">Administrateur</option>
</select>
<button type="submit">Enregistrer</button>
</form>
</div>
</main>
</div>
</body>
</html>