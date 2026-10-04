<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Messages de contact - BudgetFlow</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>
<div class="admin-app">
   {{-- Menu latéral administrateur --}}
{{-- Menu latéral de l'espace administrateur --}}
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
{{-- Contenu principal : liste des messages reçus --}}
<main class="admin-content">
<div class="admin-top">
<h1>Messages de contact</h1>
<p>Messages envoyés par les utilisateurs.</p>
</div>
       {{-- Si aucun message n'a été reçu, on affiche un texte à la place du tableau --}}
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
                       {{-- Une ligne du tableau par message --}}
                       @foreach($messages as $message)
<tr>
<td>{{ $message->name }}</td>
<td>{{ $message->email }}</td>
<td>{{ $message->message }}</td>
<td>
                                   {{-- format('d/m/Y') affiche la date à la française, ex : 04/10/2026 --}}
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