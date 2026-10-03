<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une dépense</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<div class="form-page">

    <div class="form-card">

        <h1>Modifier une dépense</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Formulaire envoyé à DepenseController@update --}}
        <form action="{{ route('depenses.update', $depense->idDepense) }}" method="POST">

            @csrf
            {{-- Un formulaire HTML ne connaît que GET et POST : @method('PUT') indique à Laravel qu'il s'agit d'une modification --}}
            @method('PUT')

            <label>Montant</label>
            <input type="number"
                   step="0.01"
                   name="montant"
                   {{-- Les champs sont pré-remplis avec les valeurs actuelles de la dépense --}}
                   value="{{ $depense->montant }}"
                   required>

            <label>Catégorie</label>
            <select name="idCategorie">

                @foreach($categories as $categorie)

                    <option value="{{ $categorie->idCategorie }}"
                        {{-- La catégorie actuelle de la dépense est sélectionnée par défaut --}}
                        {{ $depense->idCategorie == $categorie->idCategorie ? 'selected' : '' }}>

                        {{ $categorie->nomCategorie }}

                    </option>

                @endforeach

            </select>

            <label>Description</label>
            <input type="text"
                   name="description"
                   value="{{ $depense->description }}"
                   required>

            <label>Date</label>
           
                  <input
    type="date"
    name="dateDepense"
    value="{{ $depense->dateDepense }}"
    min="2026-07-01"
    required>
            <button type="submit">
                Enregistrer les modifications
            </button>

        </form>

        <a href="{{ route('depenses.index') }}" class="back-link">
            ← Retour aux dépenses
        </a>

    </div>

</div>

</body>
</html>