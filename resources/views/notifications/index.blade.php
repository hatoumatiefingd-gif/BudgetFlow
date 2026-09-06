<!DOCTYPE html>
<html lang="fr">

<head>

    <meta charset="UTF-8">

    <title>
        Notifications - BudgetFlow
    </title>

    {{-- CSS principal de BudgetFlow --}}
    <link rel="stylesheet"
          href="{{ asset('css/style.css') }}">

</head>

<body>

<div class="app">


    {{-- ==========================================
         MENU LATÉRAL
    ========================================== --}}

    <aside class="sidebar">


        {{-- Logo --}}
        <div class="side-logo">

            <img src="{{ asset('logo.png') }}"
                 alt="BudgetFlow">

        </div>


        {{-- Navigation --}}
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

            <a href="{{ route('depenses-recurrentes.index') }}">
                ↔ Récurrentes
            </a>

            <a class="active"
               href="{{ route('notifications.index') }}">
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

    <main class="content notification-page">


        {{-- En-tête --}}
        <div class="d-header">

            <div>

                <h1>
                    Notifications
                </h1>

                <p>
                    Restez informé de l’évolution de votre budget
                </p>

            </div>

        </div>


        {{-- Message après suppression --}}
        @if(session('success'))

            <div class="notif-success-message">

                {{ session('success') }}

            </div>

        @endif



        {{-- ==========================================
             CATEGORIES
        ========================================== --}}

        <section class="notif-tabs">


            <div class="notif-tab red">

                Alertes

                <span>
                    ⚠
                </span>

            </div>


            <div class="notif-tab pink">

                Paiement

                <span>
                    ♢
                </span>

            </div>


            <div class="notif-tab green">

                Réussites

                <span>
                    ✓
                </span>

            </div>


            <div class="notif-tab cyan">

                Info

                <span>
                    i
                </span>

            </div>


        </section>



        {{-- ==========================================
             HISTORIQUE DES NOTIFICATIONS
        ========================================== --}}

        <div class="notif-scroll-area">

            <section class="notif-list">


                @forelse($notifications as $notification)


                    <div class="notif-card">


                        {{-- Icône selon le type --}}
                        @if($notification->type == 'Alerte')

                            <div class="notif-icon notif-red">
                                ⚠
                            </div>

                        @elseif($notification->type == 'Paiement')

                            <div class="notif-icon notif-pink">
                                ♢
                            </div>

                        @elseif($notification->type == 'Réussite')

                            <div class="notif-icon notif-green">
                                ✓
                            </div>

                        @else

                            <div class="notif-icon notif-cyan">
                                i
                            </div>

                        @endif



                        {{-- Contenu --}}
                        <div class="notif-content">

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



                        {{-- Suppression --}}
                        <form
                            method="POST"
                            action="{{ route(
                                'notifications.destroy',
                                $notification->idNotification
                            ) }}"
                            class="notif-delete-form"
                            onsubmit="return confirm(
                                'Voulez-vous supprimer cette notification ?'
                            );"
                        >

                            @csrf

                            @method('DELETE')


                            <button
                                type="submit"
                                class="notif-delete-btn"
                                title="Supprimer"
                            >
                                ✕
                            </button>

                        </form>


                    </div>


                @empty


                    <div class="notif-card">

                        <div class="notif-icon notif-cyan">
                            i
                        </div>

                        <div class="notif-content">

                            <h3>
                                Aucune notification
                            </h3>

                            <p>
                                Aucune alerte pour le moment.
                            </p>

                        </div>

                    </div>


                @endforelse


            </section>

        </div>


    </main>

</div>

</body>

</html>