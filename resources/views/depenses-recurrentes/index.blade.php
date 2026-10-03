<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dépenses récurrentes - BudgetFlow</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<div class="app">
    {{-- Menu latéral de l'espace utilisateur --}}
    <aside class="sidebar">
<div class="side-logo">
<img src="{{ asset('logo.png') }}" alt="BudgetFlow">
</div>
<nav>
<a href="{{ route('dashboard') }}">
       ▦ Tableau de bord
</a>
<a href="{{ route('depenses.index') }}">
       ↘ Dépenses
</a>
<a href="{{ route('revenus.index') }}">
       ↗ Revenus
</a>
<a class="active" href="{{ route('depenses-recurrentes.index') }}">
       ↔ Récurrentes
</a>
<a href="{{ route('notifications.index') }}">
       ♢ Notifications
</a>
</nav>
   {{-- Accès au profil utilisateur --}}
<div class="profile-link">
<a href="{{ route('profile.edit') }}">
           👤 Mon profil
</a>
</div>
   {{-- Déconnexion --}}
<div class="logout">
{{-- Déconnexion : formulaire POST protégé par @csrf --}}
<form method="POST" action="{{ route('logout') }}">
           @csrf
<button type="submit">Déconnexion</button>
</form>
</div>
</aside>

    {{-- Contenu principal de la page --}}
    <main class="content">

        <div class="rec-header">
            <div>
                <h1>Dépenses récurrentes</h1>
                <p>Gérez vos abonnements et paiements réguliers</p>
            </div>

            <a href="{{ route('depenses-recurrentes.create') }}" class="rec-add-btn">
                + Ajouter
            </a>
        </div>

        {{-- Carte du total des dépenses récurrentes --}}
        <section class="rec-total-card">
            <p>Total des dépenses récurrentes</p>

            <h2>
                {{ number_format($recurrentes->sum('montant'), 2, '.', ' ') }} €
            </h2>

            <span>par mois</span>

            <div class="rec-total-icon">↻</div>
        </section>

        <section class="rec-grid">

            {{-- @forelse affiche une carte par dépense récurrente, ou le bloc @empty s'il n'y en a aucune --}}
            @forelse($recurrentes as $recurrente)

                <div class="rec-card">

                    <div class="rec-icon">
                        {{ strtoupper(substr($recurrente->nomDepenseRecurrente ?? 'R', 0, 1)) }}
                    </div>

                    <div class="rec-info">
                        <h3>{{ ucfirst($recurrente->nomDepenseRecurrente) }}</h3>

                        <p>
                            {{ ucfirst($recurrente->categorie->nomCategorie ?? 'Catégorie') }}
                        </p>

                        <h2>
                            {{ number_format($recurrente->montant, 2, '.', ' ') }} €
                        </h2>

                        <span>{{ ucfirst($recurrente->frequence) }}</span>
                    </div>

                    <div class="rec-date">
                        <p>Prochain paiement</p>
                        <strong>{{ date('d/m/Y', strtotime($recurrente->prochaineDate)) }}</strong>
                    </div>

                    <a href="{{ route('depenses-recurrentes.edit', $recurrente->idDepenseRecurrente) }}" class="rec-menu">
                        ⋮
                    </a>

                    {{-- Suppression : @method('DELETE') car un formulaire HTML ne connaît que GET et POST --}}
                    <form action="{{ route('depenses-recurrentes.destroy', $recurrente->idDepenseRecurrente) }}" method="POST">
                        @csrf
                        @method('DELETE')

                        <button type="submit" class="rec-delete">×</button>
                    </form>

                </div>

            {{-- Message affiché quand l'utilisateur n'a aucune dépense récurrente --}}
            @empty

                <div class="rec-card">
                    <div class="rec-info">
                        <h3>Aucune dépense récurrente</h3>
                        <p>Ajoutez une dépense récurrente pour la voir ici.</p>
                    </div>
                </div>

            @endforelse

        </section>

    </main>
</div>

</body>
</html>