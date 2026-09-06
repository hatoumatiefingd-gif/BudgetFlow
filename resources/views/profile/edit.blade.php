<x-app-layout>

   <x-slot name="header">

    {{-- En-tête simple et moderne de la page profil --}}
    <div class="profile-clean-header">

        <div>
            <h1>Mon profil</h1>

            <p>
                Gérez vos informations personnelles et la sécurité de votre compte.
            </p>
        </div>

        <a href="{{ route('dashboard') }}"
           class="profile-modern-back">
            ← Tableau de bord
        </a>

    </div>

</x-slot>


    <div class="profile-modern-page">

        <div class="profile-modern-grid">


            {{-- ==========================================
                 COLONNE GAUCHE : IDENTITÉ
            ========================================== --}}
            <aside class="profile-identity-card">

                <div class="profile-avatar">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>

                <h2>
                    {{ Auth::user()->name }}
                </h2>

                <p>
                    {{ Auth::user()->email }}
                </p>

                <span class="profile-account-badge">
                    Compte utilisateur
                </span>

                <div class="profile-identity-separator"></div>

                <div class="profile-identity-info">

                    <div>
                        <span>Nom</span>
                        <strong>{{ Auth::user()->name }}</strong>
                    </div>

                    <div>
                        <span>E-mail</span>
                        <strong>{{ Auth::user()->email }}</strong>
                    </div>

                </div>

            </aside>


            {{-- ==========================================
                 COLONNE DROITE
            ========================================== --}}
            <div class="profile-settings-column">


                {{-- Informations --}}
                <section class="profile-setting-card">

                    <div class="profile-setting-heading">

                        <div class="profile-setting-icon blue">
                            👤
                        </div>

                        <div>
                            <h2>Informations personnelles</h2>
                            <p>Modifiez votre nom et votre adresse e-mail.</p>
                        </div>

                    </div>

                    <div class="profile-setting-content">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                </section>


                {{-- Mot de passe --}}
                <section class="profile-setting-card">

                    <div class="profile-setting-heading">

                        <div class="profile-setting-icon purple">
                            🔐
                        </div>

                        <div>
                            <h2>Sécurité</h2>
                            <p>Modifiez votre mot de passe.</p>
                        </div>

                    </div>

                    <div class="profile-setting-content">
                        @include('profile.partials.update-password-form')
                    </div>

                </section>


                {{-- Suppression --}}
                <section class="profile-setting-card danger">

                    <div class="profile-setting-heading">

                        <div class="profile-setting-icon red">
                            !
                        </div>

                        <div>
                            <h2>Zone sensible</h2>
                            <p>Supprimez définitivement votre compte.</p>
                        </div>

                    </div>

                    <div class="profile-setting-content">
                        @include('profile.partials.delete-user-form')
                    </div>

                </section>

            </div>

        </div>

    </div>

</x-app-layout>