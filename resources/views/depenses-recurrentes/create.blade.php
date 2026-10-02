<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une dépense récurrente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Ajouter une dépense récurrente</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Formulaire envoyé à DepenseRecurrenteController@store --}}
        <form action="{{ route('depenses-recurrentes.store') }}" method="POST">
            {{-- Jeton de sécurité obligatoire pour les formulaires POST (protection CSRF) --}}
            @csrf

            <label>Nom de la dépense</label>
            <input type="text" name="nomDepenseRecurrente" placeholder="Exemple : Netflix" required>

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" placeholder="Exemple : 13.49" required>

            <label>Catégorie</label>
            <select name="idCategorie" required>
                {{-- Une option par catégorie, envoyées par le contrôleur --}}
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->idCategorie }}">
                        {{ ucfirst($categorie->nomCategorie) }}
                    </option>
                @endforeach
            </select>

            <label>Fréquence</label>
            {{-- Les trois fréquences acceptées par le contrôleur --}}
            <select name="frequence" required>
                <option value="Mensuel">Mensuel</option>
                <option value="Hebdomadaire">Hebdomadaire</option>
                <option value="Annuel">Annuel</option>
            </select>

            <label>Prochaine date</label>
            <input type="date" name="prochaineDate" required>

            <button type="submit">Enregistrer</button>
        </form>

        <a href="{{ route('depenses-recurrentes.index') }}" class="back-link">
            ← Retour
        </a>

    </div>
</div>

</body>
</html>