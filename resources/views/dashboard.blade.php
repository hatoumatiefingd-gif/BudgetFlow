<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">

    <title>Tableau de bord - BudgetFlow</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | CALCULS DU TABLEAU DE BORD
    |--------------------------------------------------------------------------
    */

    // Calcule le pourcentage du budget utilisé.
    $depensesPourcentage = $totalRevenus > 0
        ? min(($totalDepenses / $totalRevenus) * 100, 100)
        : 0;

    // Calcule le taux d'épargne réel du mois.
    $tauxEpargne = $totalRevenus > 0
        ? max(($budgetRestant / $totalRevenus) * 100, 0)
        : 0;

    // Détermine l'état actuel du budget.
    if ($budgetRestant > 0) {

        $etatBudget = 'Budget sain';
        $etatClass = 'good';

    } elseif ($budgetRestant == 0) {

        $etatBudget = 'Budget équilibré';
        $etatClass = 'neutral';

    } else {

        $etatBudget = 'Budget en alerte';
        $etatClass = 'danger';
    }

@endphp


<div class="app">

    {{-- ==========================================
         BARRE LATÉRALE
    ========================================== --}}

    <aside class="sidebar">

        <div class="side-logo">
            <img src="{{ asset('logo.png') }}" alt="BudgetFlow">
        </div>


        <nav>

            <a class="active" href="{{ route('dashboard') }}">
                ▦ Tableau de bord
            </a>

            <a href="{{ route('depenses.index') }}">
                ↘ Dépenses
            </a>

            <a href="{{ route('revenus.index') }}">
                ↗ Revenus
            </a>

            <a href="{{ route('depenses-recurrentes.index') }}">
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

            <form method="POST" action="{{ route('logout') }}">

                @csrf

                <button type="submit">
                    Déconnexion
                </button>

            </form>

        </div>

    </aside>


    {{-- ==========================================
         CONTENU PRINCIPAL
    ========================================== --}}

    <main class="content">


        {{-- En-tête --}}
        <div class="dash-header">

            <span class="dash-date">
                {{ now()->format('d/m/Y') }}
            </span>

            <h1>
                Tableau de bord
            </h1>

            <p>
                Votre situation financière est mise à jour automatiquement.
            </p>
{{-- Filtre permettant de consulter le tableau de bord par mois --}}
<form method="GET"
      action="{{ route('dashboard') }}"
      class="dashboard-month-filter">

    <select name="mois" onchange="this.form.submit()">

        <option value="1" {{ $mois == 1 ? 'selected' : '' }}>Janvier</option>
        <option value="2" {{ $mois == 2 ? 'selected' : '' }}>Février</option>
        <option value="3" {{ $mois == 3 ? 'selected' : '' }}>Mars</option>
        <option value="4" {{ $mois == 4 ? 'selected' : '' }}>Avril</option>
        <option value="5" {{ $mois == 5 ? 'selected' : '' }}>Mai</option>
        <option value="6" {{ $mois == 6 ? 'selected' : '' }}>Juin</option>
        <option value="7" {{ $mois == 7 ? 'selected' : '' }}>Juillet</option>
        <option value="8" {{ $mois == 8 ? 'selected' : '' }}>Août</option>
        <option value="9" {{ $mois == 9 ? 'selected' : '' }}>Septembre</option>
        <option value="10" {{ $mois == 10 ? 'selected' : '' }}>Octobre</option>
        <option value="11" {{ $mois == 11 ? 'selected' : '' }}>Novembre</option>
        <option value="12" {{ $mois == 12 ? 'selected' : '' }}>Décembre</option>

    </select>

    <select name="annee" onchange="this.form.submit()">

        @for($a = 2026; $a <= now()->year + 5; $a++)

            <option value="{{ $a }}"
                {{ $annee == $a ? 'selected' : '' }}>

                {{ $a }}

            </option>

        @endfor

    </select>

