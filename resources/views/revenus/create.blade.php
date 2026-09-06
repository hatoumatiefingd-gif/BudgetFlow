<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un revenu</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Ajouter un revenu</h1>

        <form action="{{ route('revenus.store') }}" method="POST">
            @csrf

            <label>Montant</label>
            <input type="number" step="0.01" name="montant" required>

            <label>Source</label>
            <input type="text" name="source" placeholder="Exemple : Salaire" required>

            <label>Date</label>
            <input type="date" name="dateRevenu" required>

            <button type="submit">Enregistrer</button>
        </form>

        <a href="{{ route('revenus.index') }}" class="back-link">
            ← Retour aux revenus
        </a>

    </div>
</div>

</body>
</html>