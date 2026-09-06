<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Utilisateurs</title>
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
<a href="{{ route('admin.dashboard') }}">
       ▦ Tableau de bord
</a>
<a class="active" href="{{ route('admin.utilisateurs') }}">
       👥 Utilisateurs
</a>
<a href="{{ route('admin.messages.contact') }}">
       ✉ Messages
</a>
</nav>
<div class="admin-logout">
<form method="POST" action="{{ route('logout') }}">
               @csrf
<button type="submit">
                   Déconnexion
</button>
</form>
</div>
</aside>

<main class="admin-content">
<div class="admin-top-row">
<div>
<h1>Utilisateurs</h1>
<p>
                   Consultation des comptes inscrits
</p>
</div>
</div>

       {{-- Statistiques générales sur les comptes --}}
<section class="admin-stats">
<div class="admin-stat-card">
<div class="admin-icon blue">👥</div>
<div>
<p>Total utilisateurs</p>
<h2>{{ $totalUtilisateurs }}</h2>
<span>
                       Tous les comptes
</span>
</div>
</div>

<div class="admin-stat-card">
<div class="admin-icon green">✓</div>
<div>
<p>Utilisateurs</p>
<h2>{{ $comptesActifs }}</h2>
<span>
                       Comptes utilisateurs
</span>
</div>
</div>

<div class="admin-stat-card">
<div class="admin-icon red">★</div>
<div>
<p>Administrateurs</p>
<h2>{{ $comptesAdmins }}</h2>
<span>
                       Compte administrateur
</span>
</div>
</div>
</section>

       {{-- Permet à l'administrateur de rechercher un utilisateur --}}
<form
           method="GET"
           action="{{ route('admin.utilisateurs') }}"
           class="admin-search"
>
<input
               type="text"
               name="recherche"
               value="{{ $recherche }}"
               placeholder="Rechercher un utilisateur..."
>
<button type="submit">
               Rechercher
</button>
</form>

       {{-- Liste des utilisateurs inscrits --}}
<div class="admin-table-card">
<table class="admin-table">
<thead>
<tr>
<th>Nom complet</th>
<th>Email</th>
<th>Rôle</th>
<th>Inscription</th>
</tr>
</thead>

<tbody>
                   @forelse($utilisateurs as $user)
<tr>
<td>
                               {{ $user->name }}
</td>
<td>
                               {{ $user->email }}
</td>
<td>
<span class="admin-badge">
                                   {{ ucfirst($user->role) }}
</span>
</td>
<td>
                               {{ $user->created_at
                                   ? $user->created_at->format('d/m/Y')
                                   : '-' }}
</td>
</tr>
                   @empty
<tr>
<td colspan="4">
                               Aucun utilisateur trouvé.
</td>
</tr>
                   @endforelse
</tbody>
</table>
</div>
</main>
</div>
</body>
</html>