</form>
        </div>



        {{-- ==========================================
             RÉSUMÉ FINANCIER
        ========================================== --}}

        <section class="bf-summary-strip">


            {{-- Dépenses --}}
            <div class="bf-summary-item expense">

                <div class="bf-summary-icon">
                    ↘
                </div>

                <div>

                    <span>
                        Dépenses
                    </span>

                    <strong>
                        {{ number_format($totalDepenses, 2, '.', ' ') }} €
                    </strong>

                </div>

            </div>


            <div class="bf-summary-divider"></div>


            {{-- Revenus --}}
            <div class="bf-summary-item income">

                <div class="bf-summary-icon">
                    ↗
                </div>

                <div>

                    <span>
                        Revenus
                    </span>

                    <strong>
                        {{ number_format($totalRevenus, 2, '.', ' ') }} €
                    </strong>

                </div>

            </div>


            <div class="bf-summary-divider"></div>


            {{-- Solde --}}
            <div class="bf-summary-item balance">

                <div class="bf-summary-icon">
                    {{ $budgetRestant < 0 ? '!' : '€' }}
                </div>

                <div>

                    <span>
                        Solde du mois
                    </span>

                    <strong class="{{ $budgetRestant < 0 ? 'negative-value' : '' }}">

                        {{ number_format(
                            $budgetRestant,
                            2,
                            '.',
                            ' '
                        ) }} €

                    </strong>

                </div>

            </div>

        </section>



        {{-- ==========================================
             ANALYSE DU BUDGET
        ========================================== --}}

        <section class="bf-analysis-card">


            {{-- En-tête de l'analyse --}}
            <div class="bf-analysis-header">

                <div>

                    <p class="bf-small-title">
                        Analyse du mois
                    </p>

                    <h2>
                        Répartition du budget
                    </h2>

                    <span>
                        Vue d’ensemble de votre situation financière ce mois-ci.
                    </span>

                </div>


                {{-- État dynamique du budget --}}
                <div class="bf-status {{ $etatClass }}">

                    @if($etatClass === 'danger')

                        ⚠ En alerte

                    @elseif($etatClass === 'neutral')

                        @if($totalRevenus == 0 && $totalDepenses == 0)
                            Aucune donnée
                        @else
                            Budget équilibré
                        @endif

                    @else

                        ✓ Budget sain

                    @endif

                </div>

            </div>



            <div class="bf-analysis-grid">


                {{-- ==========================================
                     GRAPHIQUE DONUT
                ========================================== --}}

                <div class="bf-chart-column">

                    <div class="bf-donut-wrapper">

                        <canvas id="budgetChart"></canvas>


                        {{-- Informations au centre du graphique --}}
                        <div class="bf-donut-center">

                            <strong>
                                {{ number_format(
                                    $depensesPourcentage,
                                    0
                                ) }}%
                            </strong>

                            <span>
                                utilisé
                            </span>

                        </div>

                    </div>

                </div>



                {{-- ==========================================
                     INDICATEURS
                ========================================== --}}

                <div class="bf-indicators">


                    {{-- Budget utilisé --}}
                    <div class="bf-indicator">

                        <span class="bf-indicator-dot used"></span>

                        <div>

                            <p>
                                Budget utilisé
                            </p>

                            <strong>
                                {{ number_format(
                                    $totalDepenses,
                                    2,
                                    '.',
                                    ' '
                                ) }} €
                            </strong>

                        </div>

                    </div>



                    {{-- Budget restant --}}
                    <div class="bf-indicator">

                        <span class="bf-indicator-dot remaining"></span>

                        <div>

                            <p>
                                Budget restant
                            </p>

                            <strong class="{{ $budgetRestant < 0 ? 'negative-value' : '' }}">

                                {{ number_format(
                                    $budgetRestant,
                                    2,
                                    '.',
                                    ' '
                                ) }} €

                            </strong>

                        </div>

                    </div>



                    {{-- Taux d'épargne --}}
                    <div class="bf-indicator">

                        <div class="bf-small-icon saving">
                            %
                        </div>

                        <div>

                            <p>
                                Taux d’épargne
                            </p>

                            <strong>
                                {{ number_format(
                                    $tauxEpargne,
                                    0
                                ) }}%
                            </strong>

                        </div>

                    </div>



                    {{-- Catégorie principale --}}
                    <div class="bf-indicator">

                        <div class="bf-small-icon category">
                            ◇
                        </div>

                        <div>

                            <p>
                                Catégorie principale
                            </p>

                            <strong>

                                {{ $categoriePrincipale
                                    && $categoriePrincipale->categorie

                                    ? ucfirst(
                                        $categoriePrincipale
                                            ->categorie
                                            ->nomCategorie
                                    )

                                    : 'Aucune'
                                }}

                            </strong>

                        </div>

                    </div>

                </div>



                {{-- ==========================================
                     MESSAGE DYNAMIQUE
                ========================================== --}}

                {{-- Message dynamique selon la situation du budget --}}
<div class="bf-budget-message {{ $etatClass }}">

    @if($etatClass === 'danger')

        {{-- Budget dépassé --}}
        <div class="bf-message-icon">!</div>

        <p>
            Vos dépenses dépassent vos revenus de

            <strong>
                {{ number_format(abs($budgetRestant), 2, '.', ' ') }} €
            </strong>

            ce mois-ci.
        </p>


    @elseif($etatClass === 'neutral')

        @if($totalRevenus == 0 && $totalDepenses == 0)

            {{-- Aucun mouvement pour le mois sélectionné --}}
            <div class="bf-message-icon">○</div>

            <p>
                Aucune donnée enregistrée pour ce mois.
            </p>

        @else

            {{-- Revenus et dépenses exactement égaux --}}
            <div class="bf-message-icon">=</div>

            <p>
                Votre budget est parfaitement équilibré ce mois-ci.
            </p>

        @endif


    @else

        {{-- Budget positif --}}
        <div class="bf-message-icon">✓</div>

        <p>
            Il vous reste

            <strong>
                {{ number_format($budgetRestant, 2, '.', ' ') }} €
            </strong>

            disponibles ce mois-ci.
        </p>

    @endif

</div>

            </div>

        </section>



        {{-- ==========================================
             BAS DU TABLEAU DE BORD
        ========================================== --}}

        <section class="dash-bottom">


            {{-- ==========================================
                 DERNIÈRES DÉPENSES
            ========================================== --}}

            <div class="dash-panel">


                <div class="panel-title">

                    <h2>
                        Dernières dépenses
                    </h2>

                    <a href="{{ route('depenses.index') }}">
                        Voir toutes ›
                    </a>

                </div>



                @forelse($depenses as $depense)


                    <div class="dash-row">


                        {{-- Icône selon la catégorie --}}
                        <div class="row-icon">


                            @php

                                $cat = strtolower(
                                    $depense->categorie->nomCategorie ?? ''
                                );

                            @endphp


                            @if($cat == 'courses')

                                🛒

                            @elseif($cat == 'loisirs')

                                🎮

                            @elseif($cat == 'restaurant')

                                🍽️

                            @elseif($cat == 'transport')

                                🚗

                            @elseif($cat == 'santé')

                                🏥

                            @elseif($cat == 'logement')

                                🏠

                            @else

                                💳

                            @endif


                        </div>



                        {{-- Informations de la dépense --}}
                        <div class="row-info">


                            <h3>
                                {{ ucfirst($depense->description) }}
                            </h3>


                            <p>

                                {{ date(
                                    'd/m/Y',
                                    strtotime(
                                        $depense->dateDepense
                                    )
                                ) }}

                            </p>


                        </div>



                        {{-- Montant --}}
                        <strong class="row-amount">

                            -{{ number_format(
                                $depense->montant,
                                2,
                                '.',
                                ' '
                            ) }} €

                        </strong>


                    </div>


                @empty


                    <p>
                        Aucune dépense récente.
                    </p>


                @endforelse


            </div>



            {{-- ==========================================
                 ALERTES RÉCENTES
            ========================================== --}}

            <div class="dash-panel">


                <div class="panel-title">

                    <h2>
                        Alertes
                    </h2>


                    <a href="{{ route('notifications.index') }}">
                        Voir toutes ›
                    </a>

                </div>



                @forelse($notifications as $notification)


                    <div class="alert-line">


                        {{-- Icône selon le type --}}
                        <div class="alert-icon">


                            @if($notification->type == 'Alerte')

                                ⚠

                            @elseif($notification->type == 'Paiement')

                                ♢

                            @elseif($notification->type == 'Réussite')

                                ✓

                            @else

                                i

                            @endif


                        </div>



                        <div>


                            <h3>
                                {{ $notification->titre }}
                            </h3>


                            <p>
                                {{ $notification->message }}
                            </p>


                            <small>

                                {{ date(
                                    'd/m/Y',
                                    strtotime(
                                        $notification->dateNotification
                                    )
                                ) }}

                            </small>


                        </div>


                    </div>


                @empty


                    <p>
                        Aucune alerte pour le moment.
                    </p>


                @endforelse


            </div>


        </section>


    </main>

</div>


{{-- Bibliothèque graphique recommandée dans le cahier des charges --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Données réelles calculées depuis la base de données.
    const revenus = {{ $totalRevenus }};
    const depenses = {{ $totalDepenses }};

    // Montant restant du budget.
    const restant = Math.max(revenus - depenses, 0);

    // Montant utilisé sans dépasser le montant total des revenus.
    const utilise = revenus > 0
        ? Math.min(depenses, revenus)
        : 0;

    // Permet de garder le donut visible lorsqu'il n'y a aucune donnée.
    const aucuneDonnee = revenus === 0 && depenses === 0;

    // Couleur du budget selon le niveau de dépenses.
    let couleurBudget = '#4f46e5';

    if (revenus > 0) {
        const pourcentage = (depenses / revenus) * 100;

        if (pourcentage >= 100) {
            couleurBudget = '#ef4444';
        } else if (pourcentage >= 80) {
            couleurBudget = '#f59e0b';
        } else {
            couleurBudget = '#4f46e5';
        }
    }

    const graphique = document.getElementById('budgetChart');

    new Chart(graphique, {
        type: 'doughnut',

        data: {
            labels: aucuneDonnee
                ? ['Aucune donnée']
                : ['Budget utilisé', 'Budget restant'],

            datasets: [{
                // À 0 donnée, on force un cercle gris complet.
                data: aucuneDonnee
                    ? [1]
                    : [utilise, restant],

                backgroundColor: aucuneDonnee
                    ? ['#d7deea']
                    : [couleurBudget, '#e8edf5'],

                borderWidth: 0,
                hoverOffset: 4
            }]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '74%',

            plugins: {
                legend: {
                    display: false
                },

                tooltip: {
                    enabled: !aucuneDonnee,

                    callbacks: {
                        label: function(context) {
                            return context.label
                                + ' : '
                                + Number(context.raw).toFixed(2)
                                + ' €';
                        }
                    }
                }
            }
        }
    });
</script>

</body>

</html>
