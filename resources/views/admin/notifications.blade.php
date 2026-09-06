<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Notifications</title>
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
<a class="active" href="{{ route('admin.notifications') }}">🔔 Notifications</a>
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
<h1>Notifications</h1>
<p>Toutes les notifications générées par l’application</p>
</div>
</div>
<div class="admin-table-card">
<table class="admin-table">
<thead>
<tr>
<th>Titre</th>
<th>Message</th>
<th>Type</th>
<th>Date</th>
</tr>
</thead>
<tbody>

                    @forelse($notifications as $notification)
<tr>
<td>{{ $notification->titre }}</td>
<td>{{ $notification->message }}</td>
<td>
<span class="admin-badge">

                                    {{ $notification->type }}
</span>
</td>
<td>{{ date('d/m/Y', strtotime($notification->dateNotification)) }}</td>
</tr>

                    @empty
<tr>
<td colspan="4">Aucune notification trouvée.</td>
</tr>

                    @endforelse
</tbody>
</table>
</div>
</main>
</div>
</body>
</html>
 