{{--
    Affiche les erreurs de validation d'un formulaire.

    Quand $request->validate() refuse une saisie, Laravel revient
    sur le formulaire et place les messages dans $errors.
    Ce bloc les affiche pour que l'utilisateur sache quoi corriger.
--}}
@if ($errors->any())
    <div class="message-erreur">
        <ul>
            @foreach ($errors->all() as $erreur)
                <li>{{ $erreur }}</li>
            @endforeach
        </ul>
    </div>
@endif
