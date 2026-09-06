<!DOCTYPE html>

<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Admin - Tableau de bord
    </title>

    {{-- CSS principal de BudgetFlow --}}
    <link rel="stylesheet"
          href="{{ asset('css/style.css') }}">

</head>


<body>

<div class="admin-app">


    {{-- ==========================================
         MENU LATÉRAL ADMINISTRATEUR
    ========================================== --}}

    <aside class="admin-sidebar">


        {{-- Logo administrateur --}}
        <div class="admin-logo">

            <strong>
                BudgetFlow
            </strong>

            <span>
                ADMIN
            </span>

        </div>


        {{-- Navigation administrateur --}}
        <nav>

            <a class="active"
               href="{{ route('admin.dashboard') }}">

                ▦ Tableau de bord

            </a>


            <a href="{{ route('admin.utilisateurs') }}">

                👥 Utilisateurs

            </a>


            <a href="{{ route('admin.messages.contact') }}">

                ✉ Messages

            </a>

        </nav>


        {{-- Déconnexion de l'administrateur --}}
        <div class="admin-logout">

            <form method="POST"
                  action="{{ route('logout') }}">

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

    <main class="admin-content">


        {{-- ==========================================
             EN-TÊTE
        ========================================== --}}

        <div class="admin-top">

            <div>

                <h1>
                    Tableau de bord administrateur
                </h1>

                <p>
                    Supervision générale de BudgetFlow
                </p>

            </div>

        </div>



        {{-- ==========================================
             STATISTIQUES
        ========================================== --}}

        <section class="admin-stats">


            {{-- Total des comptes --}}
            <div class="admin-stat-card">

                <div class="admin-icon blue">

                    👥

                </div>


                <div>

                    <p>
                        Total des comptes
                    </p>

                    <h2>
                        {{ $totalUtilisateurs }}
                    </h2>

                    <span>
                        Tous les comptes inscrits
                    </span>

                </div>

            </div>



            {{-- Utilisateurs --}}
            <div class="admin-stat-card">

                <div class="admin-icon green">

                    ✓

                </div>


                <div>

                    <p>
                        Utilisateurs
                    </p>

                    <h2>
                        {{ $totalComptesUtilisateurs }}
                    </h2>

                    <span>
                        Comptes utilisateurs
                    </span>

                </div>

            </div>



            {{-- Administrateurs --}}
            <div class="admin-stat-card">

                <div class="admin-icon red">

                    ★

                </div>


                <div>

                    <p>
                        Administrateurs
                    </p>

                    <h2>
                        {{ $totalAdmins }}
                    </h2>

                    <span>
                        Compte administrateur
                    </span>

                </div>

            </div>

        </section>



        {{-- ==========================================
             PARTIE BASSE DU DASHBOARD
        ========================================== --}}

        <section class="admin-grid">


            {{-- ==========================================
                 DERNIERS UTILISATEURS
            ========================================== --}}

            <div class="admin-panel">


                <div class="admin-panel-header">

                    <h2>
                        Derniers utilisateurs
                    </h2>

                    <a href="{{ route('admin.utilisateurs') }}">

                        Voir tous ›

                    </a>

                </div>



                @forelse($derniersUtilisateurs as $user)

                    <div class="admin-line">


                        <div class="admin-mini-icon blue">

                            👤

                        </div>


                        <div>

                            <h3>
                                {{ $user->name }}
                            </h3>

                            <p>
                                {{ $user->email }}
                            </p>

                        </div>


                        <span>

                            {{ $user->created_at
                                ? $user->created_at->format('d/m/Y')
                                : '-' }}

                        </span>

                    </div>


                @empty


                    <p>
                        Aucun utilisateur trouvé.
                    </p>


                @endforelse


            </div>



            {{-- ==========================================
                 ACTIVITÉ RÉCENTE
            ========================================== --}}

            <div class="admin-panel">


                <div class="admin-panel-header">

                    <h2>
                        Activité récente
                    </h2>

                </div>



                @forelse($activitesRecentes as $activite)


                    <div class="admin-line">


                        {{-- Icône différente selon le type d'activité --}}
                        <div class="admin-mini-icon
                            {{ $activite['type'] === 'message'
                                ? 'orange'
                                : 'blue' }}">

                            @if($activite['type'] === 'message')

                                ✉

                            @else

                                👤

                            @endif

                        </div>


                        <div>

                            <h3>

                                {{ $activite['titre'] }}

                            </h3>


                            <p>

                                {{ $activite['message'] }}

                            </p>

                        </div>


                        {{-- Date de l'activité --}}
                        <span>

                            {{ $activite['date']
                                ? $activite['date']->format('d/m/Y')
                                : '-' }}

                        </span>

                    </div>


                @empty


                    <p>
                        Aucune activité récente.
                    </p>


                @endforelse


            </div>

        </section>



        {{-- ==========================================
             PIED DE PAGE
        ========================================== --}}

        <footer class="admin-footer">

            <span>
                © 2026 BudgetFlow. Tous droits réservés.
            </span>

            <span>
                Version 1.0
            </span>

        </footer>


    </main>

</div>

</body>

</html>