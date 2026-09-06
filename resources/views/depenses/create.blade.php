<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une dépense</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">

    <div class="form-card">

        <h1>Ajouter une dépense</h1>

        <form action="{{ route('depenses.store') }}" method="POST">

            @csrf

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" required>

            <label>Catégorie</label>
            <select name="idCategorie" required>

                @foreach($categories as $categorie)

                    <option value="{{ $categorie->idCategorie }}">
                        {{ $categorie->nomCategorie }}
                    </option>

                @endforeach

            </select>

            <label>Description</label>
            <input type="text" name="description" required>

            <label>Date</label>
            <input
    type="date"
    name="dateDepense"
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