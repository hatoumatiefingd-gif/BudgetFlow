<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier une dépense récurrente</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Modifier une dépense récurrente</h1>

        <form action="{{ route('depenses-recurrentes.update', $recurrente->idDepenseRecurrente) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Nom de la dépense</label>
            <input type="text" name="nomDepenseRecurrente" value="{{ $recurrente->nomDepenseRecurrente }}" required>

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" value="{{ $recurrente->montant }}" required>

            <label>Catégorie</label>
            <select name="idCategorie" required>
                @foreach($categories as $categorie)
                    <option value="{{ $categorie->idCategorie }}"
                        {{ $categorie->idCategorie == $recurrente->idCategorie ? 'selected' : '' }}>
                        {{ ucfirst($categorie->nomCategorie) }}
                    </option>
                @endforeach
            </select>

            <label>Fréquence</label>
            <select name="frequence" required>
                <option value="Mensuel" {{ $recurrente->frequence == 'Mensuel' ? 'selected' : '' }}>Mensuel</option>
                <option value="Hebdomadaire" {{ $recurrente->frequence == 'Hebdomadaire' ? 'selected' : '' }}>Hebdomadaire</option>
                <option value="Annuel" {{ $recurrente->frequence == 'Annuel' ? 'selected' : '' }}>Annuel</option>
            </select>

            <label>Prochaine date</label>
           <input
    type="date"
    name="prochaineDate"
    min="2026-07-01"
    required>
            <button type="submit">Enregistrer les modifications</button>
        </form>

        <a href="{{ route('depenses-recurrentes.index') }}" class="back-link">
            ← Retour
        </a>

    </div>
</div>

</body>
</html>