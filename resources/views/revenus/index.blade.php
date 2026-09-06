<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Revenus - BudgetFlow</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>

<body>

<div class="app">

    {{-- ==========================================
         BARRE LATÉRALE
    ========================================== --}}

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

            <a class="active" href="{{ route('revenus.index') }}">
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
        <div class="revenu-header">

            <div>

                <h1>
                    Gestion des revenus
                </h1>

                <p>
                    Suivez tous vos revenus
                </p>

            </div>


            <a href="{{ route('revenus.create') }}"
               class="revenu-add-btn">

                + Ajouter un revenu

            </a>

        </div>


        {{-- ==========================================
             FILTRE MOIS / ANNÉE
        ========================================== --}}

        <form method="GET"
              action="{{ route('revenus.index') }}"
              class="dep-filter-modern">

            <strong>
                Afficher :
            </strong>


            {{-- Sélection du mois --}}
            <select name="mois"
                    onchange="this.form.submit()">

                <option value="1" {{ $mois == 1 ? 'selected' : '' }}>
                    Janvier
                </option>

                <option value="2" {{ $mois == 2 ? 'selected' : '' }}>
                    Février
                </option>

                <option value="3" {{ $mois == 3 ? 'selected' : '' }}>
                    Mars
                </option>

                <option value="4" {{ $mois == 4 ? 'selected' : '' }}>
                    Avril
                </option>

                <option value="5" {{ $mois == 5 ? 'selected' : '' }}>
                    Mai
                </option>

                <option value="6" {{ $mois == 6 ? 'selected' : '' }}>
                    Juin
                </option>

                <option value="7" {{ $mois == 7 ? 'selected' : '' }}>
                    Juillet
                </option>

                <option value="8" {{ $mois == 8 ? 'selected' : '' }}>
                    Août
                </option>

                <option value="9" {{ $mois == 9 ? 'selected' : '' }}>
                    Septembre
                </option>

                <option value="10" {{ $mois == 10 ? 'selected' : '' }}>
                    Octobre
                </option>

                <option value="11" {{ $mois == 11 ? 'selected' : '' }}>
                    Novembre
                </option>

                <option value="12" {{ $mois == 12 ? 'selected' : '' }}>
                    Décembre
                </option>

            </select>


          {{-- Affiche uniquement 2026 et les années suivantes --}}
<select name="annee" onchange="this.form.submit()">

    @for($a = 2026; $a <= now()->year + 5; $a++)

        <option value="{{ $a }}"
            {{ $annee == $a ? 'selected' : '' }}>

            {{ $a }}

        </option>

    @endfor

</select>

        </form>


        {{-- ==========================================
             TOTAL DES REVENUS DU MOIS CHOISI
        ========================================== --}}

        <section class="revenu-total-card">

            <p>
                Total des revenus du mois sélectionné
            </p>

            <h2>

                +{{ number_format(
                    $revenus->sum('montant'),
                    2,
                    '.',
                    ' '
                ) }} €

            </h2>

            <div class="revenu-progress"></div>

        </section>


        {{-- ==========================================
             LISTE DES REVENUS
        ========================================== --}}

        <section class="revenu-grid">


            @forelse($revenus as $revenu)


                <div class="revenu-card">


                    {{-- Première lettre de la source --}}
                    <div class="letter blue">

                        {{ strtoupper(
                            substr(
                                $revenu->source,
                                0,
                                1
                            )
                        ) }}

                    </div>


                    {{-- Modification --}}
                    <a href="{{ route(
                            'revenus.edit',
                            $revenu->idRevenu
                        ) }}"
                       class="edit-icon">

                        ⋮

                    </a>


                    {{-- Suppression --}}
                    <form action="{{ route(
                                'revenus.destroy',
                                $revenu->idRevenu
                            ) }}"
                          method="POST">

                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="delete-icon"
                                onclick="return confirm('Voulez-vous vraiment supprimer ce revenu ?')">

                            ×

                        </button>

                    </form>


                    {{-- Source --}}
                    <h3>
                        {{ ucfirst($revenu->source) }}
                    </h3>


                    {{-- Date --}}
                    <p>

                        {{ date(
                            'd/m/Y',
                            strtotime($revenu->dateRevenu)
                        ) }}

                    </p>


                    {{-- Montant --}}
                    <h2>

                        +{{ number_format(
                            $revenu->montant,
                            2,
                            '.',
                            ' '
                        ) }} €

                    </h2>


                </div>


            @empty


                <div class="revenu-card">

                    <h3>
                        Aucun revenu enregistré
                    </h3>

                    <p>
                        Aucun revenu pour le mois sélectionné.
                    </p>

                </div>


            @endforelse


        </section>


    </main>

</div>

</body>

</html>