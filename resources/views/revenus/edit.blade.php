<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un revenu</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Modifier un revenu</h1>

        <form action="{{ route('revenus.update', $revenu->idRevenu) }}" method="POST">
            @csrf
            @method('PUT')

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" value="{{ $revenu->montant }}" required>

            <label>Source</label>
            <input type="text" name="source" value="{{ $revenu->source }}" required>

            <label>Date</label>
            <input
    type="date"
    name="dateRevenu"
    min="2026-07-01"
    required>

            <button type="submit">
                Enregistrer les modifications
            </button>
        </form>

        <a href="{{ route('revenus.index') }}" class="back-link">
            ← Retour aux revenus
        </a>

    </div>
</div>

</body>
</html>