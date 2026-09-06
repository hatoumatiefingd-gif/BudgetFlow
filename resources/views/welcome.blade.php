<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BudgetFlow</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<header>
    <div class="logo">
       <img src="{{ asset('logo.png') }}" alt="BudgetFlow">
    </div>

    <nav>
        <a href="{{ route('register') }}" class="btn-inscription">S'inscrire</a>
        <a href="{{ route('login') }}" class="btn-connexion">Se connecter</a>
    </nav>
</header>

<section class="accueil">

    <div class="accueil-texte">
        <p class="badge">Gestion de budget personnel</p>

        <h1>
            Prenez le contrôle<br>
            de votre budget.
        </h1>

        <p class="texte-accueil">
            BudgetFlow vous accompagne dans le suivi de vos dépenses,
            la gestion de vos revenus et l’organisation de vos finances au quotidien.
        </p>

        <div class="boutons-accueil">
            <a href="{{ route('register') }}" class="btn-principal">
                Commencer maintenant
            </a>

            <a href="{{ route('login') }}" class="btn-secondaire">
                Se connecter
            </a>
        </div>
    </div>

    <div class="illustration">
       <img src="{{ asset('finance.png') }}" alt="Illustration BudgetFlow">
    </div>

</section>

<section class="avantages" id="avantages">

    <p class="titre-section">NOS AVANTAGES</p>

    <h2>Pourquoi choisir BudgetFlow ?</h2>

    <p class="sous-titre">
        Une solution simple et moderne pour mieux gérer vos finances au quotidien.
    </p>

    <div class="carte">
        <h3>Suivi clair</h3>
        <p>
            Visualisez vos revenus, dépenses et votre budget en quelques clics.
        </p>
    </div>

    <div class="carte">
        <h3>Organisation simple</h3>
        <p>
            Classez facilement vos opérations pour mieux comprendre vos habitudes.
        </p>
    </div>

    <div class="carte">
        <h3>Gestion complète</h3>
        <p>
            Gérez revenus, dépenses, paiements récurrents et notifications.
        </p>
    </div>

</section>
<div class="footer-politique">
<a href="{{ route('politique.confidentialite') }}">
       Politique de confidentialité
</a>
</div>
</body>
</html>