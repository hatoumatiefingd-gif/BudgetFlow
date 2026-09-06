<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - BudgetFlow</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<section class="inscription-page">

    <div class="inscription-left">
        <p class="badge">Créer votre compte BudgetFlow</p>

        <h1>Gérez vos finances<br>simplement.</h1>

        <p class="inscription-text">
            BudgetFlow vous aide à suivre vos dépenses, gérer vos revenus
            et organiser votre budget au quotidien.
        </p>
    </div>

    <div class="inscription-right">
        <div class="signup-card">

            <div class="signup-logo">
                <img src="{{ asset('logo.png') }}" alt="Logo BudgetFlow">
            </div>

            <h1>Créer un compte</h1>

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                <label for="name">Nom complet</label>
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Votre nom complet"
                >

                @error('name')
                    <small style="color:red;">{{ $message }}</small>
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
                    <small style="color:red;">{{ $message }}</small>
                @enderror
<label for="password">Mot de passe</label>
<input
   id="password"
   type="password"
   name="password"
   placeholder="Mot de passe"
>
<small style="color:#666;">
   Minimum 8 caractères avec une majuscule, une minuscule,
   un chiffre et un caractère spécial.
</small>
@error('password')
<small style="color:red;">{{ $message }}</small>
@enderror
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