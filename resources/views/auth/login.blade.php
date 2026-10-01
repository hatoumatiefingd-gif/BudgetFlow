<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - BudgetFlow</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>

<body class="connexion-body">

{{-- Page de connexion : présentation à gauche, formulaire à droite --}}
<section class="connexion-page">

    <div class="connexion-left">

        <img src="{{ asset('logo.png') }}" alt="Logo BudgetFlow">

        <h1>Bienvenue</h1>

        <p>
            Connectez-vous à votre espace BudgetFlow
            pour suivre vos dépenses, revenus et objectifs.
        </p>

    </div>

    <div class="connexion-form">

        <h2>Connexion</h2>

        <p>Accédez à votre compte personnel</p>

        {{-- Formulaire envoyé à AuthenticatedSessionController@store --}}
        <form method="POST" action="{{ route('login') }}">
            {{-- Jeton de sécurité obligatoire pour les formulaires POST (protection CSRF) --}}
            @csrf

            <label>Email</label>

            <input
                type="email"
                name="email"
                placeholder="votre.email@exemple.com"
                required
            >
            {{-- Affiche les erreurs de connexion : mauvais mot de passe ou compte désactivé --}}
{{-- Affiche les erreurs de connexion --}}
@error('email')
<p class="message-erreur">
       {{ $message }}
</p>
  
@enderror

            <label>Mot de passe</label>

            <input
                type="password"
                name="password"
                placeholder="********"
                required
            >

          <div class="forgot-password">
<a href="{{ route('password.request') }}">

        Mot de passe oublié ?
</a>
</div>
 
            <button type="submit" class="btn-login">
                Se connecter
            </button>

        </form>

        <p class="switch">
            Pas encore de compte ?

            <a href="{{ route('register') }}">
                S’inscrire
            </a>
        </p>

    </div>

</section>

</body>
</html>