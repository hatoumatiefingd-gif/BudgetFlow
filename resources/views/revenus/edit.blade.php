<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un revenu</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v=4">
</head>
<body>

<div class="form-page">
    <div class="form-card">

        <h1>Modifier un revenu</h1>

        {{-- Messages d'erreur si la saisie est refusée --}}
        @include('partials.erreurs')

        {{-- Formulaire envoyé à RevenuController@update --}}
        <form action="{{ route('revenus.update', $revenu->idRevenu) }}" method="POST">
            @csrf
            {{-- @method('PUT') : Laravel traite ce formulaire comme une modification --}}
            @method('PUT')

            <label>Montant</label>
            {{-- Les champs sont pré-remplis avec les valeurs actuelles du revenu --}}
            <input type="number" step="0.01" name="montant" value="{{ $revenu->montant }}" required>

            <label>Source</label>
            <input type="text" name="source" value="{{ $revenu->source }}" required>

            <label>Date</label>
            <input
    type="date"
    name="dateRevenu"
    value="{{ $revenu->dateRevenu }}"
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