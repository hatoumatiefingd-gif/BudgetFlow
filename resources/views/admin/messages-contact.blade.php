<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Messages de contact - BudgetFlow</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="admin-app">
   {{-- Menu latéral administrateur --}}
<aside class="admin-sidebar">
<div class="admin-logo">
<strong>BudgetFlow</strong>
<span>ADMIN</span>
</div>
<nav>
<a href="{{ route('admin.dashboard') }}">
               ▦ Tableau de bord
</a>
<a href="{{ route('admin.utilisateurs') }}">
               👥 Utilisateurs
</a>
<a class="active" href="{{ route('admin.messages.contact') }}">
               ✉ Messages
</a>
</nav>
<div class="admin-logout">
<form method="POST" action="{{ route('logout') }}">
               @csrf
<button type="submit">Déconnexion</button>
</form>
</div>
</aside>
<main class="admin-content">
<div class="admin-top">
<h1>Messages de contact</h1>
<p>Messages envoyés par les utilisateurs.</p>
</div>
       @if($messages->isEmpty())
<div class="message-vide">
               Aucun message reçu pour le moment.
</div>
       @else
<div class="messages-table-container">
<table class="messages-table">
<thead>
<tr>
<th>Nom</th>
<th>Email</th>
<th>Message</th>
<th>Date</th>
</tr>
</thead>
<tbody>
                       @foreach($messages as $message)
<tr>
<td>{{ $message->name }}</td>
<td>{{ $message->email }}</td>
<td>{{ $message->message }}</td>
<td>
                                   {{ $message->created_at->format('d/m/Y H:i') }}
</td>
</tr>
                       @endforeach
</tbody>
</table>
</div>
       @endif
</main>
</div>
</body>
</html>