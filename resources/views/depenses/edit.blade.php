<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une dépense</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">

    <div class="form-card">

        <h1>Modifier une dépense</h1>

        <form action="{{ route('depenses.update', $depense->idDepense) }}" method="POST">

            @csrf
            @method('PUT')

            <label>Montant</label>
            <input type="number"
                   step="0.01"
                   name="montant"
                   value="{{ $depense->montant }}"
                   required>

            <label>Catégorie</label>
            <select name="idCategorie">

                @foreach($categories as $categorie)

                    <option value="{{ $categorie->idCategorie }}"
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