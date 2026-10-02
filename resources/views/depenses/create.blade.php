<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une dépense</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=3">
</head>
<body>

<div class="form-page">

    <div class="form-card">

        <h1>Ajouter une dépense</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Formulaire envoyé à DepenseController@store --}}
        <form action="{{ route('depenses.store') }}" method="POST">

            {{-- Jeton de sécurité obligatoire pour les formulaires POST (protection CSRF) --}}
            @csrf

            <label>Montant</label>
            {{-- step="0.01" autorise les centimes, old() remet la valeur saisie après une erreur --}}
            <input type="number" step="0.01" name="montant" value="{{ old('montant') }}" required>

            <label>Catégorie</label>
            <select name="idCategorie" required>

                {{-- Une option par catégorie, envoyées par le contrôleur --}}
                @foreach($categories as $categorie)

                    <option value="{{ $categorie->idCategorie }}"
                        {{ old('idCategorie') == $categorie->idCategorie ? 'selected' : '' }}>
                        {{ $categorie->nomCategorie }}
                    </option>

                @endforeach

            </select>

            <label>Description</label>
            <input type="text" name="description" value="{{ old('description') }}" required>

            <label>Date</label>
            {{-- min empêche de choisir une date avant juillet 2026 --}}
            <input
    type="date"
    name="dateDepense"
    value="{{ old('dateDepense') }}"
    min="2026-07-01"
    required>
            <button type="submit">
                Enregistrer
            </button>

        </form>

        <a href="{{ route('depenses.index') }}" class="back-link">
            ← Retour aux dépenses
        </a>

    </div>

</div>

</body>
</html>