<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
<title>Dépenses - BudgetFlow</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | CATÉGORIE PRINCIPALE
    |--------------------------------------------------------------------------
    */

    // Calcule la catégorie où l'utilisateur dépense le plus.
    $categoriePrincipale = $depenses
        ->groupBy('idCategorie')
        ->map(function ($groupe) {
            return $groupe->sum('montant');
        })
        ->sortDesc()
        ->keys()
        ->first();

    $nomCategoriePrincipale = 'Aucune';

    if ($categoriePrincipale) {

        $categorieTrouvee = $categories->firstWhere(
            'idCategorie',
            $categoriePrincipale
        );

        if ($categorieTrouvee) {
            $nomCategoriePrincipale =
                ucfirst($categorieTrouvee->nomCategorie);
        }
    }

@endphp


<div class="app">

    {{-- ==========================================
         SIDEBAR
    ========================================== --}}
    <aside class="sidebar">

        <div class="side-logo">
            <img src="{{ asset('logo.png') }}" alt="BudgetFlow">
        </div>

        <nav>

            <a href="{{ route('dashboard') }}">
                ▦ Tableau de bord
            </a>

            <a class="active" href="{{ route('depenses.index') }}">
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


        {{-- Profil utilisateur --}}
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
    <main class="content budget-expense-page">


        {{-- ==========================================
             EN-TÊTE
        ========================================== --}}
        <div class="budget-expense-header">

            <div>

                <h1>
                    Dépenses
                </h1>

                <p>
                    Suivez vos sorties et gardez le contrôle de votre budget.
                </p>

            </div>


            <a href="{{ route('depenses.create') }}"
               class="budget-expense-add">

                <span>+</span>
                Ajouter une dépense

            </a>

        </div>



        {{-- ==========================================
             CARTES RÉSUMÉ
        ========================================== --}}
        <section class="budget-expense-stats">


            {{-- Total dépensé --}}
            <div class="budget-stat-card stat-expense">

                <div class="budget-stat-icon red">
                    ↘
                </div>

                <div class="budget-stat-data">

                    <span>
                        Total dépensé
                    </span>

                    <strong class="budget-stat-red">

                        -{{ number_format(
                            $depenses->sum('montant'),
                            2,
                            '.',
                            ' '
                        ) }} €

                    </strong>

                    <small>
                        Sur la période sélectionnée
                    </small>

                </div>

            </div>


            {{-- Catégorie principale --}}
            <div class="budget-stat-card stat-category">

                <div class="budget-stat-icon blue">
                    🛒
                </div>

                <div class="budget-stat-data">

                    <span>
                        Catégorie principale
                    </span>

                    <strong>
                        {{ $nomCategoriePrincipale }}
                    </strong>

                    <small>
                        Catégorie où vous dépensez le plus
                    </small>

                </div>

            </div>


            {{-- Nombre de dépenses --}}
            <div class="budget-stat-card stat-count">

                <div class="budget-stat-icon cyan">
                    ▤
                </div>

                <div class="budget-stat-data">

                    <span>
                        Dépenses
                    </span>

                    <strong>
                        {{ $depenses->count() }}
                    </strong>

                    <small>
                        Sur la période sélectionnée
                    </small>

                </div>

            </div>

        </section>



        {{-- ==========================================
             FILTRES
        ========================================== --}}
        <section class="budget-filter-bar">

            <div class="budget-filter-title">

                <div class="budget-filter-icon">
                    ≡
                </div>

                <div>

                    <strong>
                        Filtrer vos dépenses
                    </strong>

                    <span>
                        Affinez votre recherche
                    </span>

                </div>

            </div>


            <form method="GET"
                  action="{{ route('depenses.index') }}"
                  class="budget-filter-form">


                {{-- Année --}}
                <div class="budget-filter-group">

                    <label>
                        Année
                    </label>

                    <select name="annee"
                            onchange="this.form.submit()">

                        @for($a = 2026; $a <= now()->year + 5; $a++)

                            <option value="{{ $a }}"
                                {{ $annee == $a ? 'selected' : '' }}>

                                {{ $a }}

                            </option>

                        @endfor

                    </select>

                </div>


                {{-- Mois --}}
                <div class="budget-filter-group">

                    <label>
                        Mois
                    </label>

                    <select name="mois"
                            onchange="this.form.submit()">

                        <option value="">
                            Tous les mois
                        </option>

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

                </div>


                {{-- Catégorie --}}
                <div class="budget-filter-group">

                    <label>
                        Catégorie
                    </label>

                    <select name="categorie"
                            onchange="this.form.submit()">

                        <option value="">
                            Toutes les catégories
                        </option>

                        @foreach($categories as $categorie)

                            <option value="{{ $categorie->idCategorie }}"
                                {{ request('categorie') == $categorie->idCategorie ? 'selected' : '' }}>

                                {{ ucfirst($categorie->nomCategorie) }}

                            </option>

                        @endforeach

                    </select>

                </div>

            </form>

        </section>



        {{-- ==========================================
             HISTORIQUE
        ========================================== --}}
        <section class="budget-history">


            <div class="budget-history-header">

                <div class="budget-history-title">

                    <div class="budget-history-icon">
                        ▣
                    </div>

                    <div>

                        <h2>
                            Historique des dépenses
                        </h2>

                        <p>
                            Vos dernières sorties enregistrées
                        </p>

                    </div>

                </div>


                <span class="budget-history-count">

                    {{ $depenses->count() }}
                    dépense{{ $depenses->count() > 1 ? 's' : '' }}

                </span>

            </div>



            {{-- Entête tableau --}}
            <div class="budget-history-table-head">

                <span>
                    Dépense
                </span>

                <span>
                    Catégorie
                </span>

                <span>
                    Date
                </span>

                <span>
                    Montant
                </span>

                <span>
                    Actions
                </span>

            </div>



            {{-- Liste --}}
            @forelse($depenses as $depense)

                @php

                    // Permet d'afficher une icône selon la catégorie.
                    $cat = strtolower(
                        $depense->categorie->nomCategorie ?? ''
                    );

                @endphp


                <div class="budget-history-row">


                    {{-- Dépense --}}
                    <div class="budget-history-main">

                        <div class="budget-history-row-icon">

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


                        <div>

                            <h3>
                                {{ ucfirst($depense->description) }}
                            </h3>

                            <p>
                                {{ ucfirst(
                                    $depense->categorie->nomCategorie
                                    ?? 'Autre'
                                ) }}
                            </p>

                        </div>

                    </div>



                    {{-- Catégorie --}}
                    <div>

                        <span class="budget-category-pill">

                            {{ ucfirst(
                                $depense->categorie->nomCategorie
                                ?? 'Autre'
                            ) }}

                        </span>

                    </div>



                    {{-- Date --}}
                    <div class="budget-history-date">

                        <span>
                            □
                        </span>

                        {{ date(
                            'd/m/Y',
                            strtotime(
                                $depense->dateDepense
                            )
                        ) }}

                    </div>



                    {{-- Montant --}}
                    <div class="budget-history-amount">

                        -{{ number_format(
                            $depense->montant,
                            2,
                            '.',
                            ' '
                        ) }} €

                    </div>



                    {{-- Actions --}}
                    <div class="budget-history-actions">


                        <a href="{{ route(
                                'depenses.edit',
                                $depense->idDepense
                            ) }}"
                           class="budget-edit-btn"
                           title="Modifier">

                            ✎

                        </a>


                        <form action="{{ route(
                                    'depenses.destroy',
                                    $depense->idDepense
                                ) }}"
                              method="POST">

                            @csrf
                            @method('DELETE')

                            <button type="submit"
                                    class="budget-delete-btn"
                                    title="Supprimer"
                                    onclick="return confirm('Voulez-vous vraiment supprimer cette dépense ?')">

                                ♧

                            </button>

                        </form>

                    </div>

                </div>


            @empty


                <div class="budget-empty">

                    <h3>
                        Aucune dépense trouvée
                    </h3>

                    <p>
                        Aucune dépense ne correspond aux filtres choisis.
                    </p>

                </div>


            @endforelse

        </section>

    </main>

</div>

</body>

</html>