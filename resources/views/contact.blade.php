<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Contact - BudgetFlow</title>
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
<div class="contact-page">
<div class="contact-card">
           {{-- Titre de la page --}}
<h1>Contacter l’administrateur</h1>
<p class="contact-description">
               Si vous rencontrez un problème avec votre compte,
               vous pouvez envoyer un message à l’administrateur.
</p>
{{-- Message affiché lorsque l'envoi a réussi --}}
@if(session('success'))
<div class="contact-success">
       {{ session('success') }}
</div>
@endif
           {{-- Formulaire de contact --}}
<form method="POST" action="{{ route('contact.store') }}">
               @csrf
<div class="contact-field">
<label for="name">Nom</label>
<input
                       type="text"
                       id="name"
                       name="name"
                       placeholder="Votre nom"
                       required
>
</div>

<div class="contact-field">
<label for="email">Adresse e-mail</label>
<input
                       type="email"
                       id="email"
                       name="email"
                       placeholder="Votre adresse e-mail"
                       required
>
</div>

<div class="contact-field">
<label for="message">Message</label>
<textarea
                       id="message"
                       name="message"
                       rows="6"
                       placeholder="Expliquez votre problème..."
                       required
></textarea>
</div>

<button type="submit" class="contact-button">
                   Envoyer le message
</button>
</form>

           {{-- Retour à la page de connexion --}}
<div class="contact-retour">
<a href="{{ route('login') }}">
                   ← Retour à la connexion
</a>
</div>
</div>
</div>
</body>
</html>