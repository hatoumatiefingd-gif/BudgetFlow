<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - BudgetFlow</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<section class="inscription-page">

    {{-- Partie gauche : texte de présentation --}}
    <div class="inscription-left">
        <p class="badge">Créer votre compte BudgetFlow</p>

        <h1>Gérez vos finances<br>simplement.</h1>

        <p class="inscription-text">
            BudgetFlow vous aide à suivre vos dépenses, gérer vos revenus
            et organiser votre budget au quotidien.
        </p>
    </div>

    {{-- Partie droite : carte avec le formulaire d'inscription --}}
    <div class="inscription-right">
        <div class="signup-card">

            <div class="signup-logo">
                <img src="{{ asset('logo.png') }}" alt="Logo BudgetFlow">
            </div>

            <h1>Créer un compte</h1>

            {{-- Formulaire envoyé à RegisteredUserController@store. --}}
            {{-- "novalidate" désactive la vérification du navigateur : c'est Laravel qui vérifie. --}}
            <form method="POST" action="{{ route('register') }}" novalidate>
                {{-- @csrf ajoute un jeton caché qui protège contre les attaques CSRF --}}
                @csrf

                <label for="name">Nom complet</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    {{-- old('name') remet la valeur saisie si le formulaire contient une erreur --}}
                    value="{{ old('name') }}"
                    placeholder="Votre nom complet"
                >

                {{-- Affiche le message d'erreur de Laravel si le champ est invalide --}}
                @error('name')
                    <small class="message-erreur">{{ $message }}</small>
                @enderror

                <label for="email">Adresse e-mail</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="exemple@email.com"
                >

                @error('email')
                    <small class="message-erreur">{{ $message }}</small>
                @enderror
{{-- Mot de passe + rappel des règles de sécurité --}}
<label for="password">Mot de passe</label>
<input
   id="password"
   type="password"
   name="password"
   placeholder="Mot de passe"
>
<small class="message-aide">
   Minimum 8 caractères avec une majuscule, une minuscule,
   un chiffre et un caractère spécial.
</small>
@error('password')
<small class="message-erreur">{{ $message }}</small>
@enderror
                {{-- Le nom "password_confirmation" est attendu par la règle "confirmed" de Laravel --}}
                <label for="password_confirmation">Confirmer le mot de passe</label>
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    placeholder="Confirmer le mot de passe"
                >

                <button type="submit">Créer un compte</button>
            </form>

            <p class="switch">
                Vous avez déjà un compte ?
                <a href="{{ route('login') }}">Se connecter</a>
            </p>

        </div>
    </div>

</section>

</body>
</html>