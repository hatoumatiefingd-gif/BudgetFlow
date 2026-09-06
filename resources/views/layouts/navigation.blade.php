<nav class="profile-nav">

    <div class="profile-nav-inner">

        {{-- Logo BudgetFlow --}}
        <a href="{{ route('dashboard') }}"
           class="profile-nav-logo">

            <img src="{{ asset('logo.png') }}"
                 alt="BudgetFlow">

        </a>


        {{-- Lien vers le tableau de bord --}}
        <a href="{{ route('dashboard') }}"
           class="profile-nav-dashboard">

            Tableau de bord

        </a>


        {{-- Partie droite : nom utilisateur --}}
        <div class="profile-nav-user">

            <span>
                {{ Auth::user()->name }}
            </span>

            {{-- Déconnexion --}}
            <form method="POST"
                  action="{{ route('logout') }}">

                @csrf

                <button type="submit">
                    Déconnexion
                </button>

            </form>

        </div>

    </div>

</nav>