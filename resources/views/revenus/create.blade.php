<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un revenu</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Ajouter un revenu</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Formulaire envoyé à RevenuController@store --}}
        <form action="{{ route('revenus.store') }}" method="POST">
            {{-- Jeton de sécurité obligatoire pour les formulaires POST (protection CSRF) --}}
            @csrf

            <label>Montant</label>
            {{-- step="0.01" autorise les centimes, old() remet la valeur saisie après une erreur --}}
            <input type="number" step="0.01" name="montant" value="{{ old('montant') }}" required>

            <label>Source</label>
            <input type="text" name="source" value="{{ old('source') }}" placeholder="Exemple : Salaire" required>

            <label>Date</label>
            <input type="date" name="dateRevenu" value="{{ old('dateRevenu') }}" required>

            <button type="submit">Enregistrer</button>
        </form>

        <a href="{{ route('revenus.index') }}" class="back-link">
            ← Retour aux revenus
        </a>

    </div>
</div>

</body>
</html>